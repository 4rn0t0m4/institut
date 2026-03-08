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
        .greeting { font-size: 16px; margin-bottom: 20px; }
        .product-list { margin: 24px 0; }
        .product-item { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .product-item:last-child { border-bottom: none; }
        .product-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; background: #f0fdf4; }
        .product-name { font-size: 14px; font-weight: 500; color: #111827; }
        .product-qty { font-size: 12px; color: #6b7280; }
        .btn { display: inline-block; background: #276e44; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; }
        .stars { color: #f59e0b; font-size: 20px; letter-spacing: 2px; margin: 16px 0; }
        .footer { text-align: center; padding: 20px 30px; background: #f9fafb; font-size: 12px; color: #9ca3af; }
        .footer a { color: #276e44; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Votre avis nous intéresse !</h1>
        <p>Commande #{{ $order->number }}</p>
    </div>

    <div class="content">
        <p class="greeting">Bonjour {{ $order->billing_first_name }},</p>

        <p>Vous avez recu votre commande depuis quelques jours maintenant. Nous esperons que tout vous plait !</p>

        <p>Pourriez-vous prendre un instant pour donner votre avis sur les produits que vous avez commandes ? Cela aide enormement les autres clients dans leur choix.</p>

        <div class="product-list">
            @foreach($order->items as $item)
                @if($item->product)
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 8px;">
                        <tr>
                            @if($item->product->featuredImage)
                                <td width="60" style="padding-right: 14px;">
                                    <img src="{{ url($item->product->featuredImage->url) }}" alt="{{ $item->product_name }}" width="60" height="60" style="border-radius: 8px; object-fit: cover;">
                                </td>
                            @endif
                            <td>
                                <div style="font-size: 14px; font-weight: 500; color: #111827;">{{ $item->product_name }}</div>
                                <div style="font-size: 12px; color: #6b7280;">Quantite : {{ $item->quantity }}</div>
                            </td>
                            <td width="120" style="text-align: right;">
                                <a href="{{ $item->product->url() }}#avis" style="color: #276e44; font-size: 13px; font-weight: 600; text-decoration: none;">Donner mon avis</a>
                            </td>
                        </tr>
                    </table>
                @endif
            @endforeach
        </div>

        <div style="text-align: center; margin: 28px 0;">
            <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <a href="{{ url('/boutique') }}" class="btn">Voir mes produits</a>
        </div>

        <p style="font-size: 13px; color: #6b7280; text-align: center; margin-top: 24px;">
            Merci pour votre confiance !
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Institut Corps a Coeur — Tous droits reserves</p>
        <p style="margin-top: 4px;">
            <a href="{{ url('/') }}">institutcorpsacoeur.fr</a>
        </p>
    </div>
</div>
</body>
</html>
