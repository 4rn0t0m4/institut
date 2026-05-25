<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; padding: 40px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left { display: table-cell; vertical-align: top; width: 50%; }
        .header-right { display: table-cell; vertical-align: top; width: 50%; text-align: right; }
        .company-name { font-size: 18px; font-weight: bold; color: #276e44; margin-bottom: 4px; }
        .company-info { font-size: 10px; color: #666; line-height: 1.6; }
        .avoir-title { font-size: 22px; font-weight: bold; color: #276e44; margin-bottom: 4px; }
        .avoir-number { font-size: 14px; color: #555; }
        .avoir-date { font-size: 11px; color: #888; margin-top: 2px; }
        .divider { border-top: 2px solid #276e44; margin: 20px 0; }
        .info-grid { display: table; width: 100%; margin-bottom: 24px; }
        .info-block { display: table-cell; vertical-align: top; width: 50%; }
        .info-label { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: bold; }
        .info-content { font-size: 11px; line-height: 1.6; }
        .amount-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 20px; text-align: center; margin: 24px 0; }
        .amount-label { font-size: 12px; color: #666; margin-bottom: 6px; }
        .amount-value { font-size: 28px; font-weight: bold; color: #276e44; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th { text-align: left; font-size: 10px; text-transform: uppercase; color: #888; padding: 8px 0; border-bottom: 2px solid #e5e7eb; }
        .details-table td { padding: 10px 0; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
        .details-table .text-right { text-align: right; }
        .reason-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 12px; margin: 16px 0; font-size: 11px; }
        .reason-label { font-weight: bold; color: #555; margin-bottom: 4px; }
        .stripe-notice { font-size: 10px; color: #059669; margin-top: 8px; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #aaa; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="company-name">Institut Corps &agrave; Coeur</div>
            <div class="company-info">
                M&eacute;zidon Canon<br>
                institutcorpsacoeur.fr<br>
                contact@institutcorpsacoeur.fr
            </div>
        </div>
        <div class="header-right">
            <div class="avoir-title">AVOIR</div>
            <div class="avoir-number">{{ $creditNote->number }}</div>
            <div class="avoir-date">Date : {{ $creditNote->created_at->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-grid">
        <div class="info-block">
            <div class="info-label">Commande d'origine</div>
            <div class="info-content">
                <strong>{{ $creditNote->order->number }}</strong><br>
                du {{ $creditNote->order->created_at->format('d/m/Y') }}
                @if($creditNote->order->paid_at)
                    <br>Pay&eacute;e le {{ $creditNote->order->paid_at->format('d/m/Y') }}
                @endif
            </div>
        </div>
        <div class="info-block">
            <div class="info-label">Client</div>
            <div class="info-content">
                {{ $creditNote->order->billing_first_name }} {{ $creditNote->order->billing_last_name }}<br>
                {{ $creditNote->order->billing_address_1 }}<br>
                @if($creditNote->order->billing_address_2){{ $creditNote->order->billing_address_2 }}<br>@endif
                {{ $creditNote->order->billing_postcode }} {{ $creditNote->order->billing_city }}<br>
                {{ $creditNote->order->billing_email }}
            </div>
        </div>
    </div>

    <div class="amount-box">
        <div class="amount-label">Montant de l'avoir</div>
        <div class="amount-value">{{ number_format($creditNote->amount, 2, ',', ' ') }} &euro;</div>
        @if($creditNote->stripe_refunded)
            <div class="stripe-notice">Rembours&eacute; sur le moyen de paiement d'origine</div>
        @endif
    </div>

    @if($creditNote->reason)
        <div class="reason-box">
            <div class="reason-label">Motif</div>
            {{ $creditNote->reason }}
        </div>
    @endif

    <table class="details-table">
        <thead>
            <tr>
                <th>D&eacute;tail</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total commande {{ $creditNote->order->number }}</td>
                <td class="text-right">{{ number_format($creditNote->order->total, 2, ',', ' ') }} &euro;</td>
            </tr>
            <tr>
                <td><strong>Avoir {{ $creditNote->number }}</strong></td>
                <td class="text-right"><strong>-{{ number_format($creditNote->amount, 2, ',', ' ') }} &euro;</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Institut Corps &agrave; Coeur &mdash; M&eacute;zidon Canon &mdash; institutcorpsacoeur.fr
    </div>
</body>
</html>
