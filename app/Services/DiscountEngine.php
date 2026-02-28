<?php

namespace App\Services;

use App\Models\DiscountRule;

class DiscountEngine
{
    /**
     * Calculate the total discount for a cart.
     *
     * @param array $cartItems  Array from CartService::items()
     * @param float $subtotal   Cart subtotal before discount
     * @return array{amount: float, rules: array}
     */
    public function calculate(array $cartItems, float $subtotal): array
    {
        $rules = DiscountRule::active()->orderBy('sort_order')->get();

        $totalDiscount = 0.0;
        $appliedRules = [];

        foreach ($rules as $rule) {
            if (!$this->applies($rule, $cartItems, $subtotal)) {
                continue;
            }

            $discount = $this->computeDiscount($rule, $cartItems, $subtotal);

            if ($discount <= 0) {
                continue;
            }

            $totalDiscount += $discount;
            $appliedRules[] = [
                'name'     => $rule->name,
                'type'     => $rule->type,
                'discount' => round($discount, 2),
            ];

            // Stop after first non-stackable rule
            if (!$rule->stackable) {
                break;
            }
        }

        return [
            'amount' => round(min($totalDiscount, $subtotal), 2),
            'rules'  => $appliedRules,
        ];
    }

    private function applies(DiscountRule $rule, array $items, float $subtotal): bool
    {
        // Cart value condition
        if ($rule->min_cart_value !== null && $subtotal < $rule->min_cart_value) {
            return false;
        }
        if ($rule->max_cart_value !== null && $subtotal > $rule->max_cart_value) {
            return false;
        }

        // Quantity condition (total cart qty)
        if ($rule->min_quantity !== null || $rule->max_quantity !== null) {
            $totalQty = array_sum(array_column($items, 'quantity'));
            if ($rule->min_quantity !== null && $totalQty < $rule->min_quantity) {
                return false;
            }
            if ($rule->max_quantity !== null && $totalQty > $rule->max_quantity) {
                return false;
            }
        }

        // Target category / product restriction
        if (!empty($rule->target_categories) || !empty($rule->target_products)) {
            $hasMatch = false;
            foreach ($items as $item) {
                $product = $item['product'] ?? null;
                if (!$product) {
                    continue;
                }
                if (!empty($rule->target_products) && in_array($product->id, $rule->target_products)) {
                    $hasMatch = true;
                    break;
                }
                if (!empty($rule->target_categories) && in_array($product->category_id, $rule->target_categories)) {
                    $hasMatch = true;
                    break;
                }
            }
            if (!$hasMatch) {
                return false;
            }
        }

        return true;
    }

    private function computeDiscount(DiscountRule $rule, array $items, float $subtotal): float
    {
        // Base affected amount
        $base = $subtotal;

        // If rule targets specific categories/products, compute only their subtotal
        if (!empty($rule->target_categories) || !empty($rule->target_products)) {
            $base = 0.0;
            foreach ($items as $item) {
                $product = $item['product'] ?? null;
                if (!$product) {
                    continue;
                }
                $matches = (!empty($rule->target_products) && in_array($product->id, $rule->target_products))
                        || (!empty($rule->target_categories) && in_array($product->category_id, $rule->target_categories));
                if ($matches) {
                    $base += ($item['unit_price'] + ($item['addon_price'] ?? 0)) * $item['quantity'];
                }
            }
        }

        if ($rule->discount_type === 'percentage') {
            return $base * ((float) $rule->discount_amount / 100);
        }

        // Fixed amount
        return min((float) $rule->discount_amount, $base);
    }
}
