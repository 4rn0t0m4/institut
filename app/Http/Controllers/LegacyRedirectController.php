<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;

class LegacyRedirectController extends Controller
{
    public function product(string $slug)
    {
        $product = Product::with(['category.parent'])->where('slug', $slug)->first();

        if (!$product) {
            abort(404);
        }

        return redirect($product->url(), 301);
    }

    public function category(string $slug)
    {
        $category = ProductCategory::with('parent')->where('slug', $slug)->first();

        if (!$category) {
            return redirect('/boutique', 301);
        }

        $path = $category->parent
            ? '/boutique/' . $category->parent->slug . '/' . $category->slug
            : '/boutique/' . $category->slug;

        return redirect($path, 301);
    }
}
