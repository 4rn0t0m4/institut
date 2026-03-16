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
        'pacifico' => ['label' => 'Pacifico', 'google' => 'Pacifico'],
        'angelisa' => ['label' => 'Angelisa', 'google' => null, 'local' => true],
        'cinzel' => ['label' => 'Cinzel',   'google' => 'Cinzel'],
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
