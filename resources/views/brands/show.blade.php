<x-layouts.app
    :title="$brand->meta_title ?: $brand->name . ' — Cosmétiques naturels'"
    :meta-description="$brand->meta_description ?: 'Découvrez la gamme ' . $brand->name . ' chez Institut Corps à Cœur. Cosmétiques naturels sélectionnés par une esthéticienne passionnée. Livraison rapide en France.'">

{{-- Hero marque --}}
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, {{ $brand->color ?? '#276e44' }}15 0%, {{ $brand->color ?? '#276e44' }}30 100%);">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <nav class="text-sm mb-6" style="color: {{ $brand->color ?? '#276e44' }}99;">
                    <a href="{{ route('home') }}" class="hover:underline">Accueil</a>
                    <span class="mx-2">›</span>
                    <a href="{{ route('brands.index') }}" class="hover:underline">Nos marques</a>
                    <span class="mx-2">›</span>
                    <span>{{ $brand->name }}</span>
                </nav>
                <h1 class="text-4xl md:text-5xl font-semibold leading-tight mb-6" style="color: {{ $brand->color ?? '#276e44' }}; font-family: 'Source Serif Pro', Georgia, serif;">
                    {{ $brand->name }}
                </h1>
                @if($brand->description)
                    <p class="text-lg leading-relaxed mb-8" style="color: {{ $brand->color ?? '#276e44' }}cc;">
                        {{ $brand->description }}
                    </p>
                @endif
                <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}"
                   class="inline-block font-semibold px-8 py-3.5 rounded-xl transition text-sm text-white hover:opacity-90"
                   style="background-color: {{ $brand->color ?? '#276e44' }};">
                    Voir les {{ $brand->products()->where('is_active', true)->count() }} produits {{ $brand->name }}
                </a>
            </div>
            @if($brand->image_path)
                <div class="hidden lg:block">
                    <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-lg">
                        <img src="{{ asset('storage/' . $brand->image_path) }}" alt="{{ $brand->name }}"
                             class="w-full h-full object-cover" loading="eager">
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- Contenu rédactionnel --}}
@if($brand->content)
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg max-w-none
                    prose-headings:font-semibold prose-headings:tracking-tight
                    prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-4
                    prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-3
                    prose-p:leading-relaxed prose-p:mb-4
                    prose-li:leading-relaxed
                    prose-a:no-underline hover:prose-a:underline
                    prose-img:rounded-xl prose-img:shadow-md"
             style="--tw-prose-headings: {{ $brand->color ?? '#276e44' }}; --tw-prose-links: {{ $brand->color ?? '#276e44' }};">
            {!! $brand->content !!}
        </div>
    </div>
</section>
@endif

{{-- Produits de la marque --}}
@if($products->isNotEmpty())
<section class="py-16" style="background-color: {{ $brand->color ?? '#276e44' }}0a;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-semibold" style="color: {{ $brand->color ?? '#276e44' }}; font-family: 'Source Serif Pro', Georgia, serif;">
                Les produits {{ $brand->name }}
            </h2>
            <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}" class="text-sm font-medium hover:underline" style="color: {{ $brand->color ?? '#276e44' }};">
                Voir tout →
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($products as $product)
                <x-product-card :product="$product"/>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-semibold mb-4" style="color: {{ $brand->color ?? '#276e44' }}; font-family: 'Source Serif Pro', Georgia, serif;">
            Besoin d'un conseil personnalisé ?
        </h2>
        <p class="mb-8 leading-relaxed" style="color: #60916a;">
            En tant qu'esthéticienne, je connais chaque produit {{ $brand->name }} pour les utiliser quotidiennement en institut.
            N'hésitez pas à me contacter pour un conseil adapté à votre peau.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact.show') }}"
               class="inline-block font-semibold px-8 py-3.5 rounded-xl transition text-sm text-white hover:opacity-90"
               style="background-color: {{ $brand->color ?? '#276e44' }};">
                Me contacter
            </a>
            <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}"
               class="inline-block font-medium px-8 py-3.5 rounded-xl transition text-sm border hover:opacity-90"
               style="border-color: {{ $brand->color ?? '#276e44' }}; color: {{ $brand->color ?? '#276e44' }};">
                Voir la boutique {{ $brand->name }}
            </a>
        </div>
    </div>
</section>

</x-layouts.app>
