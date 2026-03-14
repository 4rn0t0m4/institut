<x-layouts.app :title="$product->meta_title ?: $product->name" :meta-description="$product->meta_description ?: $product->short_description" og-type="product">

@if($product->featuredImage?->url)
@push('head')
<link rel="preload" as="image" href="{{ $product->featuredImage->url }}">
@endpush
@endif

@if($product->personalizable)
@push('head')
@php $googleFonts = collect(config('personalization.fonts'))->pluck('google')->implode('&family='); @endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family={{ $googleFonts }}&display=swap" rel="stylesheet">
@endpush
@endif

{{-- Schema.org Product + BreadcrumbList --}}
@php
$productSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    'description' => strip_tags($product->short_description ?? $product->description ?? ''),
    'image' => $product->featuredImage?->url ? [url($product->featuredImage->url)] : [],
    'sku' => $product->sku ?: 'prod-' . $product->id,
    'brand' => [
        '@type' => 'Brand',
        'name' => $product->brand?->name ?? 'Institut Corps à Coeur',
    ],
    'offers' => [
        '@type' => 'Offer',
        'url' => url()->current(),
        'priceCurrency' => 'EUR',
        'price' => number_format($product->currentPrice(), 2, '.', ''),
        'availability' => $product->stock_status === 'outofstock'
            ? 'https://schema.org/OutOfStock'
            : 'https://schema.org/InStock',
    ],
];
if ($reviews->count() > 0) {
    $avgRating = round($reviews->avg('rating'), 1);
    $productSchema['aggregateRating'] = [
        '@type' => 'AggregateRating',
        'ratingValue' => $avgRating,
        'reviewCount' => $reviews->count(),
        'bestRating' => 5,
        'worstRating' => 1,
    ];
    $productSchema['review'] = $reviews->map(fn($r) => array_filter([
        '@type'         => 'Review',
        'name'          => $r->title ?: null,
        'reviewBody'    => $r->body,
        'datePublished' => \Carbon\Carbon::parse($r->created_at)->toDateString(),
        'author'        => ['@type' => 'Person', 'name' => $r->author_name],
        'reviewRating'  => [
            '@type'       => 'Rating',
            'ratingValue' => $r->rating,
            'bestRating'  => 5,
            'worstRating' => 1,
        ],
    ]))->values()->all();
}
$productJsonLd = json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$breadcrumbItems = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Boutique', 'item' => route('shop.index')],
];
$pos = 2;
if ($product->category?->parent) {
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $product->category->parent->name, 'item' => $product->category->parent->url()];
}
if ($product->category) {
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $product->category->name, 'item' => $product->category->url()];
}
$breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $pos, 'name' => $product->name];
$breadcrumbJsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $breadcrumbItems,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@endphp
<script type="application/ld+json">{!! $productJsonLd !!}</script>
<script type="application/ld+json">{!! $breadcrumbJsonLd !!}</script>

@if(!$product->is_active && auth()->user()?->is_admin)
    <div style="background-color: #f59e0b; color: white; font-size: 14px; font-weight: 600; text-align: center; padding: 10px 0;" role="status">
        Ce produit est masqué — visible uniquement par les administrateurs
        <a href="{{ route('admin.products.edit', $product) }}" style="color: white; text-decoration: underline; margin-left: 8px;">Modifier</a>
    </div>
