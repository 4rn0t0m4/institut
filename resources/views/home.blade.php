<x-layouts.app title="Institut de beauté et cosmétiques naturels à Mézidon Canon" meta-description="Institut Corps à Cœur : cosmétiques naturels et dermo-cosmétiques sélectionnés par une esthéticienne passionnée. Soins du visage, massages, diagnostics de peau et boutique en ligne. Livraison rapide partout en France.">

{{-- Hero --}}
<section class="relative overflow-hidden" style="background-color: #c9fad9;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-sm uppercase tracking-widest mb-4 font-medium" style="color: #60916a;">
                    Institut de beauté &amp; bien-être — Mézidon Canon
                </p>
                <h1 class="text-4xl md:text-5xl font-semibold leading-tight mb-6" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                    Institut<br>Corps à Cœur
                </h1>
                <p class="text-lg mb-4 leading-relaxed" style="color: #276e44;">
                    Cosmétiques naturels et dermo-cosmétiques sélectionnés par une esthéticienne passionnée.
                </p>
                <p class="text-base mb-8 leading-relaxed" style="color: #60916a;">
                    Soins du visage, massages, diagnostics de peau et boutique en ligne.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('shop.index') }}"
                       class="inline-block font-semibold px-8 py-3.5 rounded-xl transition text-sm text-white text-center"
                       style="background-color: #276e44;">
                        Découvrir la boutique
                    </a>
                    <a href="https://www.planity.com/institut-corps-a-coeur-14270-mezidon-vallee-dauge" target="_blank" rel="noopener"
                       class="inline-block font-medium px-8 py-3.5 rounded-xl transition text-sm border text-center"
                       style="border-color: #276e44; color: #276e44; background: white;">
                        Prendre rendez-vous →
                    </a>
                </div>
            </div>
            <div class="hidden lg:block"
                 x-data="{ offset: 0 }"
                 x-on:scroll.window.throttle.16ms="offset = window.scrollY">
                <div class="aspect-square rounded-3xl overflow-hidden">
                    <img src="{{ asset('images/hero.jpg') }}" alt="Institut Corps à Cœur — Espace bien-être et cosmétiques naturels"
                         class="w-full object-cover"
                         loading="eager"
                         :style="'height: 120%; margin-top: -10%; transform: translateY(' + (offset * 0.15) + 'px)'">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Promo banner --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 mb-8 relative z-10">
    @include('partials.promo-banner')
</div>

{{-- Pourquoi nous choisir --}}
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-4" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Pourquoi choisir Corps à Cœur ?
        </h2>
        <p class="text-center mb-12 max-w-2xl mx-auto" style="color: #60916a;">
            Une sélection exigeante de cosmétiques professionnels, testés et approuvés en institut.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            @foreach([
                ['Conseils personnalisés', 'Une esthéticienne diplômée vous guide vers les produits adaptés à votre peau.', 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ['Produits testés en institut', 'Chaque produit est utilisé en cabine avant d\'être proposé à la vente.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['Livraison rapide', 'Expédition soignée sous 24-48h, en Colissimo ou point relais.', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['Marques professionnelles', 'Des gammes sélectionnées pour leur efficacité et leurs ingrédients naturels.', 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                ['Diagnostic de peau', 'Un quiz personnalisé pour identifier votre type de peau et vos besoins.', 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
            ] as [$title, $desc, $icon])
                <div class="text-center p-5 rounded-2xl border transition hover:shadow-md" style="border-color: #e2f5e9; background-color: #f0fdf4;">
                    <div class="w-12 h-12 mx-auto mb-4 flex items-center justify-center rounded-full" style="background-color: #b0f1b9; color: #276e44;">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold mb-2" style="color: #276e44;">{{ $title }}</h3>
                    <p class="text-xs leading-relaxed" style="color: #60916a;">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Nos univers / marques --}}
