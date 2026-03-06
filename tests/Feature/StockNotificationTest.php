<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_stock_alert(): void
    {
        $product = Product::factory()->create(['stock_status' => 'outofstock']);

        $response = $this->post(route('shop.stock-notify', $product), [
            'email' => 'client@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock_notifications', [
            'product_id' => $product->id,
            'email'      => 'client@example.com',
        ]);
    }

    public function test_duplicate_subscription_doesnt_create_second_record(): void
    {
        $product = Product::factory()->create();

        $this->post(route('shop.stock-notify', $product), ['email' => 'test@example.com']);
        $this->post(route('shop.stock-notify', $product), ['email' => 'test@example.com']);

        $this->assertDatabaseCount('stock_notifications', 1);
    }

    public function test_subscription_requires_email(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('shop.stock-notify', $product), ['email' => '']);

        $response->assertSessionHasErrors('email');
    }
}
