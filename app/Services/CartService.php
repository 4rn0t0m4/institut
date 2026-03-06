<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAddon;
use Illuminate\Support\Str;

class CartService
{
    private const SESSION_KEY = 'cart';

    /** Retourne le panier courant */
    public function all(): array
    {
        return session(self::SESSION_KEY, []);
    }

    /** Ajoute ou incrémente un article */
    public function add(Product $product, int $quantity = 1, array $addons = []): string
    {
        $cart = $this->all();

        // Sanitize addons: replace client-sent label/price with DB values
        $sanitizedAddons = $this->sanitizeAddons($addons);

        // Clé unique par produit + combinaison d'addons
        $key = $product->id . '-' . md5(serialize($sanitizedAddons));

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $addonPrice = app(AddonPriceCalculator::class)->calculate($sanitizedAddons, $product->currentPrice());
            $cart[$key] = [
                'key'         => $key,
                'product_id'  => $product->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'price'       => $product->currentPrice(),
                'addon_price' => $addonPrice,
                'quantity'    => $quantity,
                'addons'      => $sanitizedAddons,
                'image'       => $product->featuredImage?->url,
            ];
        }

        session([self::SESSION_KEY => $cart]);

        return $key;
    }

    /** Replace client-sent addon labels/prices with trusted DB values */
    private function sanitizeAddons(array $addons): array
    {
        if (empty($addons)) {
            return [];
        }

        $dbAddons = ProductAddon::whereIn('id', array_keys($addons))->get()->keyBy('id');
        $sanitized = [];

        foreach ($addons as $addonId => $data) {
            $dbAddon = $dbAddons->get($addonId);
            if (!$dbAddon) {
                continue;
            }

            $sanitized[$addonId] = [
                'label' => $dbAddon->label,
                'value' => $data['value'] ?? ($data[0] ?? ''),
            ];
        }

        return $sanitized;
    }

    /** Met à jour le prix d'un article (si le prix produit a changé) */
    public function updatePrice(string $key, float $newPrice): void
    {
        $cart = $this->all();

        if (isset($cart[$key])) {
            $cart[$key]['price'] = $newPrice;
            session([self::SESSION_KEY => $cart]);
        }
    }

    /** Met à jour la quantité d'un article */
    public function update(string $key, int $quantity): void
    {
        $cart = $this->all();

        if (isset($cart[$key])) {
            if ($quantity <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $quantity;
            }
            session([self::SESSION_KEY => $cart]);
        }
    }

    /** Supprime un article */
    public function remove(string $key): void
    {
        $cart = $this->all();
        unset($cart[$key]);
        session([self::SESSION_KEY => $cart]);
    }

    /** Vide le panier */
    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /** Nombre total d'articles */
    public function count(): int
    {
        return array_sum(array_column($this->all(), 'quantity'));
    }

    /** Sous-total avant remises */
    public function subtotal(): float
    {
        return array_reduce($this->all(), function (float $carry, array $item) {
            return $carry + ($item['price'] + $item['addon_price']) * $item['quantity'];
        }, 0.0);
    }

    /** Items enrichis avec le modèle Product (pour DiscountEngine et checkout) */
    public function itemsWithProducts(): array
    {
        $cart = $this->all();
        if (empty($cart)) {
            return [];
        }

        $productIds = array_unique(array_column($cart, 'product_id'));
        $products   = Product::with('category.parent')->whereIn('id', $productIds)->get()->keyBy('id');

        return array_map(function (array $item) use ($products) {
            $product = $products->get($item['product_id']);
            $item['product']    = $product;
            $item['unit_price'] = $item['price'];
            $item['url']        = $product?->url() ?? url("boutique/{$item['slug']}");
            return $item;
        }, $cart);
    }
}
