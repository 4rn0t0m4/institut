<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name'              => $name,
            'slug'              => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 99999),
            'price'             => fake()->randomFloat(2, 5, 100),
            'stock_status'      => 'instock',
            'is_active'         => true,
            'is_featured'       => false,
            'manage_stock'      => false,
            'is_virtual'        => false,
            'is_downloadable'   => false,
            'short_description' => fake()->sentence(),
        ];
    }
}
