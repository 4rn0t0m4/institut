<?php

namespace App\Console\Commands\Migrate;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;

class WpOrders extends WpImportCommand
{
    protected $signature   = 'migrate:wp-orders';
    protected $description = 'Importe les commandes WooCommerce depuis WordPress';

    // Mapping statuts WooCommerce -> Laravel
    private array $statusMap = [
        'wc-pending'    => 'pending',
        'wc-processing' => 'processing',
        'wc-on-hold'    => 'on-hold',
        'wc-completed'  => 'completed',
        'wc-cancelled'  => 'cancelled',
        'wc-refunded'   => 'refunded',
        'wc-failed'     => 'failed',
        'pending'       => 'pending',
        'processing'    => 'processing',
        'completed'     => 'completed',
    ];

    public function handle(): void
    {
        $this->info('Import commandes...');

        $userMap    = $this->loadMap('wp_user_map.json');
        $productMap = $this->loadMap('wp_product_map.json');

        Order::query()->each(fn($o) => $o->items()->delete() && $o->delete());
        $this->safeTruncate('orders');

        $wpOrders = $this->wp()
            ->table('posts')
            ->where('post_type', 'shop_order')
            ->orderBy('ID')
            ->get();

        $created = 0;

        foreach ($wpOrders as $wpOrder) {
            $meta = $this->postMeta($wpOrder->ID);

            $wpStatus = $wpOrder->post_status;
            $status   = $this->statusMap[$wpStatus] ?? 'pending';

            $paidAt = null;
            if ($status === 'completed' && !empty($meta['_date_completed'])) {
                $paidAt = date('Y-m-d H:i:s', (int) $meta['_date_completed']);
            }

            $laravelUserId = null;
            if (!empty($meta['_customer_user'])) {
                $laravelUserId = $userMap[(int) $meta['_customer_user']] ?? null;
            }

            $order = Order::create([
                'user_id'              => $laravelUserId,
                'number'               => 'CMD-WP-' . $wpOrder->ID,
                'status'               => $status,
                'subtotal'             => (float) ($meta['_order_subtotal'] ?? 0),
                'discount_total'       => (float) ($meta['_cart_discount'] ?? 0),
                'shipping_total'       => (float) ($meta['_order_shipping'] ?? 0),
                'tax_total'            => (float) ($meta['_order_tax'] ?? 0),
                'total'                => (float) ($meta['_order_total'] ?? 0),
                'currency'             => $meta['_order_currency'] ?? 'EUR',
                'payment_method'       => $meta['_payment_method'] ?? null,
                'paid_at'              => $paidAt,
                'billing_first_name'   => $meta['_billing_first_name'] ?? null,
                'billing_last_name'    => $meta['_billing_last_name'] ?? null,
                'billing_email'        => $meta['_billing_email'] ?? null,
                'billing_phone'        => $meta['_billing_phone'] ?? null,
                'billing_address_1'    => $meta['_billing_address_1'] ?? null,
                'billing_address_2'    => $meta['_billing_address_2'] ?? null,
                'billing_city'         => $meta['_billing_city'] ?? null,
                'billing_postcode'     => $meta['_billing_postcode'] ?? null,
                'billing_country'      => $meta['_billing_country'] ?? null,
                'shipping_first_name'  => $meta['_shipping_first_name'] ?? null,
                'shipping_last_name'   => $meta['_shipping_last_name'] ?? null,
                'shipping_address_1'   => $meta['_shipping_address_1'] ?? null,
                'shipping_address_2'   => $meta['_shipping_address_2'] ?? null,
                'shipping_city'        => $meta['_shipping_city'] ?? null,
                'shipping_postcode'    => $meta['_shipping_postcode'] ?? null,
                'shipping_country'     => $meta['_shipping_country'] ?? null,
                'shipping_method'      => $meta['_shipping_method'] ?? null,
                'customer_note'        => $wpOrder->post_excerpt ?: null,
                'created_at'           => $wpOrder->post_date,
            ]);

            $this->importOrderItems($wpOrder->ID, $order->id, $productMap);
            $created++;
        }

        $this->printResult('Commandes', $created);
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

            $wpProductId  = (int) ($itemMeta['_product_id'] ?? 0);
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
