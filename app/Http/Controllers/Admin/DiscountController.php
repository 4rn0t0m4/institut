<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountRule;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = DiscountRule::orderByDesc('created_at')->get();

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRule($request);
        DiscountRule::create($data);

        return redirect()->route('admin.discounts.index')->with('success', 'Code promo créé.');
    }

    public function edit(DiscountRule $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, DiscountRule $discount)
    {
        $data = $this->validateRule($request, $discount->id);
        $discount->update($data);

        return redirect()->route('admin.discounts.index')->with('success', 'Code promo mis à jour.');
    }

    public function destroy(DiscountRule $discount)
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')->with('success', 'Code promo supprimé.');
    }

    private function validateRule(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'coupon_code' => 'nullable|string|max:50|unique:discount_rules,coupon_code'.($ignoreId ? ",$ignoreId" : ''),
            'is_active' => 'nullable|boolean',
            'type' => 'required|in:all_products,category,cart_value,quantity',
            'discount_type' => 'required|in:percentage,flat',
            'discount_amount' => 'required|numeric|min:0',
            'min_cart_value' => 'nullable|numeric|min:0',
            'max_cart_value' => 'nullable|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'stackable' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['stackable'] = $request->boolean('stackable');

        return $data;
    }
}
