<?php

namespace Tests\Feature\Admin;

use App\Models\DiscountRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_list_discounts(): void
    {
        DiscountRule::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.discounts.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.discounts.create'))
            ->assertStatus(200);
    }

    public function test_admin_can_create_discount(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.discounts.store'), [
            'name'            => 'Promo Ete',
            'coupon_code'     => 'ETE2026',
            'type'            => 'all_products',
            'discount_type'   => 'percentage',
            'discount_amount' => 15,
        ]);

        $response->assertRedirect(route('admin.discounts.index'));
        $this->assertDatabaseHas('discount_rules', ['coupon_code' => 'ETE2026']);
    }

    public function test_admin_can_update_discount(): void
    {
        $discount = DiscountRule::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('admin.discounts.update', $discount), [
            'name'            => 'Modifie',
            'type'            => 'all_products',
            'discount_type'   => 'flat',
            'discount_amount' => 5,
        ]);

        $response->assertRedirect(route('admin.discounts.index'));
        $this->assertDatabaseHas('discount_rules', ['id' => $discount->id, 'name' => 'Modifie']);
    }

    public function test_admin_can_delete_discount(): void
    {
        $discount = DiscountRule::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.discounts.destroy', $discount));

        $response->assertRedirect(route('admin.discounts.index'));
        $this->assertDatabaseMissing('discount_rules', ['id' => $discount->id]);
    }

    public function test_coupon_code_must_be_unique(): void
    {
        DiscountRule::factory()->create(['coupon_code' => 'UNIQUE']);

        $response = $this->actingAs($this->admin)->post(route('admin.discounts.store'), [
            'name'            => 'Doublon',
            'coupon_code'     => 'UNIQUE',
            'type'            => 'all_products',
            'discount_type'   => 'percentage',
            'discount_amount' => 10,
        ]);

        $response->assertSessionHasErrors('coupon_code');
    }
}
