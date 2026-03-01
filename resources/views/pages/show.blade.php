<x-layouts.app :title="$page->meta_title ?? $page->title" :meta-description="$page->meta_description">

{{-- Breadcrumb --}}
@if($page->parent)
<nav class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 text-sm" style="color: #60916a;">
    <a href="{{ route('home') }}" class="hover:opacity-70 transition-opacity">Accueil</a>
    <span class="mx-1.5">/</span>
    <a href="{{ route('page.show', $page->parent->slug) }}" class="hover:opacity-70 transition-opacity">{{ $page->parent->title }}</a>
    <span class="mx-1.5">/</span>
    <span style="color: #276e44;" class="font-medium">{{ $page->title }}</span>
</nav>
@endif

{{-- Hero --}}
<header class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6 text-center">
    <h1 class="text-3xl sm:text-4xl font-semibold italic mb-3"
        style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
        {{ $page->title }}
    </h1>
    @if($page->excerpt)
        <p class="text-base italic max-w-2xl mx-auto" style="color: #60916a;">
            {{ $page->excerpt }}
        </p>
    @endif
    <div class="mt-5 mx-auto w-16 border-t-2" style="border-color: #b0f1b9;"></div>
</header>

{{-- Featured image --}}
@if($page->featured_image)
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
    <div class="aspect-video rounded-xl overflow-hidden bg-gray-50">
        <img src="{{ $page->featured_image }}" alt="{{ $page->title }}"
             class="w-full h-full object-cover">
    </div>
</div>
@endif

{{-- Content --}}
<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="page-content">
        {!! $page->content !!}
    </div>

    {{-- CTA Rendez-vous --}}
    <div class="mt-12 text-center">
        <a href="https://www.planity.com/institut-corps-a-coeur-14270-mezidon-vallee-dauge"
           target="_blank" rel="noopener"
           class="inline-block text-sm font-semibold px-8 py-3 rounded-lg text-white transition hover:opacity-90"
           style="background-color: #276e44;">
            Prendre rendez-vous
        </a>
    </div>
</article>

</x-layouts.app>
