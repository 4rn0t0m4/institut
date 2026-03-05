<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f9fafb; margin: 0; padding: 20px; color: #374151; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: #276e44; padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; font-weight: 600; }
        .header p { color: #b0f1b9; margin: 8px 0 0; font-size: 14px; }
        .content { padding: 30px; }
        .product-box { background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 8px; padding: 20px; margin: 24px 0; text-align: center; }
        .product-name { font-size: 18px; font-weight: 600; color: #276e44; margin-bottom: 8px; }
        .product-price { font-size: 22px; font-weight: 700; color: #111827; }
        .btn { display: inline-block; background: #276e44; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; }
        .footer { text-align: center; padding: 20px 30px; background: #f9fafb; font-size: 12px; color: #9ca3af; }
        .footer a { color: #276e44; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Bonne nouvelle !</h1>
        <p>Un produit que vous attendiez est de retour</p>
    </div>

    <div class="content">
        <p>Bonjour,</p>

        <p>Vous aviez demandé à être prévenu(e) lorsque ce produit serait de nouveau disponible :</p>

        <div class="product-box">
            @if($product->featuredImage)
                <img src="{{ url($product->featuredImage->url) }}" alt="{{ $product->name }}"
                     style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
            @endif
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">{{ number_format($product->currentPrice(), 2, ',', ' ') }} €</div>
        </div>

        <p style="text-align: center; margin: 28px 0;">
            <a href="{{ route('shop.show', $product->slug) }}" class="btn">Voir le produit</a>
        </p>

        <p style="font-size: 13px; color: #6b7280;">
            Attention, les stocks sont limités. N'attendez pas trop !
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Institut Corps à Coeur — Tous droits réservés</p>
        <p>
            <a href="{{ route('shop.index') }}">Visiter la boutique</a>
        </p>
    </div>
</div>
</body>
</html>
