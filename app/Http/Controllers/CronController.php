<?php

namespace App\Http\Controllers;

use App\Mail\ReviewRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
}
