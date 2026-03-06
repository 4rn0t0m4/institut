<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductReview;
use App\Models\StockNotification;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Redirection ancien format ?categorie= vers nouveau chemin
        if ($request->filled('categorie')) {
            $cat = ProductCategory::where('slug', $request->categorie)->first();
            if ($cat) {
                return redirect($cat->url(), 301);
            }
        }

        $categories = ProductCategory::root()
            ->where('slug', '!=', 'non-classe')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $query = Product::with(['category', 'featuredImage', 'brand'])
            ->visibleTo(auth()->user())
            ->orderBy('name');

        $currentCategory = null;

        // Filtre marque
        $brands = Brand::whereHas('products', fn ($q) => $q->active())
            ->withCount(['products' => fn ($q) => $q->active()])
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
        $tags = ProductTag::whereHas('products', fn ($q) => $q->active())
            ->withCount(['products' => fn ($q) => $q->active()])
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

    public function categoryOrProduct(string $parent, ?string $child = null)
    {
        // /boutique/{parent} — catégorie parente ou produit sans sous-catégorie
        if (!$child) {
            $category = ProductCategory::where('slug', $parent)->first();
            if ($category) {
                return $this->indexWithCategory($category);
            }

            // Fallback : c'est peut-être un produit directement sous /boutique/
            return $this->showProduct($parent);
        }

        // /boutique/{parent}/{child} — sous-catégorie ou produit d'une catégorie parente
        $childCategory = ProductCategory::where('slug', $child)
            ->whereHas('parent', fn ($q) => $q->where('slug', $parent))
            ->first();

        if ($childCategory) {
            return $this->indexWithCategory($childCategory);
        }

        // C'est un produit sous une catégorie parente : /boutique/{category}/{product}
        return $this->showProduct($child, $parent);
    }

    public function show(string $parent, string $child, string $productSlug)
    {
        return $this->showProduct($productSlug, $child, $parent);
    }

    private function showProduct(string $slug, ?string $categorySlug = null, ?string $parentSlug = null)
    {
        $product = Product::where('slug', $slug)
            ->with(['category.parent', 'brand', 'tags', 'addonAssignments.addon.group'])
            ->visibleTo(auth()->user())
            ->firstOrFail();

        // Vérifier la cohérence de l'URL et rediriger si nécessaire
        $canonicalUrl = $product->url();
        if (url()->current() !== $canonicalUrl) {
            return redirect($canonicalUrl, 301);
        }

        // Produits similaires (même catégorie)
        $related = Product::with('featuredImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(4)
            ->get();

        $reviews = $product->approvedReviews()->latest()->get();

        return view('shop.show', compact('product', 'related', 'reviews'));
    }

    private function indexWithCategory(ProductCategory $category)
    {
        $request = request();

        $categories = ProductCategory::root()
            ->where('slug', '!=', 'non-classe')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $query = Product::with(['category', 'featuredImage', 'brand'])
            ->visibleTo(auth()->user())
            ->orderBy('name');

        $currentCategory = $category;
        $query->whereIn('category_id', $currentCategory->familyIds());

        $brands = Brand::whereHas('products', fn ($q) => $q->active())
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get();
        $currentBrand = null;
        if ($request->filled('marque')) {
            $currentBrand = Brand::where('slug', $request->marque)->first();
            if ($currentBrand) {
                $query->where('brand_id', $currentBrand->id);
            }
        }

        $tags = ProductTag::whereHas('products', fn ($q) => $q->active())
            ->withCount(['products' => fn ($q) => $q->active()])
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

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(24)->withQueryString();

        if ($request->headers->get('Turbo-Frame') === 'products-grid') {
            return view('shop.partials.grid', compact('products', 'categories', 'currentCategory'));
        }

        return view('shop.index', compact('products', 'categories', 'currentCategory', 'brands', 'currentBrand', 'tags', 'currentTag', 'selectedTagSlugs'));
    }

    public function stockNotify(Request $request, Product $product)
    {
        $request->validate(['email' => 'required|email']);

        StockNotification::firstOrCreate([
            'product_id' => $product->id,
            'email' => strtolower(trim($request->email)),
        ]);

        return back()->with('stock_alert', 'Vous serez prévenu(e) dès que ce produit sera de nouveau disponible.');
    }

    public function storeReview(Request $request, Product $product)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:100',
            'author_email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:2000',
        ]);

        $user = auth()->user();
        $isVerifiedBuyer = false;

        if ($user) {
            $isVerifiedBuyer = $user->orders()
                ->where('status', 'completed')
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();
        }

        ProductReview::create([
            ...$validated,
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'is_verified_buyer' => $isVerifiedBuyer,
        ]);

        return back()->with('review_success', 'Merci pour votre avis ! Il sera publié après validation.');
    }
}
