<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Page;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'orders_count' => Order::whereIn('status', ['processing', 'completed'])->count(),
            'revenue' => Order::whereIn('status', ['processing', 'completed'])->sum('total'),
            'products_count' => Product::count(),
            'pages_count' => Page::count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Google Analytics data
        $analyticsData = null;
        $analyticsConfigured = config('analytics.property_id') && file_exists(storage_path('app/analytics/service-account-credentials.json'));

        if ($analyticsConfigured) {
            try {
                $analyticsData = [
                    'visitors_today' => Analytics::fetchTotalVisitorsAndPageViews(Period::days(1)),
                    'visitors_7days' => Analytics::fetchTotalVisitorsAndPageViews(Period::days(7)),
                    'visitors_30days' => Analytics::fetchTotalVisitorsAndPageViews(Period::days(30)),
                    'top_pages' => Analytics::fetchMostVisitedPages(Period::days(30), maxResults: 10),
                    'top_referrers' => Analytics::fetchTopReferrers(Period::days(30), maxResults: 10),
                    'top_browsers' => Analytics::fetchTopBrowsers(Period::days(30), maxResults: 5),
                ];
            } catch (\Exception $e) {
                $analyticsError = $e->getMessage();
            }
        }

        return view('admin.dashboard.index', compact(
            'metrics',
            'recentOrders',
            'analyticsData',
            'analyticsConfigured',
        ))->with('analyticsError', $analyticsError ?? null);
    }
}
