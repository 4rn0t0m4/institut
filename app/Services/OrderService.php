<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CartService $cart,
    ) {}

    /**
     * Vérifie stock et prix actuels pour tous les articles du panier.
     *
     * @return array{ok: bool, errors: string[], cartUpdated: bool}
     */
    public function validateCartStock(array $items): array
    {
        $errors = [];
        $cartUpdated = false;

        foreach ($items as $key => $item) {
            $product = $item['product'];

            if (! $product || ! $product->is_active) {
                $this->cart->remove($key);
                $errors[] = "{$item['name']} n'est plus disponible.";
                continue;
            }

            if (abs($product->currentPrice() - $item['price']) > 0.01) {
                $this->cart->updatePrice($key, $product->currentPrice());
                $cartUpdated = true;
            }

            if ($product->manage_stock && $product->stock_quantity < $item['quantity']) {
                if ($product->stock_quantity <= 0) {
                    $this->cart->remove($key);
                    $errors[] = "{$item['name']} est en rupture de stock.";
                } else {
                    $this->cart->update($key, $product->stock_quantity);
                    $errors[] = "{$item['name']} : quantité réduite à {$product->stock_quantity} (stock insuffisant).";
                }
            }
        }

        return [
            'ok'          => empty($errors) && ! $cartUpdated,
            'errors'      => $errors,
            'cartUpdated' => $cartUpdated,
        ];
    }

    /**
     * Calcule le coût de livraison pour une méthode et un sous-total donnés.
     */
    public function calculateShipping(string $shippingKey, float $subtotal): float
    {
        $cost = (float) config("shipping.methods.{$shippingKey}.price", 0);
        $threshold = config('shipping.free_shipping_threshold');

        if ($threshold && config("shipping.methods.{$shippingKey}.free_above_threshold") && $subtotal >= $threshold) {
            $cost = 0;
        }

        return $cost;
    }

    /**
     * Construit la note client en préfixant les infos point relais si applicable.
     */
    public function buildCustomerNote(?string $note, string $shippingKey, ?string $relayName, ?string $relayAddress): ?string
    {
        $customerNote = $note ?? '';

        if ($shippingKey === 'boxtal' && $relayName) {
            $relayInfo = "Point relais : {$relayName}";
            if ($relayAddress) {
                $relayInfo .= " — {$relayAddress}";
            }
            $customerNote = $relayInfo . ($customerNote ? "\n\n" . $customerNote : '');
        }

        return $customerNote ?: null;
    }

    /**
     * Crée la commande et ses lignes dans une transaction.
     */
    public function createOrder(array $orderData, array $cartItems): Order
    {
        return DB::transaction(function () use ($orderData, $cartItems) {
            $order = Order::create($orderData);

            foreach ($cartItems as $item) {
                $orderItem = OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['price'],
                    'addons_price' => $item['addon_price'],
                    'total'        => ($item['price'] + $item['addon_price']) * $item['quantity'],
                    'tax'          => 0,
                ]);

                if (! empty($item['addons'])) {
                    foreach ($item['addons'] as $addonLabel => $addonValue) {
                        $orderItem->addons()->create([
                            'addon_label' => $addonLabel,
                            'addon_value' => is_array($addonValue) ? implode(', ', $addonValue) : (string) $addonValue,
                            'addon_price' => 0,
                            'addon_type'  => 'text',
                        ]);
                    }
                }
            }

            return $order;
        });
    }
}
