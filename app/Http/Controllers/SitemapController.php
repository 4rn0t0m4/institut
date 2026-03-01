<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $pages = Page::where('status', 'published')
            ->whereNotIn('slug', ['boutique', 'panier', 'commander', 'mon-compte', 'index', 'quizz'])
            ->with('parent')
            ->get();

        $products = Product::where('is_active', true)->get();
        $posts = Post::where('status', 'published')->orderByDesc('published_at')->get();

        $content = view('sitemap', compact('pages', 'products', 'posts'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
