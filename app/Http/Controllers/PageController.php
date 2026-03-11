<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        // Support nested slugs: "parent-slug/child-slug"
        $segments = explode('/', $slug);
        $lastSlug = end($segments);

        $page = Page::where('slug', $lastSlug)
            ->where('status', 'published')
            ->firstOrFail();

        // URL WP imbriquée mais page sans parent → redirection 301 vers URL plate
        if (count($segments) > 1 && !$page->parent_id) {
            return redirect('/' . $lastSlug, 301);
        }

        // Verify the full path matches the parent hierarchy
        if (count($segments) > 1 && $page->parent) {
            $expectedParentSlug = $segments[0];
            if ($page->parent->slug !== $expectedParentSlug) {
                abort(404);
            }
        }

        // Redirect flat URL to nested URL if page has a parent
        if (count($segments) === 1 && $page->parent_id) {
            $parent = $page->parent;
            if ($parent) {
                return redirect($parent->slug . '/' . $page->slug, 301);
            }
        }

        return view('pages.show', compact('page'));
    }
}