@if($brands->isNotEmpty())
<section class="py-16" style="background-color: #f8faf9;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-4" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Nos univers
        </h2>
        <p class="text-center mb-12 max-w-2xl mx-auto" style="color: #60916a;">
            Des marques soigneusement sélectionnées pour leur qualité et leur engagement envers des soins naturels et efficaces.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach($brands as $brand)
                <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}"
                   class="group relative rounded-2xl overflow-hidden transition hover:shadow-lg"
                   style="min-height: 260px;">
                    @if($brand->image_path)
                        <img src="{{ asset('storage/' . $brand->image_path) }}" alt="{{ $brand->name }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                             loading="lazy">
                        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.15) 100%);"></div>
                        <div class="relative z-10 flex flex-col justify-end h-full p-6 text-white">
                            <h3 class="text-xl font-semibold mb-2" style="font-family: 'Source Serif Pro', Georgia, serif;">{{ $brand->name }}</h3>
                            @if($brand->description)
                                <p class="text-sm leading-relaxed opacity-90">{{ $brand->description }}</p>
                            @endif
                            <p class="text-xs mt-3 font-medium opacity-75">{{ $brand->products_count }} produit{{ $brand->products_count > 1 ? 's' : '' }} →</p>
                        </div>
                    @else
                        <div class="flex flex-col justify-end h-full p-6" style="background: linear-gradient(135deg, {{ $brand->color ?? '#276e44' }}22 0%, {{ $brand->color ?? '#276e44' }}44 100%); min-height: 260px;">
                            <div class="w-14 h-14 mb-4 flex items-center justify-center rounded-full" style="background-color: {{ $brand->color ?? '#276e44' }}33;">
                                <svg class="w-7 h-7" style="color: {{ $brand->color ?? '#276e44' }};" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2" style="color: {{ $brand->color ?? '#276e44' }}; font-family: 'Source Serif Pro', Georgia, serif;">{{ $brand->name }}</h3>
                            @if($brand->description)
                                <p class="text-sm leading-relaxed" style="color: {{ $brand->color ?? '#276e44' }}cc;">{{ $brand->description }}</p>
                            @endif
                            <p class="text-xs mt-3 font-medium" style="color: {{ $brand->color ?? '#276e44' }}99;">{{ $brand->products_count }} produit{{ $brand->products_count > 1 ? 's' : '' }} →</p>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Services / Prestations --}}
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-10" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Nos prestations en institut
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
                   class="group text-center p-5 rounded-2xl border transition hover:shadow-md"
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
            <h2 class="text-2xl md:text-3xl font-semibold" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Nos produits phares
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

{{-- Diagnostic de peau --}}
@if($quiz)
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs uppercase tracking-widest mb-3 font-medium" style="color: #60916a;">Diagnostic</p>
        <h2 class="text-3xl font-semibold mb-4" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Quel est votre type de peau ?
        </h2>
        <p class="mb-8 leading-relaxed" style="color: #60916a;">
            Répondez à quelques questions pour découvrir votre profil de peau et les soins adaptés.
        </p>
        <a href="{{ route('quiz.show') }}"
           class="inline-block font-semibold px-8 py-3.5 rounded-xl text-sm text-white transition hover:opacity-90"
           style="background-color: #276e44;">
            Faire le diagnostic →
        </a>
    </div>
</section>
@endif

{{-- Réassurance SEO --}}
<section class="py-12" style="background-color: #f0fdf4;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach([
                ['Paiement sécurisé', 'CB, Visa, Mastercard via Stripe', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['Livraison offerte', 'Dès 60 € d\'achat en France', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['Retrait en institut', 'Gratuit à Mézidon Canon', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                ['Service client', 'Réponse sous 24h', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ] as [$title, $desc, $icon])
                <div>
                    <div class="w-10 h-10 mx-auto mb-3 flex items-center justify-center rounded-full" style="background-color: #b0f1b9; color: #276e44;">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold" style="color: #276e44;">{{ $title }}</p>
                    <p class="text-xs mt-1" style="color: #60916a;">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

</x-layouts.app>
