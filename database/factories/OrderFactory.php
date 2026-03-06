<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id'             => User::factory(),
            'status'              => 'pending',
            'subtotal'            => 50.00,
            'discount_total'      => 0,
            'shipping_total'      => 5.00,
            'tax_total'           => 0,
            'total'               => 55.00,
            'currency'            => 'EUR',
            'payment_method'      => 'stripe',
            'billing_first_name'  => fake()->firstName(),
            'billing_last_name'   => fake()->lastName(),
            'billing_email'       => fake()->email(),
            'billing_phone'       => fake()->phoneNumber(),
            'billing_address_1'   => fake()->streetAddress(),
            'billing_city'        => fake()->city(),
            'billing_postcode'    => fake()->postcode(),
            'billing_country'     => 'FR',
            'shipping_first_name' => fake()->firstName(),
            'shipping_last_name'  => fake()->lastName(),
            'shipping_address_1'  => fake()->streetAddress(),
            'shipping_city'       => fake()->city(),
            'shipping_postcode'   => fake()->postcode(),
            'shipping_country'    => 'FR',
            'shipping_method'     => 'Livraison à domicile (Colissimo)',
        ];
    }

    public function processing(): static
    {
        return $this->state(['status' => 'processing']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed', 'paid_at' => now()]);
    }
}
