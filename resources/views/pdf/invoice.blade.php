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
        .doc-title { font-size: 22px; font-weight: bold; color: #276e44; margin-bottom: 4px; }
        .doc-number { font-size: 14px; color: #555; }
        .doc-date { font-size: 11px; color: #888; margin-top: 2px; }
        .divider { border-top: 2px solid #276e44; margin: 20px 0; }
        .info-grid { display: table; width: 100%; margin-bottom: 24px; }
        .info-block { display: table-cell; vertical-align: top; width: 50%; }
        .info-label { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; margin-bottom: 6px; font-weight: bold; }
        .info-content { font-size: 11px; line-height: 1.6; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th { text-align: left; font-size: 10px; text-transform: uppercase; color: #888; padding: 8px 4px; border-bottom: 2px solid #e5e7eb; }
        .items-table td { padding: 8px 4px; font-size: 11px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .addon { font-size: 9px; color: #888; margin-top: 2px; }
        .totals-table { width: 50%; margin-left: auto; border-collapse: collapse; margin-top: 10px; }
        .totals-table td { padding: 6px 4px; font-size: 11px; border-bottom: 1px solid #e5e7eb; }
        .totals-table .text-right { text-align: right; }
        .totals-table tr.total-row td { border-top: 2px solid #276e44; border-bottom: 2px solid #276e44; padding: 10px 4px; font-size: 13px; font-weight: bold; }
        .payment-info { margin-top: 20px; font-size: 10px; color: #888; }
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
            <div class="doc-title">FACTURE</div>
            <div class="doc-number">{{ $order->number }}</div>
            <div class="doc-date">Date : {{ ($order->paid_at ?? $order->created_at)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-grid">
        <div class="info-block">
            <div class="info-label">Facturation</div>
            <div class="info-content">
                {{ $order->billing_first_name }} {{ $order->billing_last_name }}<br>
                {{ $order->billing_address_1 }}<br>
                @if($order->billing_address_2){{ $order->billing_address_2 }}<br>@endif
                {{ $order->billing_postcode }} {{ $order->billing_city }}<br>
                {{ $order->billing_country }}
                @if($order->billing_phone)<br>{{ $order->billing_phone }}@endif
                <br>{{ $order->billing_email }}
            </div>
        </div>
        @if($order->shipping_first_name)
            <div class="info-block">
                <div class="info-label">Livraison</div>
                <div class="info-content">
                    {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                    {{ $order->shipping_address_1 }}<br>
                    @if($order->shipping_address_2){{ $order->shipping_address_2 }}<br>@endif
                    {{ $order->shipping_postcode }} {{ $order->shipping_city }}<br>
                    {{ $order->shipping_country }}
                </div>
            </div>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>D&eacute;signation</th>
                <th class="text-center">Qt&eacute;</th>
                <th class="text-right">P.U. TTC</th>
                <th class="text-right">Total TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->sku)<br><span class="addon">R&eacute;f : {{ $item->sku }}</span>@endif
                        @foreach($item->addons as $addon)
                            <div class="addon">{{ $addon->addon_label }} : {{ $addon->addon_value }}</div>
                        @endforeach
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price + $item->addons_price, 2, ',', ' ') }} &euro;</td>
                    <td class="text-right">{{ number_format($item->total, 2, ',', ' ') }} &euro;</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $tvaRate = 20;
        $ttc = $order->total;
        $ht = round($ttc / (1 + $tvaRate / 100), 2);
        $tva = round($ttc - $ht, 2);
    @endphp

    <table class="totals-table">
        <tr>
            <td>Sous-total</td>
            <td class="text-right">{{ number_format($order->subtotal, 2, ',', ' ') }} &euro;</td>
        </tr>
        @if($order->discount_total > 0)
            <tr>
                <td>Remise</td>
                <td class="text-right">-{{ number_format($order->discount_total, 2, ',', ' ') }} &euro;</td>
            </tr>
        @endif
        <tr>
            <td>Livraison</td>
            <td class="text-right">{{ $order->shipping_total > 0 ? number_format($order->shipping_total, 2, ',', ' ') . ' €' : 'Gratuit' }}</td>
        </tr>
        <tr>
            <td>Total HT</td>
            <td class="text-right">{{ number_format($ht, 2, ',', ' ') }} &euro;</td>
        </tr>
        <tr>
            <td>TVA {{ $tvaRate }} %</td>
            <td class="text-right">{{ number_format($tva, 2, ',', ' ') }} &euro;</td>
        </tr>
        <tr class="total-row">
            <td>Total TTC</td>
            <td class="text-right">{{ number_format($ttc, 2, ',', ' ') }} &euro;</td>
        </tr>
    </table>

    <div class="payment-info">
        @if($order->payment_method)
            Paiement : {{ $order->payment_method }}
        @endif
        @if($order->paid_at)
            &mdash; R&eacute;gl&eacute;e le {{ $order->paid_at->format('d/m/Y &\a\g\r\a\v\e; H:i') }}
        @endif
    </div>

    <div class="footer">
        Institut Corps &agrave; Coeur &mdash; M&eacute;zidon Canon &mdash; institutcorpsacoeur.fr
    </div>
</body>
</html>
