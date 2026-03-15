<?php

namespace App\Services;

use App\Models\ProductAddon;

class AddonPriceCalculator
{
    /**
     * Calcule le prix total des addons sélectionnés.
     * Les prix sont TOUJOURS lus depuis la base de données, jamais depuis le formulaire.
     *
     * @param  array  $addons  [addonId => ['value'=>'...', ...], ...]
     * @param  float  $basePrice  Prix unitaire du produit (pour les addons en %)
     */
    public function calculate(array $addons, float $basePrice = 0): float
    {
        if (empty($addons)) {
            return 0.0;
        }

        $addonIds = array_keys($addons);
        $dbAddons = ProductAddon::whereIn('id', $addonIds)->get()->keyBy('id');

        $total = 0.0;

        foreach ($addons as $addonId => $data) {
            $dbAddon = $dbAddons->get($addonId);
            if (! $dbAddon || $dbAddon->price <= 0) {
                continue;
            }

            if ($dbAddon->price_type === 'percentage') {
                $total += $basePrice * $dbAddon->price / 100;
            } else {
                $total += $dbAddon->price;
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

        if (empty($value)) {
            return $label;
        }

        return "{$label} : {$value}";
    }
}
