<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_index_loads(): void
    {
        $this->get(route('shop.index'))->assertStatus(200);
    }

    public function test_shop_displays_active_products(): void
    {
        $active = Product::factory()->create(['name' => 'Crème Visible', 'is_active' => true]);
        $inactive = Product::factory()->create(['name' => 'Crème Cachée', 'is_active' => false]);

        $response = $this->get(route('shop.index'));

        $response->assertSee('Crème Visible');
        $response->assertDontSee('Crème Cachée');
    }

    public function test_old_categorie_param_redirects_to_new_url(): void
    {
        $cat = ProductCategory::factory()->create(['slug' => 'soins-visage']);

        $response = $this->get(route('shop.index', ['categorie' => 'soins-visage']));

        $response->assertRedirect($cat->url());
        $response->assertStatus(301);
    }

    public function test_shop_filters_by_category(): void
    {
        $cat = ProductCategory::factory()->create(['slug' => 'soins-visage']);
        $catOther = ProductCategory::factory()->create(['slug' => 'massages']);

        Product::factory()->create(['name' => 'Sérum Visage', 'category_id' => $cat->id]);
        Product::factory()->create(['name' => 'Huile Massage', 'category_id' => $catOther->id]);

        $response = $this->get($cat->url());

        $response->assertSee('Sérum Visage');
        $response->assertDontSee('Huile Massage');
    }

    public function test_shop_filters_by_brand(): void
    {
        $brand = Brand::factory()->create(['slug' => 'eskalia']);

        Product::factory()->create(['name' => 'Produit Eskalia', 'brand_id' => $brand->id]);
        Product::factory()->create(['name' => 'Produit Autre']);

        $response = $this->get(route('shop.index', ['marque' => 'eskalia']));

        $response->assertSee('Produit Eskalia');
        $response->assertDontSee('Produit Autre');
    }

    public function test_shop_search(): void
    {
        Product::factory()->create(['name' => 'Baume Réparateur']);
        Product::factory()->create(['name' => 'Crème Hydratante']);

        $response = $this->get(route('shop.index', ['q' => 'baume']));

        $response->assertSee('Baume Réparateur');
        $response->assertDontSee('Crème Hydratante');
    }

    public function test_product_detail_page_loads(): void
    {
        $parent = ProductCategory::factory()->create(['slug' => 'soins']);
        $child = ProductCategory::factory()->create(['slug' => 'cremes', 'parent_id' => $parent->id]);
        $product = Product::factory()->create(['category_id' => $child->id]);

        $response = $this->get($product->url());

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_inactive_product_returns_404_for_guest(): void
    {
        $parent = ProductCategory::factory()->create(['slug' => 'soins2']);
        $child = ProductCategory::factory()->create(['slug' => 'cremes2', 'parent_id' => $parent->id]);
        $product = Product::factory()->create(['is_active' => false, 'category_id' => $child->id]);

        $this->get($product->url())->assertStatus(404);
    }

    public function test_category_includes_children(): void
    {
        $parent = ProductCategory::factory()->create(['slug' => 'bijoux']);
        $child = ProductCategory::factory()->create(['slug' => 'bracelets', 'parent_id' => $parent->id]);

        Product::factory()->create(['name' => 'Bracelet Or', 'category_id' => $child->id]);

        $response = $this->get($parent->url());

        $response->assertSee('Bracelet Or');
    }
}
