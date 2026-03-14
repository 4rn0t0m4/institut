<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $pages = Page::where('status', 'published')
            ->whereNotIn('slug', ['boutique', 'panier', 'commander', 'mon-compte', 'index', 'quizz'])
            ->with('parent')
            ->get();

        $products = Product::where('is_active', true)->with(['category.parent'])->get();

        $categories = ProductCategory::with('parent')->orderBy('sort_order')->get();

        $content = view('sitemap', compact('pages', 'products', 'categories'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
