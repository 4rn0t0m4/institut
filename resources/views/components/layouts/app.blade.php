@props([
    'title'           => null,
    'metaDescription' => null,
    'noindex'         => false,
])

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' : '' }}Institut Corps à Coeur</title>
    @if($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if($noindex)
        <meta name="robots" content="noindex, nofollow">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
</head>
<body class="bg-white text-gray-900 antialiased" x-data>

    @include('partials.sticky-banner')
    @include('partials.header')

    <main id="main-content" class="min-h-screen">
        {{ $slot }}
    </main>

    @include('partials.social-bar')
    @include('partials.footer')
    @include('partials.flash')

</body>
</html>
