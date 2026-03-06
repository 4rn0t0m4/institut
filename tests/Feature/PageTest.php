<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_page_is_accessible(): void
    {
        $page = Page::factory()->create(['slug' => 'mentions-legales']);

        $response = $this->get('/mentions-legales');

        $response->assertStatus(200);
        $response->assertSee($page->title);
    }

    public function test_draft_page_returns_404(): void
    {
        Page::factory()->draft()->create(['slug' => 'brouillon']);

        $this->get('/brouillon')->assertStatus(404);
    }

    public function test_nested_page_with_parent(): void
    {
        $parent = Page::factory()->create(['slug' => 'juridique']);
        $child = Page::factory()->create(['slug' => 'cgv', 'parent_id' => $parent->id]);

        $response = $this->get('/juridique/cgv');

        $response->assertStatus(200);
        $response->assertSee($child->title);
    }

    public function test_flat_url_redirects_to_nested_for_child_page(): void
    {
        $parent = Page::factory()->create(['slug' => 'juridique']);
        Page::factory()->create(['slug' => 'cgv', 'parent_id' => $parent->id]);

        $response = $this->get('/cgv');

        $response->assertRedirect('/juridique/cgv');
    }

    public function test_nonexistent_page_returns_404(): void
    {
        $this->get('/page-inexistante')->assertStatus(404);
    }
}
