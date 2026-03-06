<?php

namespace Tests\Feature\Admin;

use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_list_reviews(): void
    {
        ProductReview::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.reviews.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_approve_review(): void
    {
        $review = ProductReview::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.reviews.approve', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', ['id' => $review->id, 'is_approved' => true]);
    }

    public function test_admin_can_reject_review(): void
    {
        $review = ProductReview::factory()->create(['is_approved' => true]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.reviews.reject', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', ['id' => $review->id, 'is_approved' => false]);
    }

    public function test_admin_can_delete_review(): void
    {
        $review = ProductReview::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.reviews.destroy', $review));

        $response->assertRedirect();
        $this->assertDatabaseMissing('product_reviews', ['id' => $review->id]);
    }
}
