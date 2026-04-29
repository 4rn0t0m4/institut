<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    private const TVA_RATE = 0.20;

    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $products = $this->getProductSales($start, $end);

        $totals = [
            'quantity' => $products->sum('total_quantity'),
            'total_ttc' => $products->sum('total_ttc'),
            'total_ht' => $products->sum('total_ht'),
        ];

        return view('admin.exports.index', compact('products', 'month', 'totals'));
    }

    public function csv(Request $request): StreamedResponse
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $products = $this->getProductSales($start, $end);
        $monthLabel = $start->translatedFormat('F Y');

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 pour Excel

            fputcsv($handle, ['Produit', 'SKU', 'Quantité', 'Prix unit. TTC', 'Total TTC', 'Total HT'], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->product_name,
                    $product->sku ?? '',
                    $product->total_quantity,
                    number_format($product->unit_price, 2, ',', ''),
                    number_format($product->total_ttc, 2, ',', ''),
                    number_format($product->total_ht, 2, ',', ''),
                ], ';');
            }

            fclose($handle);
        }, "ventes-{$month}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function getProductSales(Carbon $start, Carbon $end)
    {
        return OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['processing', 'shipped', 'completed'])
            ->whereBetween('orders.paid_at', [$start, $end])
            ->selectRaw('
                order_items.product_name,
                order_items.sku,
                order_items.unit_price,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total) as total_ttc,
                ROUND(SUM(order_items.total) / ?, 2) as total_ht
            ', [1 + self::TVA_RATE])
            ->groupBy('order_items.product_name', 'order_items.sku', 'order_items.unit_price')
            ->orderByDesc('total_quantity')
            ->get();
    }
}
