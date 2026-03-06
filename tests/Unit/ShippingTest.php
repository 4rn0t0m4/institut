<?php

namespace Tests\Unit;

use App\Services\CartService;
use App\Services\OrderService;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService(app(CartService::class));
    }

    // --- Zones ---

    public function test_france_returns_fr_zone(): void
    {
        $this->assertEquals('FR', $this->orderService->getShippingZone('FR'));
    }

    public function test_belgium_returns_international_zone(): void
    {
        $this->assertEquals('international', $this->orderService->getShippingZone('BE'));
    }

    public function test_spain_returns_international_zone(): void
    {
        $this->assertEquals('international', $this->orderService->getShippingZone('ES'));
    }

    public function test_italy_returns_international_zone(): void
    {
        $this->assertEquals('international', $this->orderService->getShippingZone('IT'));
    }

    public function test_unknown_country_falls_back_to_fr_zone(): void
    {
        $this->assertEquals('FR', $this->orderService->getShippingZone('DE'));
    }

    // --- Available methods ---

    public function test_france_has_all_shipping_methods(): void
    {
        $methods = $this->orderService->availableMethodsForCountry('FR');
        $this->assertArrayHasKey('colissimo', $methods);
        $this->assertArrayHasKey('boxtal', $methods);
        $this->assertArrayHasKey('pickup', $methods);
    }

    public function test_belgium_has_only_boxtal(): void
    {
        $methods = $this->orderService->availableMethodsForCountry('BE');
        $this->assertArrayHasKey('boxtal', $methods);
        $this->assertArrayNotHasKey('colissimo', $methods);
        $this->assertArrayNotHasKey('pickup', $methods);
    }

    public function test_spain_has_only_boxtal(): void
    {
        $methods = $this->orderService->availableMethodsForCountry('ES');
        $this->assertArrayHasKey('boxtal', $methods);
        $this->assertCount(1, $methods);
    }

    public function test_italy_has_only_boxtal(): void
    {
        $methods = $this->orderService->availableMethodsForCountry('IT');
        $this->assertArrayHasKey('boxtal', $methods);
        $this->assertCount(1, $methods);
    }

    // --- Shipping costs FR ---

    public function test_fr_colissimo_cost(): void
    {
        $this->assertEquals(7.90, $this->orderService->calculateShipping('colissimo', 30.00, 'FR'));
    }

    public function test_fr_boxtal_cost(): void
    {
        $this->assertEquals(5.00, $this->orderService->calculateShipping('boxtal', 30.00, 'FR'));
    }

    public function test_fr_pickup_is_free(): void
    {
        $this->assertEquals(0.00, $this->orderService->calculateShipping('pickup', 30.00, 'FR'));
    }

    public function test_fr_boxtal_free_above_60(): void
    {
        $this->assertEquals(0.00, $this->orderService->calculateShipping('boxtal', 60.00, 'FR'));
    }

    public function test_fr_boxtal_not_free_below_60(): void
    {
        $this->assertEquals(5.00, $this->orderService->calculateShipping('boxtal', 59.99, 'FR'));
    }

    public function test_fr_colissimo_never_free(): void
    {
        $this->assertEquals(7.90, $this->orderService->calculateShipping('colissimo', 100.00, 'FR'));
    }

    // --- Shipping costs international ---

    public function test_be_boxtal_cost(): void
    {
        $this->assertEquals(5.90, $this->orderService->calculateShipping('boxtal', 30.00, 'BE'));
    }

    public function test_es_boxtal_cost(): void
    {
        $this->assertEquals(5.90, $this->orderService->calculateShipping('boxtal', 30.00, 'ES'));
    }

    public function test_it_boxtal_cost(): void
    {
        $this->assertEquals(5.90, $this->orderService->calculateShipping('boxtal', 30.00, 'IT'));
    }

    public function test_international_boxtal_free_above_80(): void
    {
        $this->assertEquals(0.00, $this->orderService->calculateShipping('boxtal', 80.00, 'BE'));
    }

    public function test_international_boxtal_not_free_at_60(): void
    {
        $this->assertEquals(5.90, $this->orderService->calculateShipping('boxtal', 60.00, 'BE'));
    }

    public function test_international_boxtal_not_free_below_80(): void
    {
        $this->assertEquals(5.90, $this->orderService->calculateShipping('boxtal', 79.99, 'ES'));
    }

    // --- Default country parameter ---

    public function test_default_country_is_france(): void
    {
        $this->assertEquals(5.00, $this->orderService->calculateShipping('boxtal', 30.00));
    }
}
