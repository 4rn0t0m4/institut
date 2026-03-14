<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        // Pages parentes triées par sort_order, avec enfants triés par sort_order
        $parents = Page::whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')->orderBy('title')])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        // Aplatir en liste ordonnée parent → enfants
        $pages = collect();
        foreach ($parents as $parent) {
            $pages->push($parent);
            foreach ($parent->children as $child) {
                $pages->push($child);
            }
        }

        // Ajouter les pages orphelines (parent_id pointe vers un parent supprimé)
        $allIds = $pages->pluck('id');
        $orphans = Page::whereNotNull('parent_id')
            ->whereNotIn('parent_id', $parents->pluck('id'))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();
        foreach ($orphans as $orphan) {
            $pages->push($orphan);
        }

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $parents = Page::whereNull('parent_id')->orderBy('title')->get();

        return view('admin.pages.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:pages,slug'],
            'content' => 'nullable|string|max:50000',
            'status' => 'required|string|in:published,draft',
            'parent_id' => 'nullable|exists:pages,id',
            'template' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($validated['status'] === 'published' && empty($request->published_at)) {
            $validated['published_at'] = now();
        }

        Page::create($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page creee.');
    }

    public function edit(Page $page)
    {
        $parents = Page::whereNull('parent_id')
            ->where('id', '!=', $page->id)
            ->orderBy('title')
            ->get();

        return view('admin.pages.edit', compact('page', 'parents'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:pages,slug,'.$page->id],
            'content' => 'nullable|string|max:50000',
            'status' => 'required|string|in:published,draft',
            'parent_id' => 'nullable|exists:pages,id',
            'template' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($validated['status'] === 'published' && ! $page->published_at) {
            $validated['published_at'] = now();
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page mise a jour.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page supprimee.');
    }
}
