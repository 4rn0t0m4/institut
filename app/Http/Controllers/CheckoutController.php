<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutStoreRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\DiscountEngine;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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

        $subtotal        = $this->cart->subtotal();
        $discount        = $this->discount->calculate($items, $subtotal);
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
                    'billing_last_name'  => $user->last_name,
                    'billing_email'      => $user->email,
                    'billing_phone'      => $user->phone,
                    'billing_address_1'  => $user->address_1,
                    'billing_address_2'  => $user->address_2,
                    'billing_city'       => $user->city,
                    'billing_postcode'   => $user->postcode,
                    'billing_country'    => $user->country,
                ];
                // Adresse de livraison différente
                if ($user->shipping_address_1) {
                    $prefill['shipping_same']       = false;
                    $prefill['shipping_first_name']  = $user->shipping_first_name;
                    $prefill['shipping_last_name']   = $user->shipping_last_name;
                    $prefill['shipping_address_1']   = $user->shipping_address_1;
                    $prefill['shipping_address_2']   = $user->shipping_address_2;
                    $prefill['shipping_city']        = $user->shipping_city;
                    $prefill['shipping_postcode']    = $user->shipping_postcode;
                    $prefill['shipping_country']     = $user->shipping_country;
                }
            } else {
                // Fallback : dernière commande
                $lastOrder = $user->orders()->latest()->first();
                if ($lastOrder) {
                    $prefill = [
                        'billing_first_name' => $lastOrder->billing_first_name,
                        'billing_last_name'  => $lastOrder->billing_last_name,
                        'billing_email'      => $lastOrder->billing_email,
                        'billing_phone'      => $lastOrder->billing_phone,
                        'billing_address_1'  => $lastOrder->billing_address_1,
                        'billing_address_2'  => $lastOrder->billing_address_2,
                        'billing_city'       => $lastOrder->billing_city,
                        'billing_postcode'   => $lastOrder->billing_postcode,
                        'billing_country'    => $lastOrder->billing_country,
                    ];
                } else {
                    $nameParts = explode(' ', $user->name, 2);
                    $prefill = [
                        'billing_first_name' => $nameParts[0] ?? '',
                        'billing_last_name'  => $nameParts[1] ?? '',
                        'billing_email'      => $user->email,
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
        $items    = $this->cart->itemsWithProducts();
        $subtotal = $this->cart->subtotal();
        $discount = $this->discount->calculate($items, $subtotal, $request->input('coupon_code'));

        $shippingKey  = $request->shipping_method;
        $shippingSame = $request->boolean('shipping_same', false);
        $shippingCountry = $shippingSame ? $request->billing_country : ($request->shipping_country ?? $request->billing_country);
        $shippingCost = $this->orderService->calculateShipping($shippingKey, $subtotal, $shippingCountry);
        $giftWrap     = $request->boolean('gift_wrap');
        $giftCost     = $giftWrap ? 1.00 : 0;
        $total        = max(0, $subtotal - $discount['amount'] + $shippingCost + $giftCost);

        $customerNote = $this->orderService->buildCustomerNote(
            $request->customer_note,
            $shippingKey,
            $request->relay_point_name,
            $request->relay_point_address,
        );

        $order = $this->orderService->createOrder([
            'user_id'              => auth()->id(),
            'status'               => 'pending',
            'billing_first_name'   => $request->billing_first_name,
            'billing_last_name'    => $request->billing_last_name,
            'billing_email'        => $request->billing_email,
            'billing_phone'        => $request->billing_phone,
            'billing_address_1'    => $request->billing_address_1,
            'billing_address_2'    => $request->billing_address_2,
            'billing_city'         => $request->billing_city,
            'billing_postcode'     => $request->billing_postcode,
            'billing_country'      => $request->billing_country,
            'shipping_first_name'  => $shippingSame ? $request->billing_first_name : $request->shipping_first_name,
            'shipping_last_name'   => $shippingSame ? $request->billing_last_name  : $request->shipping_last_name,
            'shipping_address_1'   => $shippingSame ? $request->billing_address_1  : $request->shipping_address_1,
            'shipping_address_2'   => $shippingSame ? $request->billing_address_2  : $request->shipping_address_2,
            'shipping_city'        => $shippingSame ? $request->billing_city        : $request->shipping_city,
            'shipping_postcode'    => $shippingSame ? $request->billing_postcode    : $request->shipping_postcode,
            'shipping_country'     => $shippingSame ? $request->billing_country     : $request->shipping_country,
            'subtotal'             => $subtotal,
            'discount_total'       => $discount['amount'],
            'shipping_total'       => $shippingCost,
            'shipping_method'      => config("shipping.methods.{$shippingKey}.label"),
            'shipping_key'         => $shippingKey,
            'relay_point_code'     => $shippingKey === 'boxtal' ? $request->relay_point_code : null,
            'relay_network'        => $shippingKey === 'boxtal' ? $request->relay_network : null,
            'tax_total'            => 0,
            'total'                => $total,
            'customer_note'        => $customerNote,
            'gift_wrap'            => $giftWrap,
            'gift_type'            => $giftWrap ? $request->gift_type : null,
            'gift_message'         => $giftWrap ? $request->gift_message : null,
            'currency'             => 'EUR',
            'payment_method'       => 'stripe',
        ], $items);

        $paymentIntent = $this->createPaymentIntent($order, $total);
        $order->update(['stripe_payment_intent_id' => $paymentIntent->id]);

        return view('checkout.payment', [
            'order'        => $order,
            'clientSecret' => $paymentIntent->client_secret,
            'stripeKey'    => config('cashier.key'),
        ]);
    }

    /** Page de succès après paiement Stripe */
    public function success(Request $request)
    {
        $order = Order::where('stripe_payment_intent_id', $request->query('payment_intent'))->first();

        if (!$order) {
            return redirect()->route('shop.index');
        }

        // Vérifier côté Stripe que le paiement a bien abouti
        // Le webhook fera le traitement complet (statut, stock, emails)
        $paymentConfirmed = in_array($order->status, ['processing', 'completed']);

        if (!$paymentConfirmed && $order->status === 'pending') {
            Stripe::setApiKey(config('cashier.secret'));
            $intent = PaymentIntent::retrieve($order->stripe_payment_intent_id);
            $paymentConfirmed = $intent->status === 'succeeded';
        }

        if ($paymentConfirmed) {
            $this->cart->clear();
        }

        return view('checkout.success', [
            'order'            => $order,
            'paymentConfirmed' => $paymentConfirmed,
        ]);
    }

    private function createPaymentIntent(Order $order, float $total): PaymentIntent
    {
        Stripe::setApiKey(config('cashier.secret'));

        return PaymentIntent::create([
            'amount'                    => (int) round($total * 100),
            'currency'                  => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata'                  => ['order_id' => $order->id],
            'description'               => "Commande #{$order->id}",
            'receipt_email'             => $order->billing_email,
        ]);
    }
}
