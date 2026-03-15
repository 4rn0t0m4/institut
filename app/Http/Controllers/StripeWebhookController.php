<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderAdmin;
use App\Mail\OrderConfirmation;
use App\Mail\PaymentFailed;
use App\Models\Order;
use App\Models\Product;
use App\Services\BoxtalConnectService;
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

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('cashier.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature invalid', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            default => null,
        };

        return response('OK', 200);
    }

    private function handlePaymentSucceeded(object $paymentIntent): void
    {
        $order = DB::transaction(function () use ($paymentIntent) {
            // Verrouiller la commande pour éviter le traitement en double
            $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)
                ->lockForUpdate()
                ->first();

            if (! $order) {
                Log::error('Stripe webhook: order not found', ['payment_intent_id' => $paymentIntent->id]);

                return null;
            }

            // Idempotence : déjà traitée (par le webhook ou la page success)
            if ($order->status !== 'pending') {
                return null;
            }

            $order->update([
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            Log::info("Commande #{$order->number} payée via Stripe.");

            // Décrémentation du stock dans la même transaction
            $order->load('items');
            $this->decrementStock($order);

            return $order;
        });

        // Emails + Boxtal Connect envoyés hors transaction (pas besoin de bloquer)
        if ($order) {
            $order->load('items');

            try {
                Mail::to($order->billing_email)->send(new OrderConfirmation($order));
                Log::info("Email confirmation envoyé au client pour commande #{$order->number}");
            } catch (\Exception $e) {
                Log::error("Échec envoi email confirmation client pour commande #{$order->number}", ['error' => $e->getMessage()]);
            }

            try {
                Mail::to(config('mail.admin_address', config('mail.from.address')))->send(new NewOrderAdmin($order));
                Log::info("Email notification admin envoyé pour commande #{$order->number}");
            } catch (\Exception $e) {
                Log::error("Échec envoi email admin pour commande #{$order->number}", ['error' => $e->getMessage()]);
            }

            if ($order->shipping_key === 'boxtal') {
                app(BoxtalConnectService::class)->pushOrder($order);
            }
        }
    }

    private function decrementStock(Order $order): void
    {
        foreach ($order->items as $item) {
            // lockForUpdate implicite via la transaction parente
            $product = Product::where('id', $item->product_id)
                ->where('manage_stock', true)
                ->lockForUpdate()
                ->first();

            if (! $product || $product->stock_quantity < $item->quantity) {
                Log::warning("Stock insuffisant pour produit #{$item->product_id}", [
                    'order' => $order->id,
                    'requested' => $item->quantity,
                    'available' => $product?->stock_quantity,
                ]);

                continue;
            }

            $product->decrement('stock_quantity', $item->quantity);

            if ($product->fresh()->stock_quantity <= 0) {
                $product->update(['stock_status' => 'outofstock']);
            }
        }
    }

    private function handlePaymentFailed(object $paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (! $order) {
            return;
        }

        $order->update(['status' => 'failed']);
        Log::warning("Commande #{$order->number} paiement échoué.");

        Mail::to($order->billing_email)->send(new PaymentFailed($order));
    }
}
