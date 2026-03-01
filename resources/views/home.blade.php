<x-layouts.app title="Institut de beauté et bien-être à Mézidon Canon" meta-description="Institut Corps à Coeur, votre institut de beauté et bien-être près de Caen. Épilations, massages, soins visage, balnéothérapie et cosmétiques naturels. Prenez rendez-vous au 02 31 20 10 45.">

{{-- Hero --}}
<section class="relative overflow-hidden" style="background-color: #c9fad9;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-sm uppercase tracking-widest mb-4 font-medium" style="color: #60916a;">
                    Institut de beauté &amp; bien-être — Caen
                </p>
                <h1 class="text-4xl md:text-5xl font-semibold leading-tight mb-6" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                    Institut<br>Corps à Coeur
                </h1>
                <p class="text-lg mb-4 leading-relaxed" style="color: #276e44; font-style: italic; font-weight: 300;">
                    Épilations, massages, soins visage, cosmétiques naturels… Un espace dédié à votre bien-être.
                </p>
                <p class="text-sm mb-8" style="color: #60916a;">
                    Lun–Ven 9h–18h &nbsp;|&nbsp; Sam sur rendez-vous &nbsp;|&nbsp; 02 31 20 10 45
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('shop.index') }}"
                       class="inline-block font-semibold px-8 py-3.5 rounded-xl transition text-sm text-white"
                       style="background-color: #276e44;">
                        Découvrir la boutique
                    </a>
                    <a href="https://www.planity.com" target="_blank" rel="noopener"
                       class="inline-block font-medium px-8 py-3.5 rounded-xl transition text-sm border"
                       style="border-color: #276e44; color: #276e44; background: white;">
                        Prendre rendez-vous →
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="aspect-square rounded-3xl overflow-hidden">
                    <img src="{{ asset('images/hero.jpg') }}" alt="Institut Corps à Coeur — Espace bien-être" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Services --}}
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-semibold text-center mb-10" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Nos prestations
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach([
                ['Épilations', 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18', 'epilations'],
                ['Massages &amp; Relaxation', 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'massages-du-monde'],
                ['Soins Visage', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'soins-visage'],
                ['Sportifs', 'M13 10V3L4 14h7v7l9-11h-7z', 'sportifs'],
                ['Amincissement', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z', 'amincissement'],
                ['Onglerie', 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'onglerie'],
                ['Regard', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'regard'],
                ['Coffrets Cadeaux', 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7', 'boutique'],
            ] as [$name, $icon, $slug])
                <a href="{{ $slug === 'boutique' ? route('shop.index') : route('page.show', $slug) }}"
                   class="group text-center p-5 rounded-2xl border transition"
                   style="border-color: #b0f1b9; background-color: #f0fdf4;">
                    <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center rounded-full"
                         style="background-color: #b0f1b9; color: #276e44;">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium leading-tight" style="color: #276e44;">{!! $name !!}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Produits vedettes --}}
@if($featuredProducts->isNotEmpty())
<section class="py-16" style="background-color: #c9fad9;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-semibold" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Nos produits
            </h2>
            <a href="{{ route('shop.index') }}" class="text-sm font-medium hover:underline" style="color: #276e44;">
                Voir toute la boutique →
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product"/>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Quiz type de peau --}}
@if($quiz)
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs uppercase tracking-widest mb-3 font-medium" style="color: #60916a;">Quiz</p>
        <h2 class="text-3xl font-semibold mb-4" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Quel est votre type de peau ?
        </h2>
        <p class="mb-8 leading-relaxed" style="color: #60916a; font-style: italic;">
            Répondez à quelques questions pour découvrir votre profil de peau et les soins adaptés.
        </p>
        <a href="{{ route('quiz.show', $quiz->slug) }}"
           class="inline-block font-semibold px-8 py-3.5 rounded-xl text-sm text-white transition hover:opacity-90"
           style="background-color: #276e44;">
            Faire le quiz →
        </a>
    </div>
</section>
@endif


</x-layouts.app>
