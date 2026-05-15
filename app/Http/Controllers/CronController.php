<?php

namespace App\Http\Controllers;

use App\Mail\AbandonedCartReminder;
use App\Mail\NewOrderAdmin;
use App\Mail\OrderConfirmation;
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

        $alreadySentEmails = [];

        foreach ($orders as $order) {
            try {
                // Ne pas renvoyer si ce client a déjà reçu une demande d'avis
                if (in_array($order->billing_email, $alreadySentEmails)) {
                    $order->update(['review_requested_at' => now()]);
                    Log::info("Cron review-requests: #{$order->number} ignoré, client déjà sollicité dans ce batch");
                    continue;
                }

                // Vérifier si ce client a déjà reçu une demande d'avis (autre commande)
                $alreadyRequested = Order::where('billing_email', $order->billing_email)
                    ->where('id', '!=', $order->id)
                    ->whereNotNull('review_requested_at')
                    ->exists();

                if ($alreadyRequested) {
                    $order->update(['review_requested_at' => now()]);
                    Log::info("Cron review-requests: #{$order->number} ignoré, client déjà sollicité");
                    continue;
                }

                Mail::to($order->billing_email)->send(new ReviewRequest($order));
                $order->update(['review_requested_at' => now()]);
                $alreadySentEmails[] = $order->billing_email;
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

        $debug = [
            'now' => now()->toDateTimeString(),
            'threshold' => now()->subDays(2)->toDateTimeString(),
            'cron_token_set' => !empty(config('app.cron_token')),
            'pending_count' => Order::where('status', 'pending')->whereNull('paid_at')->count(),
            'eligible_count' => Order::where('status', 'pending')->whereNull('paid_at')->whereNull('abandoned_cart_reminded_at')->where('created_at', '<=', now()->subDays(2))->count(),
        ];

        $orders = Order::where('status', 'pending')
            ->whereNull('paid_at')
            ->whereNull('abandoned_cart_reminded_at')
            ->where('created_at', '<=', now()->subDays(2))
            ->with(['items.product.featuredImage'])
            ->get();

        $sent = 0;

        foreach ($orders as $order) {
            try {
                // Ne pas relancer si un code promo existe déjà pour cette commande
                if (DiscountRule::where('name', "Relance commande #{$order->number}")->exists()) {
                    $order->update(['abandoned_cart_reminded_at' => now()]);
                    Log::info("Cron abandoned-carts: #{$order->number} ignoré, code déjà créé");
                    continue;
                }

                // Ne pas relancer si le client a passé une commande payée depuis
                if (Order::where('billing_email', $order->billing_email)
                    ->where('id', '!=', $order->id)
                    ->whereNotNull('paid_at')
                    ->where('created_at', '>=', $order->created_at)
                    ->exists()) {
                    $order->update(['abandoned_cart_reminded_at' => now()]);
                    Log::info("Cron abandoned-carts: #{$order->number} ignoré, le client a commandé depuis");
                    continue;
                }

                // Ne pas relancer si ce client a déjà reçu une relance (1 seule par personne)
                if (DiscountRule::where('name', 'like', 'Relance commande #CMD-%')
                    ->whereIn('name', function ($query) use ($order) {
                        $query->select(\DB::raw("CONCAT('Relance commande #', number)"))
                            ->from('orders')
                            ->where('billing_email', $order->billing_email);
                    })->exists()) {
                    $order->update(['abandoned_cart_reminded_at' => now()]);
                    Log::info("Cron abandoned-carts: #{$order->number} ignoré, client déjà relancé");
                    continue;
                }

                // Créer un code promo unique valable 7 jours
                $code = 'RETOUR-'.strtoupper(Str::random(6));

                DiscountRule::create([
                    'name' => "Relance commande #{$order->number}",
                    'coupon_code' => $code,
                    'is_active' => true,
                    'type' => 'all_products',
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

        return response()->json(['sent' => $sent, 'debug' => $debug]);
    }

    public function testMail(Request $request): JsonResponse
    {
        if ($request->query('token') !== config('app.cron_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            Mail::raw('Test envoi email depuis Institut Corps à Coeur - '.now(), function ($message) {
                $message->to('arnotoma@gmail.com')
                    ->subject('Test Brevo - Institut Corps à Coeur');
            });

            return response()->json(['status' => 'ok', 'message' => 'Email envoyé']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /** Renvoie les emails de la dernière commande payée */
    public function resendLastOrder(Request $request): JsonResponse
    {
        if ($request->query('token') !== config('app.cron_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $order = Order::whereNotNull('paid_at')
            ->whereIn('status', ['processing', 'shipped', 'completed'])
            ->with('items')
            ->latest('paid_at')
            ->first();

        if (! $order) {
            return response()->json(['status' => 'error', 'message' => 'Aucune commande payée trouvée']);
        }

        $results = [];

        try {
            Mail::to($order->billing_email)->send(new OrderConfirmation($order));
            $results['client'] = "Envoyé à {$order->billing_email}";
        } catch (\Throwable $e) {
            $results['client'] = "Échec : {$e->getMessage()}";
        }

        try {
            $adminEmail = config('mail.admin_address', config('mail.from.address'));
            Mail::to($adminEmail)->send(new NewOrderAdmin($order));
            $results['admin'] = "Envoyé à {$adminEmail}";
        } catch (\Throwable $e) {
            $results['admin'] = "Échec : {$e->getMessage()}";
        }

        return response()->json([
            'status' => 'ok',
            'order' => $order->number,
            'paid_at' => $order->paid_at->toDateTimeString(),
            'results' => $results,
        ]);
    }
}
