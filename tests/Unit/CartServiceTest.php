<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cart = app(CartService::class);
    }

    public function test_cart_starts_empty(): void
    {
        $this->assertEmpty($this->cart->all());
        $this->assertEquals(0, $this->cart->count());
        $this->assertEquals(0.0, $this->cart->subtotal());
    }

    public function test_add_product_to_cart(): void
    {
        $product = Product::factory()->create(['price' => 25.00]);
        $key = $this->cart->add($product);

        $this->assertCount(1, $this->cart->all());
        $this->assertEquals(1, $this->cart->count());
        $this->assertEquals(25.00, $this->cart->subtotal());
    }

    public function test_add_same_product_increments_quantity(): void
    {
        $product = Product::factory()->create(['price' => 10.00]);
        $this->cart->add($product, 1);
        $this->cart->add($product, 2);

        $this->assertCount(1, $this->cart->all());
        $this->assertEquals(3, $this->cart->count());
        $this->assertEquals(30.00, $this->cart->subtotal());
    }

    public function test_add_different_products(): void
    {
        $p1 = Product::factory()->create(['price' => 10.00]);
        $p2 = Product::factory()->create(['price' => 20.00]);

        $this->cart->add($p1);
        $this->cart->add($p2);

        $this->assertCount(2, $this->cart->all());
        $this->assertEquals(2, $this->cart->count());
        $this->assertEquals(30.00, $this->cart->subtotal());
    }

    public function test_update_quantity(): void
    {
        $product = Product::factory()->create(['price' => 15.00]);
        $key = $this->cart->add($product);

        $this->cart->update($key, 5);

        $this->assertEquals(5, $this->cart->count());
        $this->assertEquals(75.00, $this->cart->subtotal());
    }

    public function test_update_to_zero_removes_item(): void
    {
        $product = Product::factory()->create(['price' => 10.00]);
        $key = $this->cart->add($product);

        $this->cart->update($key, 0);

        $this->assertEmpty($this->cart->all());
    }

    public function test_remove_item(): void
    {
        $product = Product::factory()->create(['price' => 10.00]);
        $key = $this->cart->add($product);

        $this->cart->remove($key);

        $this->assertEmpty($this->cart->all());
    }

    public function test_clear_cart(): void
    {
        $p1 = Product::factory()->create(['price' => 10.00]);
        $p2 = Product::factory()->create(['price' => 20.00]);

        $this->cart->add($p1);
        $this->cart->add($p2);
        $this->cart->clear();

        $this->assertEmpty($this->cart->all());
        $this->assertEquals(0, $this->cart->count());
    }

    public function test_update_price(): void
    {
        $product = Product::factory()->create(['price' => 10.00]);
        $key = $this->cart->add($product);

        $this->cart->updatePrice($key, 15.00);

        $items = $this->cart->all();
        $this->assertEquals(15.00, $items[$key]['price']);
    }

    public function test_items_with_products_enriches_data(): void
    {
        $product = Product::factory()->create(['price' => 25.00]);
        $this->cart->add($product);

        $items = $this->cart->itemsWithProducts();
        $first = reset($items);

        $this->assertNotNull($first['product']);
        $this->assertEquals($product->id, $first['product']->id);
        $this->assertArrayHasKey('unit_price', $first);
    }

    public function test_sale_price_used_when_set(): void
    {
        $product = Product::factory()->create(['price' => 30.00, 'sale_price' => 20.00]);
        $this->cart->add($product);

        $this->assertEquals(20.00, $this->cart->subtotal());
    }
}
