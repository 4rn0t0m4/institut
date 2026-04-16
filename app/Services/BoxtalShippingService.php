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

        // Colissimo géré manuellement, pas via Boxtal
        if ($order->shipping_key === 'colissimo') {
            Log::info("BoxtalShipping: commande #{$order->number} Colissimo, push ignoré (gestion manuelle).");

            return ['success' => false, 'shipping_order_id' => null, 'error' => null];
        }

        $payload = $this->buildPayload($order, $overrides);

        Log::debug("BoxtalShipping: payload commande #{$order->number}", $payload);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$this->auth(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl().'/shipping/v3.1/shipping-order', $payload);

            if ($response->successful()) {
                $data = $response->json('content') ?? $response->json();
                $shippingOrderId = $data['id'] ?? null;

                Log::info("BoxtalShipping: expédition créée pour commande #{$order->number}", [
                    'shipping_order_id' => $shippingOrderId,
                    'response' => $data,
                ]);

                // Chercher l'URL de l'étiquette dans la réponse
                $labelUrl = $data['documents'][0]['url']
                    ?? $data['labelUrl']
                    ?? $data['label']['url']
                    ?? null;

                return ['success' => true, 'shipping_order_id' => $shippingOrderId, 'label_url' => $labelUrl, 'error' => null];
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
                        'postalCode' => preg_replace('/\s+/', '', $order->shipping_postcode ?: $order->billing_postcode),
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
        $country = $order->shipping_country ?: $order->billing_country ?: 'FR';
        $isInternational = $country !== 'FR';

        // Point relais
        if ($order->relay_network) {
            if ($isInternational) {
                $euroKey = $order->relay_network.'_EUROPE';
                if (isset($offers[$euroKey])) {
                    return $offers[$euroKey];
                }
            }
            if (isset($offers[$order->relay_network])) {
                return $offers[$order->relay_network];
            }
        }

        // Colissimo
        if ($order->shipping_key === 'colissimo') {
            if ($isInternational && isset($offers['colissimo_international'])) {
                return $offers['colissimo_international'];
            }
            if (isset($offers['colissimo'])) {
                return $offers['colissimo'];
            }
        }

        // Par défaut : Mondial Relay
        if ($isInternational) {
            return $offers['MONR_NETWORK_EUROPE'] ?? 'MONR-CpourToiEurope';
        }

        return $offers['MONR_NETWORK'] ?? 'MONR-CpourToi';
    }

    /**
     * Récupère l'URL de l'étiquette d'expédition.
     */
    public function fetchLabelUrl(string $shippingOrderId): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$this->auth(),
                'Accept' => 'application/json',
            ])->get($this->baseUrl().'/shipping/v3.1/shipping-order/'.$shippingOrderId.'/document');

            if ($response->successful()) {
                $data = $response->json('content') ?? $response->json();

                Log::debug("BoxtalShipping: documents pour {$shippingOrderId}", $data);

                // Chercher le premier document PDF (étiquette)
                if (is_array($data)) {
                    foreach ($data as $doc) {
                        if (isset($doc['url'])) {
                            return $doc['url'];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("BoxtalShipping: exception récupération étiquette {$shippingOrderId}", [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Récupère les infos de tracking depuis l'API Boxtal v3.
     *
     * @param  string  $shippingOrderId  L'ID Boxtal de la commande d'expédition
     * @return array{tracking_number: ?string, tracking_url: ?string, events: array}
     */
    public function fetchTrackingV3(string $shippingOrderId): array
    {
        if (! config('shipping.boxtal.v3_access_key')) {
            Log::warning('BoxtalShipping: impossible de récupérer le tracking — clés v3 non configurées');

            return ['tracking_number' => null, 'tracking_url' => null, 'events' => []];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$this->auth(),
                'Accept' => 'application/json',
            ])->get($this->baseUrl().'/shipping/v3.1/shipping-order/'.$shippingOrderId.'/tracking');

            if (! $response->successful()) {
                Log::warning("BoxtalShipping: échec récupération tracking pour shipping_order={$shippingOrderId}", [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return ['tracking_number' => null, 'tracking_url' => null, 'events' => []];
            }

            $data = $response->json();
            $parcel = $data['parcels'][0] ?? $data['content'][0] ?? null;

            // Extraire le numéro de suivi et l'URL selon la structure de la réponse
            $trackingNumber = $parcel['trackingNumber'] ?? $parcel['reference'] ?? $data['trackingNumber'] ?? null;
            $trackingUrl = $parcel['trackingUrl'] ?? $data['trackingUrl'] ?? null;
            $events = $parcel['events'] ?? $data['events'] ?? [];

            return [
                'tracking_number' => $trackingNumber,
                'tracking_url' => $trackingUrl,
                'events' => $events,
            ];
        } catch (\Throwable $e) {
            Log::error("BoxtalShipping: exception récupération tracking v3 {$shippingOrderId}", [
                'error' => $e->getMessage(),
            ]);

            return ['tracking_number' => null, 'tracking_url' => null, 'events' => []];
        }
    }

    /**
     * Déduit le nom du transporteur à partir des infos de la commande.
     */
    public static function carrierName(Order $order): ?string
    {
        return match (true) {
            $order->relay_network === 'MONR_NETWORK' => 'Mondial Relay',
            $order->relay_network === 'CHRP_NETWORK' => 'Chronopost',
            $order->shipping_key === 'colissimo' => 'Colissimo',
            default => null,
        };
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
