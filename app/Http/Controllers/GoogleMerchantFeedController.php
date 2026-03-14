<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;

class GoogleMerchantFeedController extends Controller
{
    // ID de la catégorie Bijoux et ses sous-catégories (exclues du flux)
    private const BIJOUX_CATEGORY_ID = 5;

    public function index()
    {
        $excludedCategoryIds = $this->getBijouCategoryIds();

        $products = Product::with(['category.parent', 'brand', 'featuredImage'])
            ->where('is_active', true)
            ->whereNotIn('category_id', $excludedCategoryIds)
            ->get();

        return response()
            ->view('google-merchant-feed', compact('products'))
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function getBijouCategoryIds(): array
    {
        $parent = self::BIJOUX_CATEGORY_ID;
        $children = ProductCategory::where('parent_id', $parent)->pluck('id')->toArray();

        return array_merge([$parent], $children);
    }
}
