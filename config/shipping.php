<?php

return [
    'methods' => [
        'colissimo' => [
            'label' => 'Colissimo (livraison à domicile)',
            'price' => 7.90,
        ],
        'boxtal' => [
            'label' => 'Boxtal (point relais)',
            'price' => 5.00,
        ],
        'pickup' => [
            'label' => 'Retrait à l\'institut',
            'price' => 0.00,
        ],
    ],

    'boxtal' => [
        'access_token' => env('BOXTAL_ACCESS_TOKEN'),
        'networks'     => ['MONR_COLL'], // Mondial Relay
    ],
];
