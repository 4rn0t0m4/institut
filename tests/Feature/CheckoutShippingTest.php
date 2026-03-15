<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutShippingTest extends TestCase
{
    use RefreshDatabase;

    private function addProductToCart(): void
    {
        $product = Product::factory()->create([
            'price' => 25.00,
            'is_active' => true,
            'stock_status' => 'instock',
        ]);

        $cart = app(CartService::class);
        $cart->add($product);
    }

    private function checkoutData(array $overrides = []): array
    {
        return array_merge([
            'billing_first_name' => 'Jean',
            'billing_last_name' => 'Dupont',
            'billing_email' => 'jean@example.com',
            'billing_phone' => '0600000000',
            'billing_address_1' => '1 rue de la Paix',
            'billing_city' => 'Paris',
            'billing_postcode' => '75001',
            'billing_country' => 'FR',
            'shipping_same' => '1',
            'shipping_method' => 'colissimo',
        ], $overrides);
    }

    public function test_checkout_page_loads_with_shipping_countries(): void
    {
        $this->addProductToCart();

        $response = $this->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertSee('France');
        $response->assertSee('Belgique');
        $response->assertSee('Espagne');
        $response->assertSee('Italie');
    }

    public function test_france_accepts_colissimo(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'FR',
            'shipping_method' => 'colissimo',
        ]));

        // Should not have shipping_method validation error
        $response->assertSessionDoesntHaveErrors('shipping_method');
    }

    public function test_france_accepts_pickup(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'FR',
            'shipping_method' => 'pickup',
        ]));

        $response->assertSessionDoesntHaveErrors('shipping_method');
    }

    public function test_france_accepts_boxtal_with_relay(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'FR',
            'shipping_method' => 'boxtal',
            'relay_point_code' => 'MONR-12345',
            'relay_point_name' => 'Relay Test',
            'relay_point_address' => '1 rue Test, 75001 Paris',
        ]));

        $response->assertSessionDoesntHaveErrors('shipping_method');
    }

    public function test_belgium_rejects_colissimo(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'BE',
            'shipping_method' => 'colissimo',
        ]));

        $response->assertSessionHasErrors('shipping_method');
    }

    public function test_belgium_rejects_pickup(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'BE',
            'shipping_method' => 'pickup',
        ]));

        $response->assertSessionHasErrors('shipping_method');
    }

    public function test_belgium_accepts_boxtal(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'BE',
            'shipping_method' => 'boxtal',
            'relay_point_code' => 'MONR-99999',
            'relay_point_name' => 'Relay Bruxelles',
            'relay_point_address' => '1 Grand Place, 1000 Bruxelles',
        ]));

        $response->assertSessionDoesntHaveErrors('shipping_method');
    }

    public function test_spain_rejects_colissimo(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'ES',
            'shipping_method' => 'colissimo',
        ]));

        $response->assertSessionHasErrors('shipping_method');
    }

    public function test_italy_rejects_pickup(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'IT',
            'shipping_method' => 'pickup',
        ]));

        $response->assertSessionHasErrors('shipping_method');
    }

    public function test_unsupported_country_is_rejected(): void
    {
        $this->addProductToCart();

        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'DE',
            'shipping_method' => 'boxtal',
            'relay_point_code' => 'TEST',
        ]));

        $response->assertSessionHasErrors('billing_country');
    }

    public function test_shipping_country_overrides_billing_for_method_validation(): void
    {
        $this->addProductToCart();

        // Billing = FR but shipping to BE with colissimo should fail
        $response = $this->post(route('checkout.store'), $this->checkoutData([
            'billing_country' => 'FR',
            'shipping_same' => '0',
            'shipping_country' => 'BE',
            'shipping_method' => 'colissimo',
            'shipping_first_name' => 'Jean',
            'shipping_last_name' => 'Dupont',
            'shipping_address_1' => '1 Grand Place',
            'shipping_city' => 'Bruxelles',
            'shipping_postcode' => '1000',
        ]));

        $response->assertSessionHasErrors('shipping_method');
    }
}
