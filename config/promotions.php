<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cadeau offert (Fête des mères 2026)
    |--------------------------------------------------------------------------
    |
    | Trousse personnalisée offerte à partir de 100€ d'achat.
    | Choix entre bleue et rouge.
    |
    */

    'gift' => [
        'enabled' => true,
        'min_cart_value' => 100,
        'starts_at' => '2026-05-01',
        'ends_at' => '2026-05-31 23:59:59',
        'label' => 'Fête des mères : trousse personnalisée offerte !',
        'description' => 'Pour toute commande à partir de 100 €, choisissez votre trousse de maquillage personnalisable offerte.',
        'options' => [
            'bleue' => 'Trousse de maquillage bleue',
            'rouge' => 'Trousse de maquillage rouge',
        ],
    ],

];
