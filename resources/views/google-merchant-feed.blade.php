<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
<channel>
    <title>Institut Corps &amp; Cœur</title>
    <link>{{ config('app.url') }}</link>
    <description>Flux produits Google Shopping — Institut Corps &amp; Cœur</description>
    @foreach ($products as $product)
    @php
        $price = number_format($product->currentPrice(), 2, '.', '');
        $salePrice = $product->sale_price ? number_format($product->sale_price, 2, '.', '') : null;
        $availability = $product->stock_status === 'outofstock' ? 'out_of_stock' : 'in_stock';
        $rawImage = $product->featuredImage?->url ?? '';
        $imageUrl = $rawImage ? (str_starts_with($rawImage, 'http') ? $rawImage : rtrim(url('/'), '/') . $rawImage) : '';
        $brand = $product->brand?->name ?? 'Institut Corps & Cœur';
        $clean = fn(?string $s) => trim(preg_replace('/\s+/', ' ',
            preg_replace('/[\x{1F000}-\x{1FFFF}]|[\x{2600}-\x{27FF}]|[\x{2B00}-\x{2BFF}]|[\x{FE00}-\x{FE0F}]/u', '',
                html_entity_decode(strip_tags($s ?? ''), ENT_QUOTES, 'UTF-8')
            )
        ));

        $parts = array_filter([
            $clean($product->short_description),
            $clean($product->description),
            $clean($product->benefits),
            $clean($product->usage_instructions),
            $clean($product->composition),
            $clean($product->team_recommendation),
        ]);
        $description = implode(' ', $parts) ?: $product->name;
        $url = $product->url();
        $cat = $product->category;
        $productType = $cat?->parent ? ($cat->parent->name . ' > ' . $cat->name) : $cat?->name;
    @endphp
    @if ($imageUrl)
    <item>
        <g:id>{{ $product->sku ?: 'prod-' . $product->id }}</g:id>
        <g:title><![CDATA[{!! $product->name !!}]]></g:title>
        <g:description><![CDATA[{!! Str::limit($description, 5000) !!}]]></g:description>
        <g:link>{{ $url }}</g:link>
        <g:image_link>{{ $imageUrl }}</g:image_link>
        <g:price>{{ $price }} EUR</g:price>
        @if ($salePrice)
        <g:sale_price>{{ $salePrice }} EUR</g:sale_price>
        @endif
        <g:availability>{{ $availability }}</g:availability>
        <g:condition>new</g:condition>
        <g:brand><![CDATA[{!! $brand !!}]]></g:brand>
        @if ($product->sku)
        <g:mpn>{{ $product->sku }}</g:mpn>
        @else
        <g:identifier_exists>no</g:identifier_exists>
        @endif
        @if ($productType)
        <g:product_type><![CDATA[{!! $productType !!}]]></g:product_type>
        @endif
        @if ($product->unit_measure)
        <g:unit_pricing_measure>{{ $product->unit_measure }}</g:unit_pricing_measure>
        <g:unit_pricing_base_measure>100 {{ Str::contains($product->unit_measure, 'ml') ? 'ml' : 'g' }}</g:unit_pricing_base_measure>
        @endif
    </item>
    @endif
    @endforeach
</channel>
</rss>
