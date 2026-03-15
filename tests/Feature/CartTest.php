<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_page_loads(): void
    {
        $this->get(route('cart.index'))->assertStatus(200);
    }

    public function test_add_product_to_cart(): void
    {
        $product = Product::factory()->create(['price' => 25.00, 'stock_status' => 'instock']);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect();

        $cart = app(CartService::class);
        $this->assertEquals(1, $cart->count());
    }

    public function test_add_product_with_quantity(): void
    {
        $product = Product::factory()->create(['price' => 10.00, 'stock_status' => 'instock']);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $cart = app(CartService::class);
        $this->assertEquals(3, $cart->count());
    }

    public function test_update_cart_item_quantity(): void
    {
        $product = Product::factory()->create(['price' => 10.00]);
        $cart = app(CartService::class);
        $key = $cart->add($product);

        $this->patch(route('cart.update', $key), ['quantity' => 5]);

        $this->assertEquals(5, $cart->count());
    }

    public function test_remove_cart_item(): void
    {
        $product = Product::factory()->create(['price' => 10.00]);
        $cart = app(CartService::class);
        $key = $cart->add($product);

        $this->delete(route('cart.remove', $key));

        $this->assertEquals(0, $cart->count());
    }

    public function test_mini_cart_returns_html(): void
    {
        $response = $this->get(route('cart.mini'));

        $response->assertStatus(200);
    }

    public function test_add_nonexistent_product_fails(): void
    {
        $response = $this->post(route('cart.add'), [
            'product_id' => 99999,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('product_id');
    }
}
