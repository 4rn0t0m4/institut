<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\DiscountEngine;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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

    /** Crée la commande locale + affiche le formulaire de paiement Stripe */
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
            'coupon_code'        => 'nullable|string|max:50',
        ]);

        $items = $this->cart->itemsWithProducts();

        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        // Vérifier stock et prix actuels avant de créer la commande
        $cartUpdated = false;
        $stockErrors = [];

        foreach ($items as $key => $item) {
            $product = $item['product'];
            if (!$product || !$product->is_active) {
                $this->cart->remove($key);
                $stockErrors[] = "{$item['name']} n'est plus disponible.";
                continue;
            }

            // Vérifier le prix
            if (abs($product->currentPrice() - $item['price']) > 0.01) {
                $this->cart->updatePrice($key, $product->currentPrice());
                $cartUpdated = true;
            }

            // Vérifier le stock
            if ($product->manage_stock && $product->stock_quantity < $item['quantity']) {
                if ($product->stock_quantity <= 0) {
                    $this->cart->remove($key);
                    $stockErrors[] = "{$item['name']} est en rupture de stock.";
                } else {
                    $this->cart->update($key, $product->stock_quantity);
                    $stockErrors[] = "{$item['name']} : quantité réduite à {$product->stock_quantity} (stock insuffisant).";
                }
            }
        }

        if (!empty($stockErrors)) {
            return redirect()->route('cart.index')
                ->with('warnings', $stockErrors);
        }

        if ($cartUpdated) {
            return redirect()->route('cart.index')
                ->with('warnings', ['Les prix de certains produits ont changé. Veuillez vérifier votre panier.']);
        }

        // Recalculer avec les données fraîches
        $couponCode = $request->input('coupon_code');
        $items      = $this->cart->itemsWithProducts();
        $subtotal   = $this->cart->subtotal();
        $discount   = $this->discount->calculate($items, $subtotal, $couponCode);

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
