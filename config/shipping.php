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

        // API v3 — expéditions
        'v3_access_key' => env('BOXTAL_V3_ACCESS_KEY'),
        'v3_secret_key' => env('BOXTAL_V3_SECRET_KEY'),
        'v3_base_url' => env('BOXTAL_V3_BASE_URL', 'https://api.boxtal.com'),

        // Webhook v3 — secret pour vérifier la signature HMAC SHA256
        'v3_webhook_secret' => env('BOXTAL_V3_WEBHOOK_SECRET'),

        // Adresse expéditeur
        'from_address' => [
            'company' => env('BOXTAL_FROM_COMPANY', 'Institut Corps à Coeur'),
            'firstName' => env('BOXTAL_FROM_FIRSTNAME', 'Angélique'),
            'lastName' => env('BOXTAL_FROM_LASTNAME', 'CHANEL'),
            'email' => env('BOXTAL_FROM_EMAIL', 'contact@institutcorpsacoeur.fr'),
            'phone' => env('BOXTAL_FROM_PHONE', '0231201045'),
            'street' => env('BOXTAL_FROM_STREET', '22 avenue Jean Jaurès'),
            'city' => env('BOXTAL_FROM_CITY', 'Mézidon Canon'),
            'postalCode' => env('BOXTAL_FROM_POSTAL_CODE', '14270'),
            'country' => 'FR',
        ],

        // Colis par défaut (cosmétiques)
        'default_package' => [
            'weight' => 0.5, // kg
            'length' => 25,  // cm
            'width' => 20,   // cm
            'height' => 10,  // cm
        ],

        // Catégorie de contenu (GET /content-category pour la liste)
        'content_category_id' => env('BOXTAL_CONTENT_CATEGORY', 'content:v1:10100'),

        // Codes offres par méthode d'expédition / réseau
        'shipping_offer_codes' => [
            'MONR_NETWORK' => env('BOXTAL_OFFER_MONR', 'MONR-CpourToi'),
            'CHRP_NETWORK' => env('BOXTAL_OFFER_CHRP', 'CHRP-ChronoShoptoShop'),
            'colissimo' => env('BOXTAL_OFFER_COLISSIMO', 'POFR-ColissimoAccess'),
        ],
    ],
];
