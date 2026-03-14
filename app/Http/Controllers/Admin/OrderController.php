<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('billing_email', 'like', "%{$search}%")
                    ->orWhere('billing_last_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $paid = Order::whereIn('status', ['processing', 'completed']);
        $metrics = [
            'total_orders' => $paid->count(),
            'revenue' => $paid->sum('total'),
            'items_sold' => OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['processing', 'completed']))->sum('quantity'),
            'average_order' => $paid->count() > 0 ? $paid->avg('total') : 0,
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'metrics'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.addons']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'tracking_carrier' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string|max:1000',
        ]);

        $oldTrackingNumber = $order->tracking_number;

        $order->update($validated);

        // Envoyer l'email d'expédition quand un numéro de suivi est ajouté ou modifié
        if (($validated['tracking_number'] ?? null) && $validated['tracking_number'] !== $oldTrackingNumber) {
            $order->update(['shipped_at' => now()]);
            Mail::to($order->billing_email)->send(new OrderShipped($order));
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Commande mise à jour.');
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Seules les commandes non reglees peuvent etre supprimees.');
        }

        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Commande supprimee.');
    }
}
