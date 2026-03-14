<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Polices disponibles pour la personnalisation produit
    |--------------------------------------------------------------------------
    | Chaque entrée : clé = identifiant stocké, label = nom affiché,
    | google = nom Google Fonts (chargé dynamiquement sur la fiche produit).
    */
    'fonts' => [
        'dancing-script' => ['label' => 'Dancing Script',   'google' => 'Dancing+Script'],
        'great-vibes' => ['label' => 'Great Vibes',      'google' => 'Great+Vibes'],
        'playfair-display' => ['label' => 'Playfair Display',  'google' => 'Playfair+Display'],
        'montserrat' => ['label' => 'Montserrat',       'google' => 'Montserrat'],
        'pacifico' => ['label' => 'Pacifico',         'google' => 'Pacifico'],
        'lora' => ['label' => 'Lora',             'google' => 'Lora'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Couleurs disponibles pour la personnalisation
    |--------------------------------------------------------------------------
    | Chaque entrée : clé = identifiant stocké, label = nom affiché, hex = code couleur.
    */
    'colors' => [
        'or' => ['label' => 'Or',      'hex' => '#d4af37'],
        'argent' => ['label' => 'Argent',  'hex' => '#c0c0c0'],
        'noir' => ['label' => 'Noir',    'hex' => '#000000'],
        'blanc' => ['label' => 'Blanc',   'hex' => '#ffffff'],
        'rose' => ['label' => 'Rose',    'hex' => '#ec4899'],
        'rouge' => ['label' => 'Rouge',   'hex' => '#dc2626'],
        'bleu' => ['label' => 'Bleu',    'hex' => '#2563eb'],
        'vert' => ['label' => 'Vert',    'hex' => '#276e44'],
    ],

];
