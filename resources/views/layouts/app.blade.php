<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $pageTitle = ($title ?? config('app.name')) . ' — Institut Corps à Coeur';
        $pageDesc  = $metaDescription ?? 'Institut de beauté et bien-être à Mézidon Canon, près de Caen. Soins visage, massages, balnéothérapie, boutique cosmétiques.';
        $pageImage = $ogImage ?? asset('images/og-default.jpg');
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="Institut Corps à Coeur">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    {{-- Google Analytics + Google Ads (gtag.js) --}}
    @php
        $gaId = \App\Models\Setting::get('google_analytics_id');
        $adsId = \App\Models\Setting::get('google_ads_id');
    @endphp
    @if ($gaId || $adsId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId ?: $adsId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}

        // Consentement RGPD par défaut (désactivé pour l'UE)
        gtag('consent', 'default', {
            analytics_storage: 'denied',
            ad_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied',
            region: ['AT','BE','BG','HR','CY','CZ','DK','EE','FI','FR','DE','GR','HU','IE','IT','LV','LT','LU','MT','NL','PL','PT','RO','SK','SI','ES','SE','IS','LI','NO','GB'],
            wait_for_update: 500,
        });

        gtag('js', new Date());
        gtag('set', 'linker', {'domains': ['institutcorpsacoeur.fr']});
        @if ($gaId)
        gtag('config', '{{ $gaId }}');
        @endif
        @if ($adsId)
        gtag('config', '{{ $adsId }}', {'send_page_view': false});
        @endif
    </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 font-sans antialiased" x-data>

    {{-- Bannière sticky --}}
    @include('partials.sticky-banner')

    {{-- Header --}}
    @include('partials.header')

    {{-- Contenu principal --}}
    <main id="main-content" class="min-h-screen">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Notifications flash Turbo Stream --}}
    @include('partials.flash')

</body>
</html>
