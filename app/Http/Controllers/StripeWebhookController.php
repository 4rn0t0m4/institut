<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderAdmin;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(config('cashier.secret'));

        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('cashier.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature invalid', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleSessionCompleted($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }

    private function handleSessionCompleted(object $session): void
    {
        $order = Order::where('stripe_session_id', $session->id)->first();

        if (!$order) {
            Log::error('Stripe webhook: order not found', ['session_id' => $session->id]);
            return;
        }

        $order->update([
            'status'                    => 'processing',
            'stripe_payment_intent_id'  => $session->payment_intent,
            'paid_at'                   => now(),
        ]);

        Log::info("Commande #{$order->number} payée via Stripe.");

        // Décrémentation du stock
        $order->load('items');
        $this->decrementStock($order);

        // Envoi des emails
        Mail::to($order->billing_email)->send(new OrderConfirmation($order));
        Mail::to(config('mail.admin_address', config('mail.from.address')))->send(new NewOrderAdmin($order));
    }

    private function decrementStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $updated = Product::where('id', $item->product_id)
                ->where('manage_stock', true)
                ->where('stock_quantity', '>=', $item->quantity)
                ->update([
                    'stock_quantity' => DB::raw("stock_quantity - {$item->quantity}"),
                ]);

            if ($updated) {
                // Mark out of stock if quantity reaches 0
                Product::where('id', $item->product_id)
                    ->where('stock_quantity', '<=', 0)
                    ->update(['stock_status' => 'outofstock']);
            }
        }
    }

    private function handlePaymentFailed(object $paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update(['status' => 'failed']);
            Log::warning("Commande #{$order->number} paiement échoué.");
        }
    }
}