@endif

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Fil d'ariane --}}
    <nav class="text-xs mb-8 flex items-center gap-2" style="color: #4d7c5a;">
        <a href="{{ route('shop.index') }}" class="hover:underline">Boutique</a>
        @if($product->category)
            @if($product->category->parent)
                <span>/</span>
                <a href="{{ $product->category->parent->url() }}"
                   class="hover:underline">{{ $product->category->parent->name }}</a>
            @endif
            <span>/</span>
            <a href="{{ $product->category->url() }}"
               class="hover:underline">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span style="color: #276e44;">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

        {{-- Galerie --}}
        @php
            $galleryImages = $product->galleryImages();
            $allImages = collect();
            if ($product->featuredImage) $allImages->push($product->featuredImage);
            $allImages = $allImages->merge($galleryImages);
        @endphp
        <div x-data="{ active: 0, lightbox: false, images: {{ $allImages->pluck('url')->toJson() }} }">
            {{-- Image principale --}}
            <div class="aspect-square rounded-3xl overflow-hidden mb-3 relative group"
                 style="background-color: #f0fdf4;">
                @if($allImages->isNotEmpty())
                    @foreach($allImages as $i => $img)
                        <img src="{{ $img->url }}"
                             alt="{{ $img->alt ?: $product->name }}"
                             @if($img->width && $img->height) width="{{ $img->width }}" height="{{ $img->height }}" @endif
                             x-show="active === {{ $i }}"
                             @if($i === 0) fetchpriority="high" style="display: block" @else loading="lazy" @endif
                             class="w-full h-full object-cover cursor-zoom-in"
                             @click="lightbox = true">
                    @endforeach
                    {{-- Icône zoom --}}
                    <div class="absolute bottom-3 right-3 rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition pointer-events-none"
                         style="background-color: rgba(0,0,0,0.4);">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16zm3-8H8m3-3v6"/>
                        </svg>
                    </div>
                @else
                    <div class="w-full h-full flex items-center justify-center" style="color: #b0f1b9;">
                        <svg class="w-24 h-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Miniatures si plusieurs images --}}
            @if($allImages->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($allImages as $i => $img)
                        <button @click="active = {{ $i }}"
                                class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 transition"
                                :class="active === {{ $i }} ? 'border-[#276e44]' : 'border-transparent'">
                            <img src="{{ $img->url }}" alt="{{ $img->alt ?: $product->name }}"
                                 loading="lazy" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Lightbox --}}
            @if($allImages->isNotEmpty())
                <div x-show="lightbox" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     style="background-color: rgba(0,0,0,0.88);"
                     @click.self="lightbox = false"
                     @keydown.escape.window="lightbox = false">
                    <button @click="lightbox = false"
                            class="absolute top-4 right-5 text-white opacity-60 hover:opacity-100 transition text-4xl leading-none font-light"
                            aria-label="Fermer">×</button>
                    <img :src="images[active]"
                         alt="{{ $product->name }}"
                         class="max-h-[90vh] max-w-[90vw] object-contain rounded-2xl shadow-2xl">
                </div>
            @endif
        </div>

        {{-- Infos produit --}}
        <div x-data="productForm({{ $product->price }}, {{ $product->sale_price ?? 'null' }}, {{ $product->personalizable ? ($product->personalization_price ?? 0) : 'null' }})">
            @if($product->brand)
                <p class="text-xs uppercase tracking-widest mb-1 font-semibold">
                    <a href="{{ route('shop.index', ['marque' => $product->brand->slug]) }}" style="color: #276e44;" class="hover:underline">
                        {{ $product->brand->name }}
                    </a>
                </p>
            @endif
            <p class="text-xs uppercase tracking-widest mb-2 font-medium" style="color: #4d7c5a;">
                {{ $product->category?->name }}
            </p>
            <h1 class="text-3xl md:text-4xl font-semibold leading-tight mb-3" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                {{ $product->name }}
            </h1>

            {{-- Etoiles + lien avis --}}
            <div class="flex items-center gap-2 mb-5">
                @if($reviews->isNotEmpty())
                    @php $avgRating = round($reviews->avg('rating'), 1); @endphp
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4" fill="{{ $i <= round($avgRating) ? '#f59e0b' : '#e5e7eb' }}" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <a href="#avis" class="text-xs hover:underline" style="color: #4d7c5a;">{{ $reviews->count() }} avis</a>
                @else
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4" fill="#e5e7eb" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <a href="#avis" class="text-xs hover:underline" style="color: #4d7c5a;">Donner mon avis</a>
                @endif
            </div>

            @if($product->tags->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach($product->tags as $tag)
                        <a href="{{ route('shop.index', ['tag' => $tag->slug]) }}"
                           class="inline-block text-xs px-3 py-1 rounded-full border transition hover:opacity-80"
                           style="border-color: #b0f1b9; color: #276e44; background-color: #ecfdf5;">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Prix --}}
            <div class="flex items-baseline gap-3 mb-6">
                <span class="text-3xl font-bold" style="color: #276e44;" x-text="formatPrice(total)"></span>
                @if($product->sale_price)
                    <span class="text-base line-through" style="color: #4d7c5a;">
                        {{ number_format($product->price, 2, ',', ' ') }} €
                    </span>
                @endif
            </div>

            {{-- Séparateur --}}
            <div style="width: 3rem; height: 2px; background-color: #b0f1b9; margin-bottom: 1.5rem;"></div>

            {{-- Description courte --}}
            @if($product->short_description)
                <div class="mb-6 leading-relaxed" style="color: #374151; font-size: 0.95rem;">
                    {!! $product->short_description !!}
                </div>
            @endif

            {{-- Recommandation équipe --}}
            @if($product->team_recommendation)
                <div class="mb-6 rounded-xl p-4 flex gap-3"
                     style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a;">
                    <div class="shrink-0 mt-0.5">
                        <svg class="w-5 h-5" style="color: #d97706;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #92400e;">Notre conseil</p>
                        <p class="text-sm leading-relaxed" style="color: #78350f;">{{ $product->team_recommendation }}</p>
                    </div>
                </div>
            @endif

            {{-- Stock --}}
            @if($product->stock_status !== 'instock')
                <div class="mb-6 rounded-xl p-4" style="background-color: #fef2f2; border: 1px solid #fecaca;">
                    <p class="text-sm font-medium mb-3" style="color: #dc2626;">Produit épuisé</p>

                    @if(session('stock_alert'))
                        <p class="text-sm" style="color: #276e44;">{{ session('stock_alert') }}</p>
                    @else
                        <p class="text-xs mb-2" style="color: #374151;">Recevez une alerte dès son retour en stock :</p>
                        <form action="{{ route('shop.stock-notify', $product) }}" method="POST" class="flex gap-2" data-turbo="false">
                            @csrf
                            <input type="email" name="email" required
                                   value="{{ auth()->user()?->email }}"
                                   placeholder="votre@email.fr"
                                   aria-label="Adresse email pour alerte stock"
                                   class="flex-1 text-sm px-3 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-600"
                                   style="border-color: #d1d5db;">
                            <button type="submit"
                                    class="text-white text-sm font-semibold px-4 py-2 rounded-lg transition hover:opacity-90"
                                    style="background-color: #276e44;">
                                M'alerter
                            </button>
                        </form>
                        @error('email')
                            <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            @endif

            {{-- Formulaire ajout panier --}}
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Addons --}}
                @if($product->addonAssignments->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($product->addonAssignments as $assignment)
                            <x-product-addon :addon="$assignment->addon"/>
                        @endforeach
                    </div>
                @endif

                {{-- Personnalisation --}}
                @if($product->personalizable)
                    @php
                        $persoFonts = config('personalization.fonts', []);
                        $persoColors = config('personalization.colors', []);
                        $firstFont = array_key_first($persoFonts);
                        $firstColor = array_key_first($persoColors);
                    @endphp
                    <div class="rounded-xl p-4 space-y-4" style="background-color: #f0fdf4; border: 1px solid #b0f1b9;">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold" style="color: #276e44;">Personnalisation</p>
                            @if($product->personalization_price > 0)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background-color: #e8fae8; color: #276e44;">
                                    +{{ number_format($product->personalization_price, 2, ',', ' ') }} €
                                </span>
                            @else
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background-color: #e8fae8; color: #276e44;">
                                    Gratuit
                                </span>
                            @endif
                        </div>

                        {{-- Texte --}}
                        <div>
                            <label for="perso_text" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre texte</label>
                            <input type="text" id="perso_text" name="personalization[text]"
                                   x-model="persoText" maxlength="50" placeholder="Ex : Marie, Joyeux anniversaire..."
                                   class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-700"
                                   style="border-color: #d1d5db; background-color: #ffffff;">
                            <p class="text-xs mt-1" style="color: #9ca3af;">50 caractères maximum</p>
                        </div>

                        {{-- Police --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color: #374151;">Police</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($persoFonts as $fontKey => $fontData)
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition"
                                           :class="persoFont === '{{ $fontKey }}' ? 'border-[#276e44] bg-[#e8fae8]' : 'border-gray-200 bg-white hover:border-gray-300'">
                                        <input type="radio" name="personalization[font]" value="{{ $fontKey }}"
                                               x-model="persoFont" class="sr-only">
                                        <span class="text-sm" style="font-family: '{{ $fontData['label'] }}', cursive;">{{ $fontData['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Couleur --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color: #374151;">Couleur</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($persoColors as $colorKey => $colorData)
                                    <label class="relative cursor-pointer group" title="{{ $colorData['label'] }}">
                                        <input type="radio" name="personalization[color]" value="{{ $colorKey }}"
                                               x-model="persoColor" class="sr-only">
                                        <span class="block w-8 h-8 rounded-full border-2 transition"
                                              :class="persoColor === '{{ $colorKey }}' ? 'border-[#276e44] scale-110' : 'border-gray-200 hover:border-gray-400'"
                                              style="background-color: {{ $colorData['hex'] }};{{ $colorData['hex'] === '#ffffff' ? ' box-shadow: inset 0 0 0 1px #d1d5db;' : '' }}"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Aperçu --}}
                        <div x-show="persoText.length > 0" x-transition>
                            <p class="text-xs font-medium mb-2" style="color: #9ca3af;">Aperçu</p>
                            <div class="rounded-lg p-4 text-center" style="background-color: #ffffff; border: 1px dashed #b0f1b9;">
                                <span class="text-2xl" :style="'font-family: \'' + persoFontLabel + '\', cursive; color: ' + persoColorHex">
                                    <span x-text="persoText"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Quantité + bouton --}}
                <div class="flex items-center gap-4 pt-2">
                    <div class="flex items-center rounded-xl overflow-hidden" style="border: 1px solid #b0f1b9;">
                        <button type="button"
                                @click="qty = Math.max(1, qty - 1)"
                                aria-label="Diminuer la quantité"
                                class="px-3.5 py-2.5 transition hover:bg-green-50" style="color: #276e44;">−</button>
                        <input type="number" name="quantity" x-model="qty"
                               min="1" max="99" aria-label="Quantité"
                               class="w-12 text-center py-2.5 border-0 text-sm focus:outline-none" style="color: #276e44;">
                        <button type="button"
                                @click="qty = Math.min(99, qty + 1)"
                                aria-label="Augmenter la quantité"
                                class="px-3.5 py-2.5 transition hover:bg-green-50" style="color: #276e44;">+</button>
                    </div>
                    <button type="submit"
                            {{ $product->stock_status !== 'instock' ? 'disabled' : '' }}
                            class="flex-1 text-white py-3 px-6 rounded-xl font-semibold text-sm transition disabled:cursor-not-allowed hover:opacity-90"
                            style="background-color: {{ $product->stock_status !== 'instock' ? '#9ca3af' : '#276e44' }};">
                        {{ $product->stock_status === 'instock' ? 'Ajouter au panier' : 'Produit épuisé' }}
                    </button>
                </div>
            </form>

            {{-- Informations livraison --}}
            @if($product->stock_status === 'instock')
                <div class="mt-5 pt-5 space-y-2.5" style="border-top: 1px solid #e5e7eb;">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" style="color: #276e44;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <span class="text-xs" style="color: #374151;">Livraison en point relais <strong>offerte dès 60&nbsp;€</strong> — sinon 5,00&nbsp;€</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" style="color: #276e44;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h15M9 5v14M5 19h10a2 2 0 002-2V7a2 2 0 00-2-2"/>
                        </svg>
                        <span class="text-xs" style="color: #374151;">Livraison à domicile Colissimo — 7,90&nbsp;€</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" style="color: #276e44;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-xs" style="color: #374151;">Retrait gratuit à l'institut</span>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Description complète --}}
@if($product->description)
    <section class="py-16" style="background-color: #c9fad9;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #4d7c5a;">Tout savoir</p>
            <h2 class="text-2xl font-semibold mb-6" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Description
            </h2>
            <div class="product-description max-w-3xl">
                {!! $product->description !!}
            </div>
        </div>
    </section>
@endif

{{-- Sections (bienfaits, utilisation, composition) --}}
@php
    $sections = [
        ['key' => 'benefits',           'label' => 'Bienfaits',            'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',                                                                      'gradient' => 'linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%)', 'border' => '#a7f3d0', 'iconBg' => '#d1fae5', 'iconColor' => '#059669'],
        ['key' => 'usage_instructions', 'label' => 'Comment l\'utiliser',  'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'gradient' => 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)',  'border' => '#93c5fd', 'iconBg' => '#dbeafe', 'iconColor' => '#2563eb'],
        ['key' => 'composition',        'label' => 'Composition',          'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',                                                                  'gradient' => 'linear-gradient(135deg, #fdf4ff 0%, #f3e8ff 100%)',  'border' => '#d8b4fe', 'iconBg' => '#f3e8ff', 'iconColor' => '#7c3aed'],
    ];
    $activeSections = collect($sections)->filter(fn($s) => !empty($product->{$s['key']}));
@endphp
@if($activeSections->isNotEmpty())
    <section class="py-14 bg-white border-b" style="border-color: #e5e7eb;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #4d7c5a;">En détail</p>
            <h2 class="text-2xl font-semibold mb-8" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Les essentiels à retenir
            </h2>
            <div class="grid grid-cols-1 gap-6 {{ $activeSections->count() === 1 ? 'max-w-xl' : ($activeSections->count() === 2 ? 'md:grid-cols-2 max-w-4xl' : 'md:grid-cols-3') }}">
                @foreach($sections as $section)
                    @if(!empty($product->{$section['key']}))
                        <div class="rounded-2xl p-6 transition-shadow hover:shadow-md"
                             style="background: {{ $section['gradient'] }}; border: 1px solid {{ $section['border'] }};">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                     style="background-color: {{ $section['iconBg'] }};">
                                    <svg class="w-5 h-5" style="color: {{ $section['iconColor'] }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $section['icon'] }}"/>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-base" style="color: #1f2937;">{{ $section['label'] }}</h3>
                            </div>
                            <div class="product-description leading-relaxed" style="color: #374151; font-size: 0.9375rem;">
                                {!! $product->{$section['key']} !!}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Avis clients --}}
