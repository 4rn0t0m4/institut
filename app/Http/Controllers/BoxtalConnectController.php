<?php

namespace App\Http\Controllers;

use App\Mail\OrderShipped;
use App\Models\Order;
use App\Services\BoxtalAuthService;
use App\Services\BoxtalShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Endpoints REST compatibles Boxtal Connect.
 *
 * Reproduit le comportement du plugin WooCommerce Boxtal Connect
 * pour permettre la synchronisation des commandes vers le dashboard Boxtal.
 *
 * Routes :
 *   POST /boxtal-connect/v1/shop/pair          — pairing initial
 *   POST /boxtal-connect/v1/order              — liste des commandes
 *   POST /boxtal-connect/v1/order/{id}/shipped  — marquer expédié
 *   POST /boxtal-connect/v1/order/{id}/delivered — marquer livré
 */
class BoxtalConnectController extends Controller
{
    public function __construct(
        private BoxtalAuthService $auth,
        private BoxtalShippingService $shipping,
    ) {}

    /**
     * Pairing : Boxtal envoie les credentials pour connecter la boutique.
     */
    public function pair(Request $request): JsonResponse
    {
        Log::info('BoxtalConnect: pairing request reçu', ['uri' => $request->getRequestUri()]);

        $decrypted = $this->auth->authenticate($request->getContent());

        if (! $decrypted) {
            Log::warning('BoxtalConnect: pairing — déchiffrement échoué');

            return response()->json(['message' => 'Authentication failed'], 401);
        }

        Log::info('BoxtalConnect: pairing — body déchiffré', ['keys' => array_keys((array) $decrypted)]);

        if (! isset($decrypted->accessKey, $decrypted->secretKey)) {
            return response()->json(['message' => 'Missing credentials'], 400);
        }

        // Stocker les credentials dans le .env n'est pas idéal en prod
        // On les stocke dans la table settings pour pouvoir les mettre à jour
        $this->storeConnectCredentials($decrypted->accessKey, $decrypted->secretKey);

        Log::info('BoxtalConnect: pairing réussi');

        return response()->json([
            'pluginConfigurationUrl' => url('/admin'),
        ]);
    }

    /**
     * Retourne les commandes en cours pour synchronisation.
     */
    public function retrieveOrders(Request $request): JsonResponse
    {
        $decrypted = $this->auth->authenticateWithAccessKey($request->getContent());

        if (! $decrypted) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $orders = Order::with('items')
            ->whereIn('status', ['processing'])
            ->orderByDesc('created_at')
            ->get();

        $locale = app()->getLocale();
        $statusLabels = [
            'processing' => 'En cours de traitement',
            'completed' => 'Terminée',
            'shipped' => 'Expédiée',
        ];

        $response = $orders->map(function (Order $order) use ($locale, $statusLabels) {
            return [
                'internalReference' => (string) $order->id,
                'reference' => $order->number,
                'status' => [
                    'key' => 'wc-'.$order->status,
                    'translations' => [
                        $locale => $statusLabels[$order->status] ?? $order->status,
                    ],
                ],
                'shippingMethod' => [
                    'key' => $order->shipping_key ?? 'flat_rate',
                    'translations' => [
                        $locale => $order->shipping_method ?? 'Livraison',
                    ],
                ],
                'shippingAmount' => (float) $order->shipping_total,
                'creationDate' => $order->created_at->toIso8601String(),
                'orderAmount' => (float) $order->total,
                'recipient' => [
                    'firstname' => $order->shipping_first_name ?: $order->billing_first_name,
                    'lastname' => $order->shipping_last_name ?: $order->billing_last_name,
                    'company' => null,
                    'addressLine1' => $order->shipping_address_1 ?: $order->billing_address_1,
                    'addressLine2' => $order->shipping_address_2 ?: $order->billing_address_2,
                    'city' => $order->shipping_city ?: $order->billing_city,
                    'state' => null,
                    'postcode' => $order->shipping_postcode ?: $order->billing_postcode,
                    'country' => $order->shipping_country ?: $order->billing_country ?: 'FR',
                    'phone' => $order->billing_phone,
                    'email' => $order->billing_email,
                ],
                'products' => $order->items->map(function ($item) use ($locale) {
                    return [
                        'weight' => null,
                        'quantity' => (int) $item->quantity,
                        'price' => (float) $item->unit_price,
                        'description' => [
                            $locale => $item->product_name,
                        ],
                    ];
                })->values()->all(),
                'parcelPoint' => $order->relay_point_code ? [
                    'code' => $order->relay_point_code,
                    'network' => $order->relay_network ?? 'MONR_NETWORK',
                ] : null,
            ];
        });

        Log::info('BoxtalConnect: sync retourne '.count($response).' commandes');

        return response()->json(['orders' => $response->values()->all()]);
    }

    /**
     * Boxtal signale qu'une commande a été expédiée.
     *
     * Récupère le tracking depuis l'API Boxtal, met à jour la commande,
     * puis envoie l'email d'expédition au client.
     */
    public function orderShipped(Request $request, int $orderId): JsonResponse
    {
        $decrypted = $this->auth->authenticateWithAccessKey($request->getContent());

        if (! $decrypted) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order = Order::find($orderId);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Récupérer le tracking depuis l'API Boxtal v3 si possible
        $trackingNumber = $order->tracking_number;
        if ($order->boxtal_shipping_order_id) {
            $tracking = $this->shipping->fetchTrackingV3($order->boxtal_shipping_order_id);
            $trackingNumber = $tracking['tracking_number'] ?? $trackingNumber;
        }
        $carrier = BoxtalShippingService::carrierName($order);

        $order->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $trackingNumber,
            'tracking_carrier' => $carrier ?? $order->tracking_carrier,
        ]);

        Log::info("BoxtalConnect: commande #{$order->number} marquée expédiée", [
            'tracking_number' => $order->tracking_number,
            'tracking_carrier' => $order->tracking_carrier,
        ]);

        // Envoyer l'email d'expédition au client
        try {
            $order->load('items');
            Mail::to($order->billing_email)->send(new OrderShipped($order));
            Log::info("BoxtalConnect: email d'expédition envoyé pour commande #{$order->number}");
        } catch (\Throwable $e) {
            Log::error("BoxtalConnect: échec envoi email expédition #{$order->number}", [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(null, 200);
    }

    /**
     * Boxtal signale qu'une commande a été livrée.
     */
    public function orderDelivered(Request $request, int $orderId): JsonResponse
    {
        $decrypted = $this->auth->authenticateWithAccessKey($request->getContent());

        if (! $decrypted) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $order = Order::find($orderId);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update(['status' => 'completed']);

        Log::info("BoxtalConnect: commande #{$order->number} marquée livrée");

        return response()->json(null, 200);
    }

    /**
     * Stocke les credentials Boxtal Connect en base via le modèle Setting.
     */
    private function storeConnectCredentials(string $accessKey, string $secretKey): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'boxtal_connect_access_key'],
            ['value' => $accessKey]
        );
        \App\Models\Setting::updateOrCreate(
            ['key' => 'boxtal_connect_secret_key'],
            ['value' => $secretKey]
        );

        // Mettre aussi en config pour la session courante
        config(['shipping.boxtal.connect_access_key' => $accessKey]);
        config(['shipping.boxtal.connect_secret_key' => $secretKey]);
    }
}
