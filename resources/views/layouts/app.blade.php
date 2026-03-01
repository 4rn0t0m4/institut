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
