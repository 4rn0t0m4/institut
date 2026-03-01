<?php

namespace App\Console\Commands\Migrate;

use App\Models\Order;
use App\Models\OrderItem;

class WpOrders extends WpImportCommand
{
    protected $signature   = 'migrate:wp-orders';
    protected $description = 'Importe les commandes WooCommerce (HPOS) depuis WordPress';

    private array $statusMap = [
        'wc-pending'    => 'pending',
        'wc-processing' => 'processing',
        'wc-on-hold'    => 'on-hold',
        'wc-completed'  => 'completed',
        'wc-cancelled'  => 'cancelled',
        'wc-refunded'   => 'refunded',
        'wc-failed'     => 'failed',
    ];

    public function handle(): void
    {
        $this->info('Import commandes (HPOS)...');

        $userMap    = $this->loadMap('wp_user_map.json');
        $productMap = $this->loadMap('wp_product_map.json');

        // Clean existing orders
        $this->safeTruncate('order_item_addons');
        $this->safeTruncate('order_items');
        $this->safeTruncate('orders');

        $wpOrders = $this->wp()
            ->table('wc_orders')
            ->where('type', 'shop_order')
            ->orderBy('id')
            ->get();

        $created = 0;

        foreach ($wpOrders as $wo) {
            $status = $this->statusMap[$wo->status] ?? 'pending';

            // Addresses
            $billing  = $this->getAddress($wo->id, 'billing');
            $shipping = $this->getAddress($wo->id, 'shipping');

            // Operational data (paid_at, shipping totals, discounts)
            $opData = $this->wp()
                ->table('wc_order_operational_data')
                ->where('order_id', $wo->id)
                ->first();

            $paidAt = null;
            if ($opData && $opData->date_paid_gmt) {
                $paidAt = $opData->date_paid_gmt;
            }

            // Map WP user to Laravel user
            $laravelUserId = null;
            if ($wo->customer_id) {
                $laravelUserId = $userMap[(int) $wo->customer_id] ?? null;
            }

            $order = new Order;
            $order->timestamps = false;
            $order->fill([
                'user_id'              => $laravelUserId,
                'number'               => 'CMD-WP-' . $wo->id,
                'status'               => $status,
                'subtotal'             => (float) $wo->total_amount - (float) ($opData->shipping_total_amount ?? 0),
                'discount_total'       => (float) ($opData->discount_total_amount ?? 0),
                'shipping_total'       => (float) ($opData->shipping_total_amount ?? 0),
                'tax_total'            => (float) ($wo->tax_amount ?? 0),
                'total'                => (float) $wo->total_amount,
                'currency'             => $wo->currency ?? 'EUR',
                'payment_method'       => $wo->payment_method,
                'paid_at'              => $paidAt,
                'billing_first_name'   => $billing->first_name ?? null,
                'billing_last_name'    => $billing->last_name ?? null,
                'billing_email'        => $billing->email ?? $wo->billing_email,
                'billing_phone'        => $billing->phone ?? null,
                'billing_address_1'    => $billing->address_1 ?? null,
                'billing_address_2'    => $billing->address_2 ?? null,
                'billing_city'         => $billing->city ?? null,
                'billing_postcode'     => $billing->postcode ?? null,
                'billing_country'      => $billing->country ?? null,
                'shipping_first_name'  => $shipping->first_name ?? null,
                'shipping_last_name'   => $shipping->last_name ?? null,
                'shipping_address_1'   => $shipping->address_1 ?? null,
                'shipping_address_2'   => $shipping->address_2 ?? null,
                'shipping_city'        => $shipping->city ?? null,
                'shipping_postcode'    => $shipping->postcode ?? null,
                'shipping_country'     => $shipping->country ?? null,
                'customer_note'        => $wo->customer_note ?: null,
            ]);
            $order->created_at = $wo->date_created_gmt;
            $order->updated_at = $wo->date_updated_gmt;
            $order->save();

            $this->importOrderItems($wo->id, $order->id, $productMap);
            $created++;
        }

        $this->printResult('Commandes', $created);
    }

    private function getAddress(int $orderId, string $type): ?object
    {
        return $this->wp()
            ->table('wc_order_addresses')
            ->where('order_id', $orderId)
            ->where('address_type', $type)
            ->first();
    }

    private function importOrderItems(int $wpOrderId, int $laravelOrderId, array $productMap): void
    {
        $items = $this->wp()
            ->table('woocommerce_order_items')
            ->where('order_id', $wpOrderId)
            ->where('order_item_type', 'line_item')
            ->get();

        foreach ($items as $item) {
            $itemMeta = $this->wp()
                ->table('woocommerce_order_itemmeta')
                ->where('order_item_id', $item->order_item_id)
                ->pluck('meta_value', 'meta_key')
                ->all();

            $wpProductId   = (int) ($itemMeta['_product_id'] ?? 0);
            $laravelProdId = $productMap[$wpProductId] ?? null;

            $qty       = (int)   ($itemMeta['_qty'] ?? 1);
            $lineTotal = (float) ($itemMeta['_line_total'] ?? 0);
            $unitPrice = $qty > 0 ? round($lineTotal / $qty, 2) : 0;

            OrderItem::create([
                'order_id'     => $laravelOrderId,
                'product_id'   => $laravelProdId,
                'product_name' => $item->order_item_name,
                'quantity'     => $qty,
                'unit_price'   => $unitPrice,
                'addons_price' => 0,
                'total'        => $lineTotal,
                'tax'          => (float) ($itemMeta['_line_tax'] ?? 0),
            ]);
        }
    }

    private function loadMap(string $filename): array
    {
        $path = storage_path($filename);
        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }
}
