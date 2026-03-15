<?php

namespace Database\Factories;

use App\Models\DiscountRule;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountRuleFactory extends Factory
{
    protected $model = DiscountRule::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'is_active' => true,
            'type' => 'all_products',
            'discount_type' => 'percentage',
            'discount_amount' => 10.00,
            'stackable' => true,
            'sort_order' => 0,
        ];
    }

    public function withCoupon(string $code): static
    {
        return $this->state(['coupon_code' => $code]);
    }

    public function fixed(float $amount): static
    {
        return $this->state(['discount_type' => 'flat', 'discount_amount' => $amount]);
    }

    public function percentage(float $pct): static
    {
        return $this->state(['discount_type' => 'percentage', 'discount_amount' => $pct]);
    }
}
