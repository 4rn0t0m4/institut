<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::root()
            ->where('slug', '!=', 'non-classe')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $query = Product::with(['category', 'featuredImage', 'brand'])
            ->visibleTo(auth()->user())
            ->orderBy('name');

        // Filtre catégorie (inclut parent + enfants dans les deux sens)
        $currentCategory = null;
        if ($request->filled('categorie')) {
            $currentCategory = ProductCategory::where('slug', $request->categorie)->first();
            if ($currentCategory) {
                $query->whereIn('category_id', $currentCategory->familyIds());
            }
        }

        // Filtre marque
        $brands = Brand::withCount(['products' => fn ($q) => $q->active()])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();
        $currentBrand = null;
        if ($request->filled('marque')) {
            $currentBrand = Brand::where('slug', $request->marque)->first();
            if ($currentBrand) {
                $query->where('brand_id', $currentBrand->id);
            }
        }

        // Filtre tags (types de peau) — supporte tags[] (multi) et tag (simple)
        $tags = ProductTag::withCount(['products' => fn ($q) => $q->active()])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();
        $currentTag = null;
        $selectedTagSlugs = $request->input('tags', []);
        if ($request->filled('tag')) {
            $selectedTagSlugs = [$request->tag];
        }
        if (!empty($selectedTagSlugs)) {
            $selectedTagIds = ProductTag::whereIn('slug', $selectedTagSlugs)->pluck('id');
            if ($selectedTagIds->isNotEmpty()) {
                $query->whereHas('tags', fn ($q) => $q->whereIn('product_tag_id', $selectedTagIds));
            }
            $currentTag = ProductTag::whereIn('slug', $selectedTagSlugs)->first();
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

        return view('shop.index', compact('products', 'categories', 'currentCategory', 'brands', 'currentBrand', 'tags', 'currentTag', 'selectedTagSlugs'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['category.parent', 'brand', 'tags', 'addonAssignments.addon.group'])
            ->visibleTo(auth()->user())
            ->firstOrFail();

        // Produits similaires (même catégorie)
        $related = Product::with('featuredImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
