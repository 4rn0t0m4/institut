<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductReviewFactory extends Factory
{
    protected $model = ProductReview::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'author_name' => fake()->name(),
            'author_email' => fake()->email(),
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'is_approved' => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(['is_approved' => true]);
    }
}
