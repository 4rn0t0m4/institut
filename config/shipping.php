<?php

return [
    // Pays supportés avec leur libellé
    'countries' => [
        'FR' => 'France',
        'BE' => 'Belgique',
        'ES' => 'Espagne',
        'IT' => 'Italie',
    ],

    // Zones de livraison par pays
    'zones' => [
        'FR' => [
            'methods' => ['colissimo', 'boxtal', 'pickup'],
            'free_shipping_threshold' => 60.00,
        ],
        'international' => [
            'methods' => ['boxtal'],
            'free_shipping_threshold' => 80.00,
            'countries' => ['BE', 'ES', 'IT'],
        ],
    ],

    'methods' => [
        'colissimo' => [
            'label' => 'Livraison à domicile (Colissimo)',
            'price' => 7.90,
        ],
        'boxtal' => [
            'label' => 'Livraison en point relais (Mondial Relay et Chronopost)',
            'price' => 5.00,
            'free_above_threshold' => true,
            'price_international' => 5.90,
        ],
        'pickup' => [
            'label' => 'Retrait à l\'institut',
            'price' => 0.00,
        ],
    ],

    // Legacy key kept for backward compat
    'free_shipping_threshold' => 60.00,

    'boxtal' => [
        'access_key' => env('BOXTAL_ACCESS_KEY'),
        'secret_key' => env('BOXTAL_SECRET_KEY'),
        'connect_access_key' => env('BOXTAL_CONNECT_ACCESS_KEY'),
        'token_url' => 'https://api.boxtal.com/v2/token/maps',
        'bootstrap_url' => 'https://maps.boxtal.com/styles/boxtal/style.json?access_token=${access_token}',
        'networks' => ['MONR_NETWORK', 'CHRP_NETWORK'],
    ],
];
