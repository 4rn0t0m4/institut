<?php

return [
    'methods' => [
        'colissimo' => [
            'label' => 'Livraison à domicile (Colissimo)',
            'price' => 7.90,
        ],
        'boxtal' => [
            'label' => 'Livraison en point relais (Mondial Relay et Chronopost)',
            'price' => 5.00,
        ],
        'pickup' => [
            'label' => 'Retrait à l\'institut',
            'price' => 0.00,
        ],
    ],

    'boxtal' => [
        'access_key'     => env('BOXTAL_ACCESS_KEY'),
        'secret_key'     => env('BOXTAL_SECRET_KEY'),
        'token_url'      => 'https://api.boxtal.com/v2/token/maps',
        'bootstrap_url'  => 'https://maps.boxtal.com/styles/boxtal/style.json?access_token=${access_token}',
        'networks'       => ['MONR_NETWORK', 'CHRP_NETWORK'],
    ],
];
