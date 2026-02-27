<?php

namespace App\Console\Commands\Migrate;

use App\Models\DiscountRule;

class WpDiscounts extends WpImportCommand
{
    protected $signature   = 'migrate:wp-discounts';
    protected $description = 'Importe les règles de remise depuis WordPress (plugin Simple Discount Rules)';

    // discount_type WP : 0=all, 1=taxonomy, 2=cart, 3=quantity
    private array $typeMap = [
        0 => 'all_products',
        1 => 'category',
        2 => 'cart_value',
        3 => 'quantity',
    ];

    // discount_amount_type WP : 0=percentage, 1=flat
    private array $amountTypeMap = [
        0 => 'percentage',
        1 => 'flat',
    ];

    public function handle(): void
    {
        $this->info('Import remises...');

        $this->safeTruncate('discount_rules');

        $wpDiscounts = $this->wp()->table('wpcd_discounts')->get();
        $catMap      = file_exists(storage_path('wp_category_map.json'))
            ? json_decode(file_get_contents(storage_path('wp_category_map.json')), true)
            : [];

        $created = 0;

        foreach ($wpDiscounts as $wd) {
            $type         = $this->typeMap[$wd->discount_type] ?? 'all_products';
            $discountType = $this->amountTypeMap[$wd->discount_amount_type] ?? 'percentage';

            // Résoudre les catégories cibles pour les remises de type taxonomy
            $targetCategories = null;
            if ($type === 'category') {
                $wpTermIds = $this->wp()
                    ->table('wpcd_taxonomy_discount_terms')
                    ->where('discount_id', $wd->id)
                    ->get();

                $laravelCatIds = [];
                foreach ($wpTermIds as $row) {
                    $terms = json_decode($row->terms ?? '[]', true);
                    foreach ((array) $terms as $wpTermId) {
                        if (isset($catMap[$wpTermId])) {
                            $laravelCatIds[] = $catMap[$wpTermId];
                        }
                    }
                }
                $targetCategories = $laravelCatIds ?: null;
            }

            // Conditions panier
            $minCart = null;
            $maxCart = null;
            if ($type === 'cart_value') {
                $cartRule = $this->wp()
                    ->table('wpcd_cart_discount_rules')
                    ->where('discount_id', $wd->id)
                    ->first();
                if ($cartRule) {
                    $minCart = $cartRule->min_cart_value ?? null;
                    $maxCart = $cartRule->max_cart_value ?? null;
                }
            }

            DiscountRule::create([
                'name'              => $wd->name,
                'is_active'         => (bool) $wd->status,
                'type'              => $type,
                'discount_type'     => $discountType,
                'discount_amount'   => (float) $wd->discount_amount,
                'target_categories' => $targetCategories,
                'min_cart_value'    => $minCart,
                'max_cart_value'    => $maxCart,
                'starts_at'         => $wd->start_date ?: null,
                'ends_at'           => $wd->end_date ?: null,
                'stackable'         => false,
                'sort_order'        => 0,
            ]);

            $created++;
        }

        $this->printResult('Remises', $created);
    }
}
