<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_review(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('shop.review.store', $product), [
            'author_name' => 'Marie',
            'author_email' => 'marie@example.com',
            'rating' => 4,
            'title' => 'Super produit',
            'body' => 'Je recommande ce produit à tout le monde.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'author_name' => 'Marie',
            'rating' => 4,
            'is_approved' => false,
        ]);
    }

    public function test_review_requires_all_fields(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('shop.review.store', $product), []);

        $response->assertSessionHasErrors(['author_name', 'author_email', 'rating', 'title', 'body']);
    }

    public function test_rating_must_be_between_1_and_5(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('shop.review.store', $product), [
            'author_name' => 'Test',
            'author_email' => 'test@example.com',
            'rating' => 6,
            'title' => 'Test',
            'body' => 'Test body',
        ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_verified_buyer_detected(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->completed()->create(['user_id' => $user->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id]);

        $this->actingAs($user)->post(route('shop.review.store', $product), [
            'author_name' => $user->name,
            'author_email' => $user->email,
            'rating' => 5,
            'title' => 'Excellent',
            'body' => 'Très bon produit.',
        ]);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'is_verified_buyer' => true,
        ]);
    }

    public function test_non_buyer_not_verified(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->post(route('shop.review.store', $product), [
            'author_name' => $user->name,
            'author_email' => $user->email,
            'rating' => 3,
            'title' => 'Correct',
            'body' => 'Produit correct.',
        ]);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'is_verified_buyer' => false,
        ]);
    }

    public function test_only_approved_reviews_shown_on_product_page(): void
    {
        $parent = ProductCategory::factory()->create(['slug' => 'soins']);
        $child = ProductCategory::factory()->create(['slug' => 'cremes', 'parent_id' => $parent->id]);
        $product = Product::factory()->create(['category_id' => $child->id]);
        ProductReview::factory()->approved()->create([
            'product_id' => $product->id,
            'author_name' => 'Approuvé',
        ]);
        ProductReview::factory()->create([
            'product_id' => $product->id,
            'author_name' => 'En attente',
        ]);

        $response = $this->get($product->url());

        $response->assertSee('Approuvé');
        $response->assertDontSee('En attente');
    }
}