<section id="avis" class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
        @endphp

        {{-- En-tête + résumé --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-10">
            <div>
                <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #4d7c5a;">Retours d'expérience</p>
                <h2 class="text-2xl font-semibold" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                    Avis clients
                    @if($reviews->isNotEmpty())
                        <span class="text-base font-normal" style="color: #4d7c5a;">({{ $reviews->count() }})</span>
                    @endif
                </h2>
            </div>

            @if($reviews->isNotEmpty())
                @php
                    $avgRating = round($reviews->avg('rating'), 1);
                    $distrib = [];
                    for ($s = 5; $s >= 1; $s--) {
                        $distrib[$s] = $reviews->where('rating', $s)->count();
                    }
                @endphp
                {{-- Bloc note + distribution --}}
                <div class="flex items-center gap-6 shrink-0">
                    <div class="text-center">
                        <div class="text-4xl font-bold" style="color: #276e44;">{{ number_format($avgRating, 1, ',', '') }}</div>
                        <div class="flex justify-center gap-0.5 my-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4" fill="{{ $i <= round($avgRating) ? '#f59e0b' : '#e5e7eb' }}" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                            @endfor
                        </div>
                        <div class="text-xs" style="color: #9ca3af;">sur 5</div>
                    </div>
                    <div class="space-y-1 min-w-[140px]">
                        @foreach($distrib as $stars => $count)
                            @php $pct = $reviews->count() > 0 ? round($count / $reviews->count() * 100) : 0; @endphp
                            <div class="flex items-center gap-2 text-xs" style="color: #6b7280;">
                                <span class="w-3 text-right">{{ $stars }}</span>
                                <svg class="w-3 h-3 shrink-0" fill="#f59e0b" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                                <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background-color: #e5e7eb;">
                                    <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: #f59e0b;"></div>
                                </div>
                                <span class="w-7 text-right">{{ $pct }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Liste des avis --}}
        @if($reviews->isNotEmpty())
            <div x-data="{ showAll: false }">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
                    @foreach($reviews as $index => $review)
                        @php $initial = mb_strtoupper(mb_substr($review->author_name, 0, 1)); @endphp
                        <div x-show="{{ $index }} < 6 || showAll"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="bg-white rounded-xl p-5 flex flex-col gap-3"
                             style="border: 1px solid #e5e7eb; border-left: 3px solid #276e44;">
                            <div class="flex items-start gap-3">
                                {{-- Avatar initiale --}}
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-white text-sm font-bold"
                                     style="background-color: #276e44;">{{ $initial }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-sm" style="color: #1f2937;">{{ $review->author_name }}</span>
                                            @if($review->is_verified_buyer)
                                                <span class="inline-flex items-center gap-1 text-xs px-1.5 py-0.5 rounded-full font-medium" style="background-color: #ecfdf5; color: #276e44;">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                    Achat vérifié
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs shrink-0" style="color: #9ca3af;">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center gap-0.5 mt-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5" fill="{{ $i <= $review->rating ? '#f59e0b' : '#d1d5db' }}" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            @if($review->title)
                                <p class="font-semibold text-sm" style="color: #374151;">{{ $review->title }}</p>
                            @endif
                            <p class="text-sm leading-relaxed" style="color: #6b7280;">{{ $review->body }}</p>
                        </div>
                    @endforeach
                </div>

                @if($reviews->count() > 6)
                    <div class="text-center mt-6">
                        <button @click="showAll = !showAll"
                                class="inline-flex items-center gap-2 text-sm font-medium px-5 py-2.5 rounded-xl border transition hover:bg-green-50"
                                style="color: #276e44; border-color: #276e44;"
                            <span x-text="showAll ? 'Voir moins' : 'Voir tous les avis ({{ $reviews->count() }})'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="showAll ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @else
            <p class="text-sm mb-8" style="color: #6b7280;">Aucun avis pour le moment. Soyez le premier à donner votre avis !</p>
        @endif

        {{-- Séparateur formulaire --}}
        <div class="mt-14 pt-10" style="border-top: 1px solid #e5e7eb;">
            <p class="text-xs uppercase tracking-widest font-medium mb-1" style="color: #4d7c5a;">Votre expérience</p>
            <h3 class="text-xl font-semibold mb-6" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Laisser un avis
            </h3>

            @if(session('review_success'))
                <div class="max-w-xl rounded-xl p-4 mb-6" style="background-color: #ecfdf5; border: 1px solid #b0f1b9; color: #276e44;">
                    <p class="text-sm font-medium">{{ session('review_success') }}</p>
                </div>
            @else
                <form action="{{ route('shop.review.store', $product) }}" method="POST"
                      class="max-w-xl space-y-5" data-turbo="false" x-data="{ rating: 0 }">
                    @csrf

                    {{-- Note --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium" style="color: #374151;">Votre note *</label>
                        <div class="flex gap-1.5">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}"
                                        aria-label="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}"
                                        class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-9 h-9" :fill="rating >= {{ $i }} ? '#f59e0b' : '#d1d5db'" viewBox="0 0 20 20">
                                        <path d="{{ $starPath }}"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                        @error('rating') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="author_name" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre nom *</label>
                            <input type="text" id="author_name" name="author_name" required
                                   value="{{ old('author_name', auth()->user()?->name) }}"
                                   class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-700"
                                   style="border-color: #d1d5db; background-color: #ffffff;">
                            @error('author_name') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="author_email" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre email *</label>
                            <input type="email" id="author_email" name="author_email" required
                                   value="{{ old('author_email', auth()->user()?->email) }}"
                                   class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-700"
                                   style="border-color: #d1d5db; background-color: #ffffff;">
                            <p class="text-xs mt-1" style="color: #9ca3af;">Non affiché publiquement</p>
                            @error('author_email') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="review_title" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Titre <span style="color: #9ca3af;">(optionnel)</span></label>
                        <input type="text" id="review_title" name="title"
                               value="{{ old('title') }}"
                               placeholder="Résumez votre expérience"
                               class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-700"
                               style="border-color: #d1d5db; background-color: #ffffff;">
                        @error('title') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="review_body" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre avis *</label>
                        <textarea id="review_body" name="body" required rows="4"
                                  placeholder="Partagez votre expérience avec ce produit..."
                                  class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-700 resize-y"
                                  style="border-color: #d1d5db; background-color: #ffffff;">{{ old('body') }}</textarea>
                        @error('body') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="text-white py-2.5 px-6 rounded-xl font-semibold text-sm transition hover:opacity-90"
                            style="background-color: #276e44;">
                        Publier mon avis
                    </button>
                </form>
            @endif
        </div>

    </div>
</section>

{{-- Produits similaires --}}
@if($related->isNotEmpty())
    <section class="py-16" style="background-color: #c9fad9;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #4d7c5a;">Boutique</p>
            <h2 class="text-2xl font-semibold mb-8" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Vous aimerez aussi
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                @foreach($related as $p)
                    <x-product-card :product="$p"/>
                @endforeach
            </div>
        </div>
    </section>
@endif

<script>
function productForm(basePrice, salePrice, persoPrice) {
    const fonts = @json(collect(config('personalization.fonts'))->map(fn($f, $k) => ['key' => $k, 'label' => $f['label']])->values());
    const colors = @json(collect(config('personalization.colors'))->map(fn($c, $k) => ['key' => $k, 'hex' => $c['hex']])->values());

    return {
        qty: 1,
        addonTotal: 0,
        persoText: '',
        persoFont: fonts.length ? fonts[0].key : '',
        persoColor: colors.length ? colors[0].key : '',
        get persoFontLabel() {
            const f = fonts.find(f => f.key === this.persoFont);
            return f ? f.label : '';
        },
        get persoColorHex() {
            const c = colors.find(c => c.key === this.persoColor);
            return c ? c.hex : '#000000';
        },
        get persoExtra() {
            return (persoPrice !== null && this.persoText.length > 0) ? persoPrice : 0;
        },
        get total() {
            return ((salePrice ?? basePrice) + this.addonTotal + this.persoExtra) * this.qty;
        },
        formatPrice(val) {
            return val.toFixed(2).replace('.', ',') + ' €';
        }
    }
}
</script>
</x-layouts.app>
