<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // --- Login ---

    public function test_login_page_loads(): void
    {
        $this->get(route('login'))->assertStatus(200);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('account.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // --- Registration ---

    public function test_register_page_loads(): void
    {
        $this->get(route('register'))->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('account.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
    }

    public function test_register_requires_password_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_requires_minimum_password_length(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Test',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // --- Logout ---

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    // --- Password reset ---

    public function test_forgot_password_page_loads(): void
    {
        $this->get(route('password.request'))->assertStatus(200);
    }

    // --- Authenticated redirects ---

    public function test_guest_cannot_access_account(): void
    {
        $this->get(route('account.index'))->assertRedirect(route('login'));
    }
}
