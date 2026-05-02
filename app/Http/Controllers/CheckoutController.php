<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutStoreRequest;
use App\Mail\NewOrderAdmin;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;

use App\Services\CartService;
use App\Services\DiscountEngine;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private DiscountEngine $discount,
        private OrderService $orderService,
    ) {}

    /** Page récapitulatif & formulaire adresse */
    public function index()
    {
        $items = $this->cart->itemsWithProducts();

        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        $subtotal = $this->cart->subtotal();
        $discount = $this->discount->calculate($items, $subtotal);
        $shippingMethods = config('shipping.methods');
        $shippingCountries = config('shipping.countries');
        $shippingZones = config('shipping.zones');

        // Pré-remplir avec les coordonnées du profil ou la dernière commande
        $prefill = [];
        if ($user = auth()->user()) {
            if ($user->first_name && $user->address_1) {
                // Coordonnées enregistrées dans le profil
                $prefill = [
                    'billing_first_name' => $user->first_name,
                    'billing_last_name' => $user->last_name,
                    'billing_email' => $user->email,
                    'billing_phone' => $user->phone,
                    'billing_address_1' => $user->address_1,
                    'billing_address_2' => $user->address_2,
                    'billing_city' => $user->city,
                    'billing_postcode' => $user->postcode,
                    'billing_country' => $user->country,
                ];
                // Adresse de livraison différente
                if ($user->shipping_address_1) {
                    $prefill['shipping_same'] = false;
                    $prefill['shipping_first_name'] = $user->shipping_first_name;
                    $prefill['shipping_last_name'] = $user->shipping_last_name;
                    $prefill['shipping_address_1'] = $user->shipping_address_1;
                    $prefill['shipping_address_2'] = $user->shipping_address_2;
                    $prefill['shipping_city'] = $user->shipping_city;
                    $prefill['shipping_postcode'] = $user->shipping_postcode;
                    $prefill['shipping_country'] = $user->shipping_country;
                }
            } else {
                // Fallback : dernière commande
                $lastOrder = $user->orders()->latest()->first();
                if ($lastOrder) {
                    $prefill = [
                        'billing_first_name' => $lastOrder->billing_first_name,
                        'billing_last_name' => $lastOrder->billing_last_name,
                        'billing_email' => $lastOrder->billing_email,
                        'billing_phone' => $lastOrder->billing_phone,
                        'billing_address_1' => $lastOrder->billing_address_1,
                        'billing_address_2' => $lastOrder->billing_address_2,
                        'billing_city' => $lastOrder->billing_city,
                        'billing_postcode' => $lastOrder->billing_postcode,
                        'billing_country' => $lastOrder->billing_country,
                    ];
                } else {
                    $nameParts = explode(' ', $user->name, 2);
                    $prefill = [
                        'billing_first_name' => $nameParts[0] ?? '',
                        'billing_last_name' => $nameParts[1] ?? '',
                        'billing_email' => $user->email,
                    ];
                }
            }
        }

        return view('checkout.index', compact('items', 'subtotal', 'discount', 'shippingMethods', 'shippingCountries', 'shippingZones', 'prefill'));
    }

    /** Crée la commande locale + affiche le formulaire de paiement Stripe */
    public function store(CheckoutStoreRequest $request)
    {
        $items = $this->cart->itemsWithProducts();

        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        // Vérifier stock et prix actuels
        $stockCheck = $this->orderService->validateCartStock($items);

        if (! empty($stockCheck['errors'])) {
            return redirect()->route('cart.index')->with('warnings', $stockCheck['errors']);
        }

        if ($stockCheck['cartUpdated']) {
            return redirect()->route('cart.index')
                ->with('warnings', ['Les prix de certains produits ont changé. Veuillez vérifier votre panier.']);
        }

        // Recalculer avec les données fraîches
        $items = $this->cart->itemsWithProducts();
        $subtotal = $this->cart->subtotal();
        $discount = $this->discount->calculate($items, $subtotal, $request->input('coupon_code'));

        $shippingKey = $request->shipping_method;
        $shippingSame = $request->boolean('shipping_same', false);
        $shippingCountry = $shippingSame ? $request->billing_country : ($request->shipping_country ?? $request->billing_country);
        $shippingCost = $this->orderService->calculateShipping($shippingKey, $subtotal, $shippingCountry);
        $giftWrap = $request->boolean('gift_wrap');
        $giftCost = $giftWrap ? 1.00 : 0;
        $total = max(0, $subtotal - $discount['amount'] + $shippingCost + $giftCost);

        $customerNote = $this->orderService->buildCustomerNote(
            $request->customer_note,
            $shippingKey,
            $request->relay_point_name,
            $request->relay_point_address,
        );

        // Créer le PaymentIntent Stripe AVANT la commande pour éviter
        // les commandes orphelines si Stripe échoue
        $paymentIntent = $this->createPaymentIntent($total, $request->billing_email);

        // Cadeau offert (promo Fête des mères)
        $promoGift = null;
        $giftConfig = config('promotions.gift');
        $giftChoice = session('promo_gift');
        if ($giftChoice && $giftConfig['enabled']
            && now()->between($giftConfig['starts_at'], $giftConfig['ends_at'])
            && $subtotal >= $giftConfig['min_cart_value']
            && isset($giftConfig['options'][$giftChoice])) {
            $promoGift = [
                'name' => $giftConfig['options'][$giftChoice],
                'option' => $giftChoice,
            ];
        }

        $order = $this->orderService->createOrder([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'stripe_payment_intent_id' => $paymentIntent->id,
            'billing_first_name' => $request->billing_first_name,
            'billing_last_name' => $request->billing_last_name,
            'billing_email' => $request->billing_email,
            'billing_phone' => $request->billing_phone,
            'billing_address_1' => $request->billing_address_1,
            'billing_address_2' => $request->billing_address_2,
            'billing_city' => $request->billing_city,
            'billing_postcode' => $request->billing_postcode,
            'billing_country' => $request->billing_country,
            'shipping_first_name' => $shippingSame ? $request->billing_first_name : $request->shipping_first_name,
            'shipping_last_name' => $shippingSame ? $request->billing_last_name : $request->shipping_last_name,
            'shipping_address_1' => $shippingSame ? $request->billing_address_1 : $request->shipping_address_1,
            'shipping_address_2' => $shippingSame ? $request->billing_address_2 : $request->shipping_address_2,
            'shipping_city' => $shippingSame ? $request->billing_city : $request->shipping_city,
            'shipping_postcode' => $shippingSame ? $request->billing_postcode : $request->shipping_postcode,
            'shipping_country' => $shippingSame ? $request->billing_country : $request->shipping_country,
            'subtotal' => $subtotal,
            'discount_total' => $discount['amount'],
            'shipping_total' => $shippingCost,
            'shipping_method' => config("shipping.methods.{$shippingKey}.label"),
            'shipping_key' => $shippingKey,
            'relay_point_code' => $shippingKey === 'boxtal' ? $request->relay_point_code : null,
            'relay_network' => $shippingKey === 'boxtal' ? $request->relay_network : null,
            'tax_total' => 0,
            'total' => $total,
            'customer_note' => $customerNote,
            'gift_wrap' => $giftWrap,
            'gift_type' => $giftWrap ? $request->gift_type : null,
            'gift_message' => $giftWrap ? $request->gift_message : null,
            'currency' => 'EUR',
            'payment_method' => 'stripe',
        ], $items, $promoGift);

        // Mettre à jour le PI avec l'ID de commande en metadata
        Stripe::setApiKey(config('cashier.secret'));
        PaymentIntent::update($paymentIntent->id, [
            'metadata' => ['order_id' => $order->id],
            'description' => "Commande #{$order->number}",
        ]);

        return view('checkout.payment', [
            'order' => $order,
            'clientSecret' => $paymentIntent->client_secret,
            'stripeKey' => config('cashier.key'),
        ]);
    }

    /** Page de succès après paiement Stripe */
    public function success(Request $request)
    {
        $order = Order::where('stripe_payment_intent_id', $request->query('payment_intent'))->first();

        if (! $order) {
            return redirect()->route('shop.index');
        }

        // Vérifier côté Stripe que le paiement a bien abouti
        $paymentConfirmed = in_array($order->status, ['processing', 'completed']);

        // Si la commande est encore pending, vérifier auprès de Stripe et valider
        // (sert de filet de sécurité si le webhook n'a pas encore été traité)
        if (! $paymentConfirmed && $order->status === 'pending') {
            Stripe::setApiKey(config('cashier.secret'));
            $intent = PaymentIntent::retrieve($order->stripe_payment_intent_id);

            if ($intent->status === 'succeeded') {
                $paymentConfirmed = true;
                $this->confirmOrderFromSuccess($order);
            }
        }

        if ($paymentConfirmed) {
            $this->cart->clear();
        }

        return view('checkout.success', [
            'order' => $order,
            'paymentConfirmed' => $paymentConfirmed,
        ]);
    }

    /**
     * Confirme la commande depuis la page success si le webhook n'a pas encore traité.
     * Même logique que le webhook, avec lockForUpdate pour éviter les doublons.
     */
    private function confirmOrderFromSuccess(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $locked = Order::where('id', $order->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $locked) {
                return; // Déjà traité par le webhook entre-temps
            }

            $locked->update([
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            Log::info("Commande #{$locked->number} confirmée via page success (webhook non reçu).");

            // Décrémentation du stock
            $locked->load('items');
            foreach ($locked->items as $item) {
                $product = Product::where('id', $item->product_id)
                    ->where('manage_stock', true)
                    ->lockForUpdate()
                    ->first();

                if ($product && $product->stock_quantity >= $item->quantity) {
                    $product->decrement('stock_quantity', $item->quantity);
                    if ($product->fresh()->stock_quantity <= 0) {
                        $product->update(['stock_status' => 'outofstock']);
                    }
                }
            }
        });

        // Emails hors transaction
        $order->refresh();
        if ($order->status === 'processing') {
            try {
                Mail::to($order->billing_email)->send(new OrderConfirmation($order->load('items')));
                Log::info("Email confirmation envoyé au client pour commande #{$order->number} (via success)");
            } catch (\Exception $e) {
                Log::error("Échec envoi email confirmation client pour commande #{$order->number} (via success)", ['error' => $e->getMessage()]);
            }

            try {
                Mail::to(config('mail.admin_address', config('mail.from.address')))->send(new NewOrderAdmin($order));
                Log::info("Email notification admin envoyé pour commande #{$order->number} (via success)");
            } catch (\Exception $e) {
                Log::error("Échec envoi email admin pour commande #{$order->number} (via success)", ['error' => $e->getMessage()]);
            }
        }
    }

    private function createPaymentIntent(float $total, string $email): PaymentIntent
    {
        Stripe::setApiKey(config('cashier.secret'));

        return PaymentIntent::create([
            'amount' => (int) round($total * 100),
            'currency' => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'receipt_email' => $email,
        ]);
    }
}
