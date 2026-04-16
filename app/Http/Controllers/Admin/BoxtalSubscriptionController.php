<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gestion des souscriptions webhook Boxtal v3 depuis l'admin.
 *
 * Nécessaire car les appels API sortants depuis artisan/CLI
 * sont bloqués sur OVH mutualisé (seul PHP-FPM a accès réseau).
 */
class BoxtalSubscriptionController extends Controller
{
    private function baseUrl(): string
    {
        return rtrim(config('shipping.boxtal.v3_base_url', 'https://api.boxtal.com'), '/');
    }

    private function auth(): string
    {
        return base64_encode(
            config('shipping.boxtal.v3_access_key').':'.config('shipping.boxtal.v3_secret_key')
        );
    }

    public function index()
    {
        $subscriptions = [];
        $error = null;

        if (config('shipping.boxtal.v3_access_key')) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '.$this->auth(),
                    'Accept' => 'application/json',
                ])->get($this->baseUrl().'/shipping/v3.1/subscription');

                if ($response->successful()) {
                    $subscriptions = $response->json('content') ?? $response->json() ?? [];
                } else {
                    $error = 'Erreur API : '.$response->status();
                }
            } catch (\Throwable $e) {
                $error = 'Connexion impossible : '.$e->getMessage();
            }
        } else {
            $error = 'BOXTAL_V3_ACCESS_KEY non configurée.';
        }

        return view('admin.boxtal-subscriptions.index', compact('subscriptions', 'error'));
    }

    public function store(Request $request)
    {
        $callbackUrl = $request->input('callback_url', url('/api/boxtal/webhook'));
        $webhookSecret = $request->input('webhook_secret', bin2hex(random_bytes(32)));
        $results = [];

        $eventTypes = $request->input('event_types')
            ? explode(',', $request->input('event_types'))
            : ['DOCUMENT_CREATED', 'TRACKING_CHANGED'];

        foreach ($eventTypes as $eventType) {
            $eventType = trim($eventType);
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '.$this->auth(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post($this->baseUrl().'/shipping/v3.1/subscription', [
                    'eventType' => $eventType,
                    'callbackUrl' => $callbackUrl,
                    'webhookSecret' => $webhookSecret,
                ]);

                if ($response->successful()) {
                    // Sauvegarder le secret en base pour vérification des webhooks
                    Setting::updateOrCreate(
                        ['key' => 'boxtal_v3_webhook_secret'],
                        ['value' => $webhookSecret]
                    );

                    $results[] = [
                        'eventType' => $eventType,
                        'success' => true,
                        'webhookSecret' => $webhookSecret,
                    ];

                    Log::info("BoxtalSubscription: souscription créée pour {$eventType}", [
                        'callback_url' => $callbackUrl,
                    ]);
                } else {
                    $results[] = [
                        'eventType' => $eventType,
                        'success' => false,
                        'error' => $response->status().' — '.$response->body(),
                    ];
                }
            } catch (\Throwable $e) {
                $results[] = [
                    'eventType' => $eventType,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return redirect()->route('admin.boxtal-subscriptions.index')
            ->with('results', $results);
    }

    /**
     * Envoie un vrai webhook de test au endpoint /api/boxtal/webhook.
     * Simule exactement ce que Boxtal enverrait.
     */
    public function test(Request $request)
    {
        $orderNumber = $request->input('order_number');

        $order = \App\Models\Order::where('number', $orderNumber)->first();

        if (! $order) {
            return redirect()->route('admin.boxtal-subscriptions.index')
                ->with('error', "Commande {$orderNumber} introuvable.");
        }

        $secret = config('shipping.boxtal.v3_webhook_secret')
            ?: Setting::where('key', 'boxtal_v3_webhook_secret')->value('value');

        $payload = [
            'eventType' => 'TRACKING_CHANGED',
            'shippingOrderId' => $order->boxtal_shipping_order_id ?? 'TEST-'.$order->id,
            'orderNumber' => $order->number,
            'test' => true,
        ];

        $jsonPayload = json_encode($payload);
        $signature = $secret ? hash_hmac('sha256', $jsonPayload, $secret) : '';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-bxt-signature' => $signature,
            ])->withBody($jsonPayload, 'application/json')
                ->post(url('/api/boxtal/webhook'));

            return redirect()->route('admin.boxtal-subscriptions.index')
                ->with('test_result', [
                    'status' => $response->status(),
                    'body' => "Webhook appelé pour #{$order->number} → HTTP {$response->status()} — "
                        .($response->json('message') ?? $response->body()),
                ]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.boxtal-subscriptions.index')
                ->with('error', 'Test échoué : '.$e->getMessage());
        }
    }

    /**
     * Liste les offres d'expédition disponibles pour une destination.
     */
    public function offers(Request $request)
    {
        $params = [
            'fromCountry' => $request->input('from_country', 'FR'),
            'fromPostalCode' => $request->input('from_postal', '14270'),
            'toCountry' => $request->input('to_country', 'BE'),
            'toPostalCode' => $request->input('to_postal', '7030'),
            'weight' => $request->input('weight', '0.5'),
            'length' => $request->input('length', '25'),
            'width' => $request->input('width', '20'),
            'height' => $request->input('height', '10'),
        ];

        try {
            $endpoints = [
                '/shipping/v3.1/shipping-offer',
                '/shipping/v3.1/shipping-offers',
                '/shipping/v3.1/offer',
                '/v3.1/shipping-offer',
            ];

            $results = [];
            foreach ($endpoints as $ep) {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '.$this->auth(),
                    'Accept' => 'application/json',
                ])->get($this->baseUrl().$ep, $params);

                $results[$ep] = [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ];

                if ($response->successful()) {
                    return response()->json([
                        'endpoint' => $ep,
                        'params' => $params,
                        'offers' => $response->json(),
                    ]);
                }
            }

            return response()->json([
                'params' => $params,
                'tried' => $results,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$this->auth(),
            ])->delete($this->baseUrl().'/shipping/v3.1/subscription/'.$id);

            if ($response->successful()) {
                return redirect()->route('admin.boxtal-subscriptions.index')
                    ->with('success', "Souscription {$id} supprimée.");
            }

            return redirect()->route('admin.boxtal-subscriptions.index')
                ->with('error', 'Erreur : '.$response->status().' — '.$response->body());
        } catch (\Throwable $e) {
            return redirect()->route('admin.boxtal-subscriptions.index')
                ->with('error', 'Erreur : '.$e->getMessage());
        }
    }
}
