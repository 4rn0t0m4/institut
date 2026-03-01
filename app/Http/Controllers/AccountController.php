<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('account.index', compact('orders'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function order(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('items.product', 'items.addons');

        return view('account.order', compact('order'));
    }

    public function editProfile()
    {
        return view('account.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return back()->with('success', 'Profil mis à jour.');
    }

    public function editAddress()
    {
        return view('account.address');
    }

    public function updateAddress(Request $request)
    {
        $data = $request->validate([
            'first_name'           => 'required|string|max:100',
            'last_name'            => 'required|string|max:100',
            'phone'                => 'nullable|string|max:30',
            'address_1'            => 'required|string|max:255',
            'address_2'            => 'nullable|string|max:255',
            'city'                 => 'required|string|max:100',
            'postcode'             => 'required|string|max:20',
            'country'              => 'required|string|size:2',
            'shipping_first_name'  => 'nullable|string|max:100',
            'shipping_last_name'   => 'nullable|string|max:100',
            'shipping_address_1'   => 'nullable|string|max:255',
            'shipping_address_2'   => 'nullable|string|max:255',
            'shipping_city'        => 'nullable|string|max:100',
            'shipping_postcode'    => 'nullable|string|max:20',
            'shipping_country'     => 'nullable|string|size:2',
        ]);

        // Si pas d'adresse de livraison renseignée, vider les champs
        if (!$request->filled('shipping_address_1')) {
            $data = array_merge($data, [
                'shipping_first_name' => null,
                'shipping_last_name'  => null,
                'shipping_address_1'  => null,
                'shipping_address_2'  => null,
                'shipping_city'       => null,
                'shipping_postcode'   => null,
                'shipping_country'    => null,
            ]);
        }

        auth()->user()->update($data);

        return back()->with('success', 'Coordonnées mises à jour.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe modifié.');
    }
}
