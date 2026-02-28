<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Post;
use App\Models\Quiz;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('category')
            ->limit(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('is_active', true)
                ->with('category')
                ->limit(8)
                ->get();
        }

        $categories = ProductCategory::whereNull('parent_id')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $latestPosts = Post::published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $quiz = Quiz::first();

        return view('home', compact('featuredProducts', 'categories', 'latestPosts', 'quiz'));
    }
}
