<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Page;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'orders_count' => Order::count(),
            'revenue' => Order::where('status', 'completed')->sum('total'),
            'products_count' => Product::count(),
            'pages_count' => Page::count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard.index', compact('metrics', 'recentOrders'));
    }
}
