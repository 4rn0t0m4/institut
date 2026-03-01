<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\DiscountEngine;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private DiscountEngine $discount,
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

        return view('checkout.index', compact('items', 'subtotal', 'discount', 'shippingMethods'));
    }

    /** Crée la commande locale + redirige vers Stripe Checkout */
    public function store(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:100',
            'billing_last_name'  => 'required|string|max:100',
            'billing_email'      => 'required|email|max:255',
            'billing_phone'      => 'nullable|string|max:30',
            'billing_address_1'  => 'required|string|max:255',
            'billing_address_2'  => 'nullable|string|max:255',
            'billing_city'       => 'required|string|max:100',
            'billing_postcode'   => 'required|string|max:20',
            'billing_country'    => 'required|string|size:2',
            'shipping_same'      => 'nullable|boolean',
            'shipping_first_name'=> 'nullable|string|max:100',
            'shipping_last_name' => 'nullable|string|max:100',
            'shipping_address_1' => 'nullable|string|max:255',
            'shipping_address_2' => 'nullable|string|max:255',
            'shipping_city'      => 'nullable|string|max:100',
            'shipping_postcode'  => 'nullable|string|max:20',
            'shipping_country'   => 'nullable|string|size:2',
            'customer_note'      => 'nullable|string|max:1000',
            'shipping_method'    => 'required|in:' . implode(',', array_keys(config('shipping.methods'))),
            'relay_point_code'   => 'nullable|required_if:shipping_method,boxtal|string|max:100',
            'relay_point_name'   => 'nullable|string|max:255',
            'relay_point_address'=> 'nullable|string|max:500',
        ]);

        $items    = $this->cart->itemsWithProducts();
        $subtotal = $this->cart->subtotal();
        $discount = $this->discount->calculate($items, $subtotal);

        $shippingKey  = $request->shipping_method;
        $shippingCost = config("shipping.methods.{$shippingKey}.price", 0);
        $total        = max(0, $subtotal - $discount['amount'] + $shippingCost);

        // Build customer note with relay point info
        $customerNote = $request->customer_note ?? '';
        if ($shippingKey === 'boxtal' && $request->relay_point_name) {
            $relayInfo = "Point relais : {$request->relay_point_name}";
            if ($request->relay_point_address) {
                $relayInfo .= " — {$request->relay_point_address}";
            }
            $customerNote = $relayInfo . ($customerNote ? "\n\n" . $customerNote : '');
        }

        $shippingSame = $request->boolean('shipping_same', false);

        $order = Order::create([
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
            'tax_total'            => 0,
            'total'                => $total,
            'customer_note'        => $customerNote ?: null,
            'currency'             => 'EUR',
            'payment_method'       => 'stripe',
        ]);

        foreach ($items as $item) {
            $orderItem = OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['price'],
                'addons_price' => $item['addon_price'],
                'total'        => ($item['price'] + $item['addon_price']) * $item['quantity'],
                'tax'          => 0,
            ]);

            if (!empty($item['addons'])) {
                foreach ($item['addons'] as $addonLabel => $addonValue) {
                    $orderItem->addons()->create([
                        'addon_label' => $addonLabel,
                        'addon_value' => is_array($addonValue) ? implode(', ', $addonValue) : (string) $addonValue,
                        'addon_price' => 0,
                        'addon_type'  => 'text',
                    ]);
                }
            }
        }

        $stripeSession = $this->createStripeSession($order, $items, $discount, $shippingCost);
        $order->update(['stripe_session_id' => $stripeSession->id]);

        return redirect($stripeSession->url);
    }

    /** Page de succès après paiement Stripe */
    public function success(Request $request)
    {
        $order = Order::where('stripe_session_id', $request->query('session_id'))->first();

        if (!$order) {
            return redirect()->route('shop.index');
        }

        if (in_array($order->status, ['processing', 'completed'])) {
            $this->cart->clear();
        }

        return view('checkout.success', compact('order'));
    }

    /** Page d'annulation */
    public function cancel(Request $request)
    {
        $order = Order::where('stripe_session_id', $request->query('session_id'))->first();

        if ($order?->status === 'pending') {
            $order->update(['status' => 'cancelled']);
        }

        return view('checkout.cancel');
    }

    private function createStripeSession(Order $order, array $items, array $discount, float $shippingCost): StripeSession
    {
        Stripe::setApiKey(config('cashier.secret'));

        $lineItems = [];

        foreach ($items as $item) {
            $unitAmount = (int) round(($item['price'] + $item['addon_price']) * 100);
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'unit_amount'  => $unitAmount,
                    'product_data' => ['name' => $item['name']],
                ],
                'quantity' => $item['quantity'],
            ];
        }

        if ($shippingCost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'unit_amount'  => (int) round($shippingCost * 100),
                    'product_data' => ['name' => 'Frais de livraison'],
                ],
                'quantity' => 1,
            ];
        }

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'customer_email'       => $order->billing_email,
            'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata'             => ['order_id' => $order->id],
            'locale'               => 'fr',
        ];

        if ($discount['amount'] > 0) {
            $coupon = \Stripe\Coupon::create([
                'amount_off' => (int) round($discount['amount'] * 100),
                'currency'   => 'eur',
                'duration'   => 'once',
                'name'       => $discount['label'] ?? 'Remise',
            ]);
            $sessionParams['discounts'] = [['coupon' => $coupon->id]];
        }

        return StripeSession::create($sessionParams);
    }
}
