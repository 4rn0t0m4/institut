<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Crée des expéditions via l'API Boxtal v3.
 *
 * Doc : https://developer.boxtal.com/fr/fr/apiv3/documentation
 * Endpoint : POST /shipping/v3.1/shipping-order
 */
class BoxtalShippingService
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

    /**
     * Crée une expédition Boxtal pour la commande.
     *
     * @return array{success: bool, shipping_order_id: ?string, error: ?string}
     */
    public function createShipment(Order $order, array $overrides = []): array
    {
        if (! config('shipping.boxtal.v3_access_key')) {
            return ['success' => false, 'shipping_order_id' => null, 'error' => 'BOXTAL_V3_ACCESS_KEY non configuré.'];
        }

        $payload = $this->buildPayload($order, $overrides);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$this->auth(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl().'/shipping/v3.1/shipping-order', $payload);

            if ($response->successful()) {
                $shippingOrderId = $response->json('content.id');

                Log::info("BoxtalShipping: expédition créée pour commande #{$order->number}", [
                    'shipping_order_id' => $shippingOrderId,
                ]);

                return ['success' => true, 'shipping_order_id' => $shippingOrderId, 'error' => null];
            }

            $errorBody = $response->json();
            $errorMsg = $this->formatApiError($errorBody, $response->status());

            Log::error("BoxtalShipping: échec création expédition #{$order->number}", [
                'status' => $response->status(),
                'body' => $errorBody,
            ]);

            return ['success' => false, 'shipping_order_id' => null, 'error' => $errorMsg];
        } catch (\Throwable $e) {
            Log::error("BoxtalShipping: exception pour commande #{$order->number}", [
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'shipping_order_id' => null, 'error' => $e->getMessage()];
        }
    }

    private function buildPayload(Order $order, array $overrides): array
    {
        $from = config('shipping.boxtal.from_address');
        $pkg = config('shipping.boxtal.default_package');
        $contentCategoryId = config('shipping.boxtal.content_category_id');

        // Déterminer le code offre d'expédition
        $offerCode = $overrides['shippingOfferCode']
            ?? $this->resolveOfferCode($order);

        $payload = [
            'shippingOfferCode' => $offerCode,
            'labelType' => $overrides['labelType'] ?? 'PDF_A4',
            'shipment' => [
                'externalId' => $order->number,
                'content' => [
                    'id' => $contentCategoryId,
                    'description' => 'Cosmétiques et soins',
                ],
                'fromAddress' => [
                    'type' => 'BUSINESS',
                    'contact' => [
                        'company' => $from['company'],
                        'firstName' => $from['firstName'],
                        'lastName' => $from['lastName'],
                        'email' => $from['email'],
                        'phone' => $from['phone'],
                    ],
                    'location' => [
                        'street' => $from['street'],
                        'city' => $from['city'],
                        'postalCode' => $from['postalCode'],
                        'countryIsoCode' => $from['country'],
                    ],
                ],
                'toAddress' => [
                    'type' => 'RESIDENTIAL',
                    'contact' => [
                        'firstName' => $order->shipping_first_name ?: $order->billing_first_name,
                        'lastName' => $order->shipping_last_name ?: $order->billing_last_name,
                        'email' => $order->billing_email,
                        'phone' => $order->billing_phone ?? '',
                    ],
                    'location' => [
                        'street' => trim(($order->shipping_address_1 ?: $order->billing_address_1).' '.($order->shipping_address_2 ?: $order->billing_address_2 ?? '')),
                        'city' => $order->shipping_city ?: $order->billing_city,
                        'postalCode' => $order->shipping_postcode ?: $order->billing_postcode,
                        'countryIsoCode' => $order->shipping_country ?: $order->billing_country ?: 'FR',
                    ],
                ],
                'packages' => [
                    [
                        'weight' => (float) ($overrides['weight'] ?? $pkg['weight']),
                        'length' => (int) ($overrides['length'] ?? $pkg['length']),
                        'width' => (int) ($overrides['width'] ?? $pkg['width']),
                        'height' => (int) ($overrides['height'] ?? $pkg['height']),
                        'value' => [
                            'value' => (float) $order->total,
                            'currency' => 'EUR',
                        ],
                        'content' => [
                            'id' => $contentCategoryId,
                            'description' => 'Cosmétiques et soins',
                        ],
                    ],
                ],
            ],
        ];

        // Point relais : pickupPointCode
        if ($order->relay_point_code) {
            $payload['shipment']['pickupPointCode'] = $order->relay_point_code;
        }

        return $payload;
    }

    /**
     * Résout le code offre à partir de la méthode d'expédition de la commande.
     */
    private function resolveOfferCode(Order $order): string
    {
        $offers = config('shipping.boxtal.shipping_offer_codes');

        // Point relais : utiliser le réseau
        if ($order->relay_network && isset($offers[$order->relay_network])) {
            return $offers[$order->relay_network];
        }

        // Colissimo
        if ($order->shipping_key === 'colissimo' && isset($offers['colissimo'])) {
            return $offers['colissimo'];
        }

        // Par défaut : Mondial Relay
        return $offers['MONR_NETWORK'] ?? 'MONR-CpourToi';
    }

    private function formatApiError(?array $body, int $status): string
    {
        if (! $body || ! isset($body['errors'])) {
            return "Erreur API Boxtal (HTTP {$status})";
        }

        $messages = [];
        foreach ($body['errors'] as $error) {
            $msg = $error['code'] ?? 'Unknown';
            if (isset($error['parameters'])) {
                foreach ($error['parameters'] as $param) {
                    $msg .= ' — '.($param['field'] ?? '').': '.($param['code'] ?? '');
                }
            }
            $messages[] = $msg;
        }

        return implode('; ', $messages);
    }
}
