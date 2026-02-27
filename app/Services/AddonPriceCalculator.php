<?php

namespace App\Services;

class AddonPriceCalculator
{
    /**
     * Calcule le prix total des addons sélectionnés.
     *
     * @param array $addons  [['label'=>'...','type'=>'flat|percentage','price'=>12.5,'value'=>'...'], ...]
     * @param float $basePrice  Prix unitaire du produit (pour les addons en %)
     */
    public function calculate(array $addons, float $basePrice = 0): float
    {
        $total = 0.0;

        foreach ($addons as $addon) {
            $price = (float) ($addon['price'] ?? 0);

            if (($addon['price_type'] ?? 'flat') === 'percentage') {
                $total += $basePrice * $price / 100;
            } else {
                $total += $price;
            }
        }

        return round($total, 2);
    }

    /**
     * Retourne le libellé formaté d'un addon pour l'affichage panier/commande.
     */
    public function format(array $addon): string
    {
        $label = $addon['label'] ?? '';
        $value = $addon['value'] ?? '';

        if (empty($value)) return $label;

        return "{$label} : {$value}";
    }
}
