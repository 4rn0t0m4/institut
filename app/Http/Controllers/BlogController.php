<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()->orderByDesc('published_at');

        if ($request->filled('categorie')) {
            $query->whereJsonContains('categories', $request->categorie);
        }

        $posts = $query->paginate(12)->withQueryString();

        if ($request->headers->get('Turbo-Frame') === 'posts-grid') {
            return view('blog.partials.grid', compact('posts'));
        }

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
