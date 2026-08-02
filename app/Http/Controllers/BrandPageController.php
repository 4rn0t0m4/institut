<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;

class BrandPageController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        return view('brands.index', compact('brands'));
    }

    public function show(Brand $brand)
    {
        $products = Product::where('brand_id', $brand->id)
            ->where('is_active', true)
            ->with(['category', 'featuredImage', 'brand'])
            ->withCount(['approvedReviews as reviews_count'])
            ->withAvg('approvedReviews as reviews_avg', 'rating')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('brands.show', compact('brand', 'products'));
    }
}
