<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Quiz;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with(['category', 'featuredImage', 'brand'])
            ->withCount(['approvedReviews as reviews_count'])
            ->withAvg('approvedReviews as reviews_avg', 'rating')
            ->limit(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('is_active', true)
                ->with(['category', 'featuredImage', 'brand'])
                ->withCount(['approvedReviews as reviews_count'])
                ->withAvg('approvedReviews as reviews_avg', 'rating')
                ->limit(8)
                ->get();
        }

        $categories = ProductCategory::whereNull('parent_id')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $quiz = Quiz::first();

        return view('home', compact('featuredProducts', 'categories', 'quiz'));
    }
}
