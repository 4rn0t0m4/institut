<?php

namespace Tests\Unit;

use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\DiscountEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountEngineTest extends TestCase
{
    use RefreshDatabase;

    private DiscountEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new DiscountEngine();
    }

    private function makeCartItems(array $products): array
    {
        return array_map(fn(Product $p) => [
            'product_id' => $p->id,
            'product'    => $p,
            'price'      => $p->currentPrice(),
            'unit_price' => $p->currentPrice(),
            'addon_price' => 0,
            'quantity'   => 1,
        ], $products);
    }

    public function test_no_rules_returns_zero_discount(): void
    {
        $result = $this->engine->calculate([], 100.00);

        $this->assertEquals(0.0, $result['amount']);
        $this->assertNull($result['label']);
        $this->assertEmpty($result['rules']);
    }

    public function test_percentage_discount_on_all_products(): void
    {
        DiscountRule::factory()->percentage(10)->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $result = $this->engine->calculate($this->makeCartItems([$product]), 100.00);

        $this->assertEquals(10.00, $result['amount']);
    }

    public function test_fixed_discount_on_all_products(): void
    {
        DiscountRule::factory()->fixed(15)->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $result = $this->engine->calculate($this->makeCartItems([$product]), 100.00);

        $this->assertEquals(15.00, $result['amount']);
    }

    public function test_coupon_code_required_when_set(): void
    {
        DiscountRule::factory()->withCoupon('PROMO10')->percentage(10)->create();
        $product = Product::factory()->create(['price' => 100.00]);
        $items = $this->makeCartItems([$product]);

        // Without coupon
        $result = $this->engine->calculate($items, 100.00);
        $this->assertEquals(0.0, $result['amount']);

        // With wrong coupon
        $result = $this->engine->calculate($items, 100.00, 'WRONG');
        $this->assertEquals(0.0, $result['amount']);

        // With correct coupon
        $result = $this->engine->calculate($items, 100.00, 'PROMO10');
        $this->assertEquals(10.00, $result['amount']);
    }

    public function test_coupon_code_is_case_insensitive(): void
    {
        DiscountRule::factory()->withCoupon('PROMO10')->percentage(10)->create();
        $product = Product::factory()->create(['price' => 100.00]);
        $items = $this->makeCartItems([$product]);

        $result = $this->engine->calculate($items, 100.00, 'promo10');
        $this->assertEquals(10.00, $result['amount']);
    }

    public function test_min_cart_value_condition(): void
    {
        DiscountRule::factory()->percentage(10)->create(['min_cart_value' => 50.00]);
        $product = Product::factory()->create(['price' => 30.00]);
        $items = $this->makeCartItems([$product]);

        // Below minimum
        $result = $this->engine->calculate($items, 30.00);
        $this->assertEquals(0.0, $result['amount']);

        // Above minimum
        $result = $this->engine->calculate($items, 60.00);
        $this->assertEquals(6.00, $result['amount']);
    }

    public function test_max_cart_value_condition(): void
    {
        DiscountRule::factory()->percentage(10)->create(['max_cart_value' => 50.00]);
        $product = Product::factory()->create(['price' => 60.00]);

        $result = $this->engine->calculate($this->makeCartItems([$product]), 60.00);
        $this->assertEquals(0.0, $result['amount']);
    }

    public function test_min_quantity_condition(): void
    {
        DiscountRule::factory()->percentage(10)->create(['min_quantity' => 3]);
        $product = Product::factory()->create(['price' => 10.00]);

        // 1 item
        $items = [['product_id' => $product->id, 'product' => $product, 'price' => 10.00, 'unit_price' => 10.00, 'addon_price' => 0, 'quantity' => 1]];
        $result = $this->engine->calculate($items, 10.00);
        $this->assertEquals(0.0, $result['amount']);

        // 3 items
        $items[0]['quantity'] = 3;
        $result = $this->engine->calculate($items, 30.00);
        $this->assertEquals(3.00, $result['amount']);
    }

    public function test_category_targeting(): void
    {
        $cat = ProductCategory::factory()->create();
        $catOther = ProductCategory::factory()->create();

        DiscountRule::factory()->percentage(20)->create([
            'target_categories' => [$cat->id],
        ]);

        $pTarget = Product::factory()->create(['price' => 50.00, 'category_id' => $cat->id]);
        $pOther = Product::factory()->create(['price' => 50.00, 'category_id' => $catOther->id]);

        // Product in target category
        $result = $this->engine->calculate($this->makeCartItems([$pTarget]), 50.00);
        $this->assertEquals(10.00, $result['amount']);

        // Product NOT in target category
        $result = $this->engine->calculate($this->makeCartItems([$pOther]), 50.00);
        $this->assertEquals(0.0, $result['amount']);
    }

    public function test_product_targeting(): void
    {
        $pTarget = Product::factory()->create(['price' => 50.00]);
        $pOther = Product::factory()->create(['price' => 50.00]);

        DiscountRule::factory()->percentage(20)->create([
            'target_products' => [$pTarget->id],
        ]);

        $result = $this->engine->calculate($this->makeCartItems([$pTarget]), 50.00);
        $this->assertEquals(10.00, $result['amount']);

        $result = $this->engine->calculate($this->makeCartItems([$pOther]), 50.00);
        $this->assertEquals(0.0, $result['amount']);
    }

    public function test_discount_cannot_exceed_subtotal(): void
    {
        DiscountRule::factory()->fixed(200)->create();
        $product = Product::factory()->create(['price' => 50.00]);

        $result = $this->engine->calculate($this->makeCartItems([$product]), 50.00);
        $this->assertEquals(50.00, $result['amount']);
    }

    public function test_non_stackable_rule_stops_chain(): void
    {
        DiscountRule::factory()->percentage(10)->create(['sort_order' => 1, 'stackable' => false]);
        DiscountRule::factory()->percentage(5)->create(['sort_order' => 2, 'stackable' => true]);

        $product = Product::factory()->create(['price' => 100.00]);
        $result = $this->engine->calculate($this->makeCartItems([$product]), 100.00);

        // Only first rule applied (10%)
        $this->assertEquals(10.00, $result['amount']);
        $this->assertCount(1, $result['rules']);
    }

    public function test_stackable_rules_accumulate(): void
    {
        DiscountRule::factory()->percentage(10)->create(['sort_order' => 1, 'stackable' => true]);
        DiscountRule::factory()->fixed(5)->create(['sort_order' => 2, 'stackable' => true]);

        $product = Product::factory()->create(['price' => 100.00]);
        $result = $this->engine->calculate($this->makeCartItems([$product]), 100.00);

        $this->assertEquals(15.00, $result['amount']);
        $this->assertCount(2, $result['rules']);
    }

    public function test_inactive_rule_is_ignored(): void
    {
        DiscountRule::factory()->percentage(10)->create(['is_active' => false]);
        $product = Product::factory()->create(['price' => 100.00]);

        $result = $this->engine->calculate($this->makeCartItems([$product]), 100.00);
        $this->assertEquals(0.0, $result['amount']);
    }

    public function test_expired_rule_is_ignored(): void
    {
        DiscountRule::factory()->percentage(10)->create(['ends_at' => now()->subDay()]);
        $product = Product::factory()->create(['price' => 100.00]);

        $result = $this->engine->calculate($this->makeCartItems([$product]), 100.00);
        $this->assertEquals(0.0, $result['amount']);
    }

    public function test_future_rule_is_ignored(): void
    {
        DiscountRule::factory()->percentage(10)->create(['starts_at' => now()->addDay()]);
        $product = Product::factory()->create(['price' => 100.00]);

        $result = $this->engine->calculate($this->makeCartItems([$product]), 100.00);
        $this->assertEquals(0.0, $result['amount']);
    }

    public function test_validate_coupon_returns_rule(): void
    {
        DiscountRule::factory()->withCoupon('TESTCODE')->create();

        $this->assertNotNull($this->engine->validateCoupon('TESTCODE'));
        $this->assertNotNull($this->engine->validateCoupon('testcode'));
        $this->assertNull($this->engine->validateCoupon('INVALID'));
    }
}
