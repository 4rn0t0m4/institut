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
        .credit-note-box { background: #f0fdf4; border-radius: 8px; padding: 20px; margin-bottom: 24px; }
        .credit-note-box p { margin: 6px 0; font-size: 14px; }
        .credit-note-box strong { color: #276e44; }
        .amount { font-size: 24px; font-weight: 700; color: #276e44; margin: 12px 0; }
        .footer { text-align: center; padding: 20px 30px; background: #f9fafb; font-size: 12px; color: #9ca3af; }
        .footer a { color: #276e44; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Avoir</h1>
            <p>Institut Corps &agrave; Coeur</p>
        </div>

        <div class="content">
            <p class="greeting">Bonjour {{ $creditNote->order->billing_first_name }},</p>
            <p>Nous avons proc&eacute;d&eacute; au remboursement de votre commande. Voici les d&eacute;tails de l'avoir :</p>

            <div class="credit-note-box">
                <p><strong>Avoir n&deg; :</strong> {{ $creditNote->number }}</p>
                <p><strong>Commande :</strong> #{{ $creditNote->order->number }}</p>
                <p><strong>Date :</strong> {{ $creditNote->created_at->format('d/m/Y') }}</p>
                <p class="amount">{{ number_format($creditNote->amount, 2, ',', ' ') }} &euro;</p>
                @if($creditNote->reason)
                    <p><strong>Motif :</strong> {{ $creditNote->reason }}</p>
                @endif
                @if($creditNote->stripe_refunded)
                    <p style="margin-top: 12px; font-size: 13px; color: #059669;">Le remboursement a &eacute;t&eacute; effectu&eacute; sur votre moyen de paiement. Comptez 5 &agrave; 10 jours ouvrables pour voir appara&icirc;tre le cr&eacute;dit.</p>
                @endif
            </div>

            <p style="font-size: 14px;">Pour toute question, n'h&eacute;sitez pas &agrave; nous contacter.</p>
        </div>

        <div class="footer">
            <p>Institut Corps &agrave; Coeur &mdash; M&eacute;zidon Canon</p>
            <p><a href="{{ url('/') }}">institutcorpsacoeur.fr</a></p>
        </div>
    </div>
</body>
</html>
