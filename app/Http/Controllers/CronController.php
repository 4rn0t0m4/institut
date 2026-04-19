<?php

namespace App\Http\Controllers;

use App\Mail\AbandonedCartReminder;
use App\Mail\ReviewRequest;
use App\Models\DiscountRule;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Endpoint web pour les tâches cron sur OVH mutualisé.
 *
 * Sur OVH mutualisé, les commandes artisan ne peuvent pas faire
 * d'appels réseau (emails, API). Ce contrôleur expose les tâches
 * planifiées via HTTP, protégées par un token secret.
 *
 * Cron OVH : curl -s https://institutcorpsacoeur.fr/cron/review-requests?token=XXX
 */
class CronController extends Controller
{
    public function reviewRequests(Request $request): JsonResponse
    {
        if ($request->query('token') !== config('app.cron_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $orders = Order::whereNotNull('shipped_at')
            ->whereNull('review_requested_at')
            ->where('shipped_at', '<=', now()->subDays(7))
            ->whereIn('status', ['shipped', 'processing', 'completed'])
            ->with(['items.product.featuredImage'])
            ->get();

        $sent = 0;

        foreach ($orders as $order) {
            try {
                Mail::to($order->billing_email)->send(new ReviewRequest($order));
                Mail::to('arnotoma@gmail.com')->send(new ReviewRequest($order));
                $order->update(['review_requested_at' => now()]);
                $sent++;
                Log::info("Cron review-requests: email envoyé pour #{$order->number}");
            } catch (\Throwable $e) {
                Log::error("Cron review-requests: échec pour #{$order->number}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['sent' => $sent]);
    }

    public function abandonedCarts(Request $request): JsonResponse
    {
        if ($request->query('token') !== config('app.cron_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $orders = Order::where('status', 'pending')
            ->whereNull('paid_at')
            ->whereNull('abandoned_cart_reminded_at')
            ->where('created_at', '<=', now()->subDays(2))
            ->with(['items.product.featuredImage'])
            ->get();

        $sent = 0;

        foreach ($orders as $order) {
            try {
                // Créer un code promo unique valable 7 jours
                $code = 'RETOUR-'.strtoupper(Str::random(6));

                DiscountRule::create([
                    'name' => "Relance commande #{$order->number}",
                    'coupon_code' => $code,
                    'is_active' => true,
                    'type' => 'coupon',
                    'discount_type' => 'percentage',
                    'discount_amount' => 10,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays(7),
                    'stackable' => false,
                    'sort_order' => 99,
                ]);

                Mail::to($order->billing_email)->send(new AbandonedCartReminder($order, $code));
                Mail::to('arnotoma@gmail.com')->send(new AbandonedCartReminder($order, $code));

                $order->update(['abandoned_cart_reminded_at' => now()]);
                $sent++;

                Log::info("Cron abandoned-carts: email envoyé pour #{$order->number}, code {$code}");
            } catch (\Throwable $e) {
                Log::error("Cron abandoned-carts: échec pour #{$order->number}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['sent' => $sent]);
    }
}
