<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CreditNoteMail;
use App\Mail\NewOrderAdmin;
use App\Mail\OrderConfirmation;
use App\Mail\OrderShipped;
use App\Models\CreditNote;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BoxtalShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Refund;

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

        $paid = Order::whereIn('status', ['processing', 'shipped', 'completed']);
        $metrics = [
            'total_orders' => $paid->count(),
            'revenue' => $paid->sum('total'),
            'items_sold' => OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['processing', 'shipped', 'completed']))->sum('quantity'),
            'average_order' => $paid->count() > 0 ? $paid->avg('total') : 0,
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'metrics'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.addons', 'creditNotes']);

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
            'status' => 'required|string|in:pending,processing,shipped,completed,cancelled,refunded',
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
            Mail::to('arnotoma@gmail.com')->send(new OrderShipped($order));
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Commande mise à jour.');
    }

    public function resendEmails(Order $order)
    {
        $order->load('items');

        try {
            Mail::to($order->billing_email)->send(new OrderConfirmation($order));
            Mail::to(config('mail.admin_address', config('mail.from.address')))->send(new NewOrderAdmin($order));
            Log::info("Emails renvoyés manuellement pour commande #{$order->number}");

            return redirect()->route('admin.orders.show', $order)->with('success', 'Emails de confirmation renvoyés avec succès.');
        } catch (\Exception $e) {
            Log::error("Échec renvoi emails pour commande #{$order->number}", ['error' => $e->getMessage()]);

            return redirect()->route('admin.orders.show', $order)->with('error', "Erreur lors de l'envoi : {$e->getMessage()}");
        }
    }

    public function createShipment(Request $request, Order $order, BoxtalShippingService $boxtal)
    {
        if ($order->boxtal_shipping_order_id) {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Une expédition Boxtal existe déjà pour cette commande.');
        }

        $validated = $request->validate([
            'weight' => 'nullable|numeric|min:0.01|max:30',
            'length' => 'nullable|integer|min:1|max:200',
            'width' => 'nullable|integer|min:1|max:200',
            'height' => 'nullable|integer|min:1|max:200',
            'shippingOfferCode' => 'nullable|string|max:50',
        ]);

        $overrides = array_filter($validated);

        $result = $boxtal->createShipment($order, $overrides);

        if ($result['success']) {
            $order->update([
                'boxtal_shipping_order_id' => $result['shipping_order_id'],
            ]);

            return redirect()->route('admin.orders.show', $order)->with('success', 'Expédition Boxtal créée avec succès (ID : '.$result['shipping_order_id'].').');
        }

        return redirect()->route('admin.orders.show', $order)->with('error', 'Erreur Boxtal : '.$result['error']);
    }

    public function label(Order $order, BoxtalShippingService $boxtal)
    {
        if (! $order->boxtal_shipping_order_id) {
            return redirect()->route('admin.orders.show', $order)->with('error', 'Aucune expédition Boxtal pour cette commande.');
        }

        $labelUrl = $boxtal->fetchLabelUrl($order->boxtal_shipping_order_id);

        if ($labelUrl) {
            return redirect()->away($labelUrl);
        }

        return redirect()->route('admin.orders.show', $order)->with('error', 'Étiquette non disponible. Vérifiez sur le dashboard Boxtal.');
    }

    public function resetShipment(Order $order)
    {
        $order->update(['boxtal_shipping_order_id' => null]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Expédition Boxtal dissociée. Vous pouvez en créer une nouvelle.');
    }

    public function refund(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$order->total,
            'reason' => 'nullable|string|max:500',
            'stripe_refund' => 'nullable|boolean',
        ]);

        $stripeRefunded = false;
        $stripeRefundId = null;

        if ($request->boolean('stripe_refund') && $order->stripe_payment_intent_id) {
            try {
                Stripe::setApiKey(config('cashier.secret'));
                $refund = Refund::create([
                    'payment_intent' => $order->stripe_payment_intent_id,
                    'amount' => (int) round($validated['amount'] * 100),
                ]);
                $stripeRefunded = true;
                $stripeRefundId = $refund->id;
                Log::info("Remboursement Stripe effectué pour commande #{$order->number}", ['refund_id' => $refund->id]);
            } catch (\Exception $e) {
                Log::error("Échec remboursement Stripe pour commande #{$order->number}", ['error' => $e->getMessage()]);

                return redirect()->route('admin.orders.show', $order)->with('error', "Erreur Stripe : {$e->getMessage()}");
            }
        }

        $creditNote = CreditNote::create([
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'stripe_refunded' => $stripeRefunded,
            'stripe_refund_id' => $stripeRefundId,
        ]);

        $order->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);

        try {
            Mail::to($order->billing_email)->send(new CreditNoteMail($creditNote));
            Log::info("Email avoir envoyé pour commande #{$order->number}");
        } catch (\Exception $e) {
            Log::error("Échec envoi email avoir pour commande #{$order->number}", ['error' => $e->getMessage()]);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', "Avoir {$creditNote->number} créé — {$validated['amount']} € remboursés.");
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
