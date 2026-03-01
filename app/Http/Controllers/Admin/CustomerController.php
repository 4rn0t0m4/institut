<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['orders' => fn ($q) => $q->whereIn('status', ['processing', 'completed'])])
            ->withSum(['orders as orders_total' => fn ($q) => $q->whereIn('status', ['processing', 'completed'])], 'total')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $orders = $customer->orders()->latest()->paginate(15);

        $stats = [
            'orders_count' => $customer->orders()->whereIn('status', ['processing', 'completed'])->count(),
            'total_spent'  => $customer->orders()->whereIn('status', ['processing', 'completed'])->sum('total'),
        ];

        return view('admin.customers.show', compact('customer', 'orders', 'stats'));
    }
}
