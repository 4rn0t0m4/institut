<?php

namespace App\Http\Controllers;

use App\Mail\OrderShipped;
use App\Models\Order;
use App\Services\BoxtalShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Reçoit les webhooks de l'API Boxtal v3 (souscriptions).
 *
 * Événements gérés :
 * - Document d'expédition créé (ignoré pour l'instant)
 * - Suivi d'expédition modifié → mise à jour commande + email client
 *
 * Route : POST /api/boxtal/webhook
 */
class BoxtalWebhookController extends Controller
{
    public function __construct(private BoxtalShippingService $shipping) {}

    public function handle(Request $request): JsonResponse
    {
        // Vérifier la signature HMAC SHA256
        $secret = config('shipping.boxtal.v3_webhook_secret');

        if ($secret && ! $this->verifySignature($request, $secret)) {
            Log::warning('BoxtalWebhook: signature invalide');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $payload = $request->all();

        Log::info('BoxtalWebhook: événement reçu', [
            'type' => $payload['eventType'] ?? 'unknown',
            'shipping_order_id' => $payload['shippingOrderId'] ?? null,
        ]);

        $eventType = $payload['eventType'] ?? null;

        return match ($eventType) {
            'TRACKING_CHANGED', 'tracking_changed' => $this->handleTrackingUpdate($payload),
            'DOCUMENT_CREATED', 'document_created' => $this->handleShippingDocument($payload),
            default => $this->handleUnknownEvent($payload),
        };
    }

    private function handleTrackingUpdate(array $payload): JsonResponse
    {
        $shippingOrderId = $payload['shippingOrderId'] ?? null;

        if (! $shippingOrderId) {
            Log::warning('BoxtalWebhook: tracking_update sans shippingOrderId');

            return response()->json(['message' => 'Missing shippingOrderId'], 200);
        }

        // Trouver la commande par son boxtal_shipping_order_id
        $order = Order::where('boxtal_shipping_order_id', $shippingOrderId)->first();

        if (! $order) {
            Log::warning("BoxtalWebhook: commande introuvable pour shipping_order_id={$shippingOrderId}");

            return response()->json(['message' => 'Order not found'], 200);
        }

        // Récupérer le tracking détaillé via l'API v3
        $tracking = $this->shipping->fetchTrackingV3($shippingOrderId);

        if (! $tracking['tracking_number']) {
            Log::info("BoxtalWebhook: pas encore de tracking pour commande #{$order->number}");

            return response()->json(['message' => 'No tracking yet'], 200);
        }

        $carrier = BoxtalShippingService::carrierName($order);
        $wasAlreadyShipped = $order->status === 'shipped';
        $trackingChanged = $tracking['tracking_number'] !== $order->tracking_number;

        $order->update([
            'status' => 'shipped',
            'shipped_at' => $order->shipped_at ?? now(),
            'tracking_number' => $tracking['tracking_number'],
            'tracking_carrier' => $carrier ?? $order->tracking_carrier,
        ]);

        Log::info("BoxtalWebhook: commande #{$order->number} tracking mis à jour", [
            'tracking_number' => $tracking['tracking_number'],
            'carrier' => $carrier,
        ]);

        // Envoyer l'email uniquement la première fois (passage à "shipped")
        if (! $wasAlreadyShipped || ($trackingChanged && ! $order->shipped_at)) {
            try {
                $order->load('items');
                Mail::to($order->billing_email)->send(new OrderShipped($order));
                Log::info("BoxtalWebhook: email d'expédition envoyé pour commande #{$order->number}");
            } catch (\Throwable $e) {
                Log::error("BoxtalWebhook: échec envoi email #{$order->number}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['message' => 'OK'], 200);
    }

    private function handleShippingDocument(array $payload): JsonResponse
    {
        Log::info('BoxtalWebhook: document d\'expédition reçu', [
            'shipping_order_id' => $payload['shippingOrderId'] ?? null,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }

    private function handleUnknownEvent(array $payload): JsonResponse
    {
        Log::info('BoxtalWebhook: événement inconnu', ['payload' => $payload]);

        return response()->json(['message' => 'OK'], 200);
    }

    private function verifySignature(Request $request, string $secret): bool
    {
        $signature = $request->header('x-bxt-signature');

        if (! $signature) {
            return false;
        }

        $computed = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($computed, $signature);
    }
}
