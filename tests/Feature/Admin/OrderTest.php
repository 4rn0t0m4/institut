<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_list_orders(): void
    {
        Order::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.orders.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        Order::factory()->create(['status' => 'processing']);
        Order::factory()->create(['status' => 'completed']);

        $this->actingAs($this->admin)
            ->get(route('admin.orders.index', ['status' => 'processing']))
            ->assertStatus(200);
    }

    public function test_admin_can_view_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order))
            ->assertStatus(200);
    }

    public function test_admin_can_edit_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.orders.edit', $order))
            ->assertStatus(200);
    }

    public function test_admin_can_update_order_status(): void
    {
        Mail::fake();
        $order = Order::factory()->create(['status' => 'processing']);

        $response = $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
    }

    public function test_adding_tracking_sends_shipped_email(): void
    {
        Mail::fake();
        $order = Order::factory()->create(['status' => 'processing', 'billing_email' => 'client@test.com']);

        $this->actingAs($this->admin)->put(route('admin.orders.update', $order), [
            'status'           => 'processing',
            'tracking_number'  => 'COL123456',
            'tracking_carrier' => 'Colissimo',
        ]);

        Mail::assertSent(\App\Mail\OrderShipped::class, fn ($mail) => $mail->hasTo('client@test.com'));
    }

    public function test_admin_can_delete_pending_order(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->admin)->delete(route('admin.orders.destroy', $order));

        $response->assertRedirect(route('admin.orders.index'));
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_admin_cannot_delete_non_pending_order(): void
    {
        $order = Order::factory()->create(['status' => 'processing']);

        $response = $this->actingAs($this->admin)->delete(route('admin.orders.destroy', $order));

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }
}
