@props([
    'title'           => null,
    'metaDescription' => null,
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
