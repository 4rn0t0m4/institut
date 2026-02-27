<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} — Institut Corps à Coeur</title>
    <meta name="description" content="{{ $metaDescription ?? '' }}">
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
