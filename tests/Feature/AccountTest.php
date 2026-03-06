<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    // --- Access control ---

    public function test_guest_redirected_from_account(): void
    {
        $this->get(route('account.index'))->assertRedirect(route('login'));
        $this->get(route('account.orders'))->assertRedirect(route('login'));
        $this->get(route('account.profile'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('account.index'))->assertStatus(200);
    }

    // --- Profile ---

    public function test_profile_page_loads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('account.profile'))->assertStatus(200);
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('account.profile.update'), [
            'name'       => 'Nouveau Nom',
            'email'      => $user->email,
            'first_name' => 'Jean',
            'last_name'  => 'Dupont',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nouveau Nom']);
    }

    // --- Password ---

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create(['password' => 'oldpassword']);

        $response = $this->actingAs($user)->patch(route('account.password.update'), [
            'current_password'      => 'oldpassword',
            'password'              => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_password_requires_confirmation(): void
    {
        $user = User::factory()->create(['password' => 'oldpassword']);

        $response = $this->actingAs($user)->patch(route('account.password.update'), [
            'current_password' => 'oldpassword',
            'password'         => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // --- Orders ---

    public function test_orders_page_loads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('account.orders'))->assertStatus(200);
    }

    public function test_user_can_view_own_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('account.order', $order))
            ->assertStatus(200);
    }

    public function test_user_cannot_view_other_users_order(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->get(route('account.order', $order))
            ->assertStatus(403);
    }

    // --- Address ---

    public function test_address_page_loads(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('account.address'))->assertStatus(200);
    }

    public function test_user_can_update_address(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('account.address.update'), [
            'first_name' => 'Jean',
            'last_name'  => 'Dupont',
            'address_1'  => '1 rue de la Paix',
            'city'       => 'Paris',
            'postcode'   => '75001',
            'country'    => 'FR',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'city' => 'Paris']);
    }
}
