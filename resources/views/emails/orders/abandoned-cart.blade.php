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
        .discount-box { background: #f0fdf4; border: 2px dashed #276e44; border-radius: 10px; padding: 20px; text-align: center; margin: 24px 0; }
        .discount-code { font-size: 28px; font-weight: 700; color: #276e44; letter-spacing: 3px; margin: 8px 0; }
        .discount-label { font-size: 14px; color: #6b7280; }
        .discount-value { font-size: 18px; font-weight: 600; color: #276e44; margin-bottom: 4px; }
        .btn { display: inline-block; background: #276e44; color: white; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; }
        .footer { text-align: center; padding: 20px 30px; background: #f9fafb; font-size: 12px; color: #9ca3af; }
        .footer a { color: #276e44; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Votre commande vous attend !</h1>
        <p>Commande #{{ $order->number }}</p>
    </div>

    <div class="content">
        <p class="greeting">Bonjour {{ $order->billing_first_name }},</p>

        <p>Nous avons remarqué que votre commande n'a pas encore été finalisée. Vos articles sont toujours disponibles :</p>

        <div class="product-list">
            @foreach($order->items as $item)
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 8px;">
                    <tr>
                        @if($item->product && $item->product->featuredImage)
                            <td width="60" style="padding-right: 14px;">
                                <img src="{{ url($item->product->featuredImage->url) }}" alt="{{ $item->product_name }}" width="60" height="60" style="border-radius: 8px; object-fit: cover;">
                            </td>
                        @endif
                        <td>
                            <div style="font-size: 14px; font-weight: 500; color: #111827;">{{ $item->product_name }}</div>
                            <div style="font-size: 12px; color: #6b7280;">Quantité : {{ $item->quantity }}</div>
                        </td>
                        <td width="80" style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #111827;">{{ number_format($item->total, 2, ',', ' ') }} €</div>
                        </td>
                    </tr>
                </table>
            @endforeach
        </div>

        <p>Pour vous remercier de votre intérêt, nous vous offrons <strong>10% de réduction</strong> sur votre prochaine commande avec le code :</p>

        <div class="discount-box">
            <div class="discount-value">-10% sur votre commande</div>
            <div class="discount-code">{{ $discountCode }}</div>
            <div class="discount-label">Valable 7 jours</div>
        </div>

        <div style="text-align: center; margin: 28px 0;">
            <a href="{{ url('/boutique') }}" class="btn" style="display: inline-block; background: #276e44; color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600;">Reprendre mes achats</a>
        </div>

        <p style="font-size: 13px; color: #6b7280; text-align: center; margin-top: 24px;">
            À très bientôt !
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Institut Corps à Cœur — Tous droits réservés</p>
        <p style="margin-top: 4px;">
            <a href="{{ url('/') }}">institutcorpsacoeur.fr</a>
        </p>
    </div>
</div>
</body>
</html>
