<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->withSum(['orderItems as total_sold' => fn ($q) => $q->whereHas('order', fn ($o) => $o->whereIn('status', ['processing', 'completed']))], 'quantity');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Tri
        $sortable = ['name', 'stock_quantity', 'is_active', 'created_at'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $dir);

        $products = $query->paginate(20)->withQueryString();
        $categories = ProductCategory::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'team_recommendation' => 'nullable|string|max:1000',
            'benefits' => 'nullable|string',
            'usage_instructions' => 'nullable|string',
            'composition' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'unit_measure' => 'nullable|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_status' => 'required|in:instock,outofstock,onbackorder',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        $product = Product::create($validated);

        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            $media = $this->storeMedia($request->file('featured_image'));
            $product->featured_image_id = $media->id;
            $product->save();
        }

        $validGallery = array_filter((array) $request->file('gallery_images', []), fn ($f) => $f && $f->isValid());
        if (!empty($validGallery)) {
            $galleryIds = [];
            foreach ($validGallery as $file) {
                $galleryIds[] = $this->storeMedia($file)->id;
            }
            $product->gallery_image_ids = $galleryIds;
            $product->save();
        }

        return redirect()->route('admin.products.index')->with('success', 'Produit cree avec succes.');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'team_recommendation' => 'nullable|string|max:1000',
            'benefits' => 'nullable|string',
            'usage_instructions' => 'nullable|string',
            'composition' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'unit_measure' => 'nullable|string|max:50',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_status' => 'required|in:instock,outofstock,onbackorder',
            'category_id' => 'nullable|exists:product_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        $product->update($validated);

        // Handle featured image removal
        if ($request->input('remove_featured_image') == '1') {
            $product->featured_image_id = null;
            $product->save();
        } elseif ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            $media = $this->storeMedia($request->file('featured_image'));
            $product->featured_image_id = $media->id;
            $product->save();
        }

        // Handle gallery removal and additions
        $galleryIds = $product->gallery_image_ids ?? [];

        if ($request->filled('gallery_remove')) {
            $toRemove = array_map('intval', $request->input('gallery_remove'));
            $galleryIds = array_values(array_diff($galleryIds, $toRemove));
        }

        $validGallery = array_filter((array) $request->file('gallery_images', []), fn ($f) => $f && $f->isValid());
        foreach ($validGallery as $file) {
            $galleryIds[] = $this->storeMedia($file)->id;
        }

        if ($request->filled('gallery_remove') || !empty($validGallery)) {
            $product->gallery_image_ids = array_values($galleryIds);
            $product->save();
        }

        return redirect()->route('admin.products.index')->with('success', 'Produit mis a jour.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        return response()->json(['is_featured' => $product->is_featured]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produit supprime.');
    }

    private function storeMedia(UploadedFile $file): Media
    {
        $filename = Str::uuid() . '.webp';
        $finalPath = storage_path('app/public/media/' . $filename);

        // Redimensionne (max 1200px) et convertit en WebP
        Image::make($file->getRealPath())
            ->resize(900, 900, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 78)
            ->save($finalPath);

        [$width, $height] = getimagesize($finalPath) ?: [null, null];

        return Media::create([
            'filename'          => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'disk'              => 'public',
            'path'              => 'media/' . $filename,
            'url'               => '/storage/media/' . $filename,
            'mime_type'         => 'image/webp',
            'size'              => filesize($finalPath) ?: 0,
            'width'             => $width,
            'height'            => $height,
            'alt'               => '',
            'title'             => '',
        ]);
    }
}
