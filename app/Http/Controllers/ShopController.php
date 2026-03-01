<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $query = Product::with(['category', 'featuredImage', 'brand'])
            ->orderBy('name');

        if (!auth()->user()?->is_admin) {
            $query->where('is_active', true);
        }

        // Filtre catégorie (inclut parent + enfants dans les deux sens)
        $currentCategory = null;
        if ($request->filled('categorie')) {
            $currentCategory = ProductCategory::where('slug', $request->categorie)->first();
            if ($currentCategory) {
                $categoryIds = collect([$currentCategory->id]);

                // Ajouter les sous-catégories
                $categoryIds = $categoryIds->merge($currentCategory->children->pluck('id'));

                // Ajouter la catégorie parente si c'est une sous-catégorie
                if ($currentCategory->parent_id) {
                    $categoryIds->push($currentCategory->parent_id);
                }

                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filtre recherche
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(24)->withQueryString();

        // Turbo Frame : retourner seulement la grille si demandé
        if ($request->headers->get('Turbo-Frame') === 'products-grid') {
            return view('shop.partials.grid', compact('products', 'categories', 'currentCategory'));
        }

        return view('shop.index', compact('products', 'categories', 'currentCategory'));
    }

    public function show(string $slug)
    {
        $query = Product::where('slug', $slug)
            ->with(['category', 'brand', 'addonAssignments.addon.group']);

        if (!auth()->user()?->is_admin) {
            $query->where('is_active', true);
        }

        $product = $query->firstOrFail();

        // Produits similaires (même catégorie)
        $related = Product::with('featuredImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
