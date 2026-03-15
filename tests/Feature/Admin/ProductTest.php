<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.products.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.create'))
            ->assertStatus(200);
    }

    public function test_admin_can_create_product(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Nouveau Produit',
            'price' => 29.90,
            'stock_status' => 'instock',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Nouveau Produit', 'price' => 29.90]);
    }

    public function test_product_requires_name_and_price(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'stock_status' => 'instock',
        ]);

        $response->assertSessionHasErrors(['name', 'price']);
    }

    public function test_admin_can_edit_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product))
            ->assertStatus(200);
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), [
            'name' => 'Produit Modifie',
            'price' => 35.00,
            'stock_status' => 'instock',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Produit Modifie']);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_auto_generates_slug(): void
    {
        $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Mon Super Produit',
            'price' => 10.00,
            'stock_status' => 'instock',
        ]);

        $this->assertDatabaseHas('products', ['slug' => 'mon-super-produit']);
    }
}
