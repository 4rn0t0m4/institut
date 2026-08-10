<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published' && ! auth()->user()?->is_admin) {
            abort(404);
        }

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $recommendedProducts = collect();
        if (! empty($post->categories)) {
            $recommendedProducts = Product::where('is_active', true)
                ->whereIn('category_id', $post->categories)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        return view('blog.show', compact('post', 'relatedPosts', 'recommendedProducts'));
    }
}
