<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReview::with('product')
            ->latest();

        if ($request->filled('status')) {
            match ($request->status) {
                'pending' => $query->where('is_approved', false),
                'approved' => $query->where('is_approved', true),
                default => null,
            };
        }

        $reviews = $query->paginate(25)->withQueryString();
        $pendingCount = ProductReview::where('is_approved', false)->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount'));
    }

    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Avis approuve.');
    }

    public function reject(ProductReview $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Avis rejete.');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return back()->with('success', 'Avis supprime.');
    }
}
