<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->orderBy('name')->get();

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:brands,slug'],
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'image' => 'nullable|image|max:2048',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        unset($validated['image']);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image_path'] = $request->file('image')->store('brands', 'public');
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Marque creee avec succes.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:brands,slug,'.$brand->id],
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'image' => 'nullable|image|max:2048',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        unset($validated['image']);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image_path'] = $request->file('image')->store('brands', 'public');
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')->with('success', 'Marque mise a jour.');
    }

    public function destroy(Brand $brand)
    {
        $brand->products()->update(['brand_id' => null]);
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Marque supprimee.');
    }
}
