<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShippingController extends Controller
{
    public function index()
    {
        $shipping = [
            'colissimo_price'              => Setting::get('shipping_colissimo_price', config('shipping.methods.colissimo.price')),
            'boxtal_price'                 => Setting::get('shipping_boxtal_price', config('shipping.methods.boxtal.price')),
            'boxtal_price_international'   => Setting::get('shipping_boxtal_price_international', config('shipping.methods.boxtal.price_international')),
            'free_threshold_fr'            => Setting::get('shipping_free_threshold_fr', config('shipping.zones.FR.free_shipping_threshold')),
            'free_threshold_international' => Setting::get('shipping_free_threshold_international', config('shipping.zones.international.free_shipping_threshold')),
        ];

        return view('admin.shipping.index', compact('shipping'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'colissimo_price'              => 'required|numeric|min:0',
            'boxtal_price'                 => 'required|numeric|min:0',
            'boxtal_price_international'   => 'required|numeric|min:0',
            'free_threshold_fr'            => 'required|numeric|min:0',
            'free_threshold_international' => 'required|numeric|min:0',
        ]);

        Setting::set('shipping_colissimo_price', $validated['colissimo_price'], 'shipping');
        Setting::set('shipping_boxtal_price', $validated['boxtal_price'], 'shipping');
        Setting::set('shipping_boxtal_price_international', $validated['boxtal_price_international'], 'shipping');
        Setting::set('shipping_free_threshold_fr', $validated['free_threshold_fr'], 'shipping');
        Setting::set('shipping_free_threshold_international', $validated['free_threshold_international'], 'shipping');

        Cache::forget('shipping_settings');

        return redirect()->route('admin.shipping.index')->with('success', 'Frais de livraison mis à jour.');
    }
}
