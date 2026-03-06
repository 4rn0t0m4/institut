<x-layouts.app :title="$product->meta_title ?: $product->name" :meta-description="$product->meta_description ?: $product->short_description" og-type="product">

{{-- Schema.org Product + BreadcrumbList --}}
@php
$productJsonLd = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->name,
    'description' => strip_tags($product->short_description ?? $product->description ?? ''),
    'image' => $product->featuredImage?->url ? [url($product->featuredImage->url)] : [],
    'sku' => (string) $product->id,
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
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

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
    <div style="background-color: #f59e0b; color: white; font-size: 14px; font-weight: 600; text-align: center; padding: 10px 0;">
        Ce produit est masque — visible uniquement par les administrateurs
        <a href="{{ route('admin.products.edit', $product) }}" style="color: white; text-decoration: underline; margin-left: 8px;">Modifier</a>
    </div>
@endif

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Fil d'ariane --}}
    <nav class="text-xs mb-8 flex items-center gap-2" style="color: #60916a;">
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
        <div x-data="{ active: 0 }">
            {{-- Image principale --}}
            <div class="aspect-square rounded-3xl overflow-hidden mb-3" style="background-color: #f0fdf4;">
                @if($allImages->isNotEmpty())
                    @foreach($allImages as $i => $img)
                        <img src="{{ $img->url }}"
                             alt="{{ $img->alt ?: $product->name }}"
                             x-show="active === {{ $i }}"
                             class="w-full h-full object-cover">
                    @endforeach
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
                                 class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Infos produit --}}
        <div x-data="productForm({{ $product->price }}, {{ $product->sale_price ?? 'null' }})">
            @if($product->brand)
                <p class="text-xs uppercase tracking-widest mb-1 font-semibold">
                    <a href="{{ route('shop.index', ['marque' => $product->brand->slug]) }}" style="color: #276e44;" class="hover:underline">
                        {{ $product->brand->name }}
                    </a>
                </p>
            @endif
            <p class="text-xs uppercase tracking-widest mb-2 font-medium" style="color: #60916a;">
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
                    <a href="#avis" class="text-xs hover:underline" style="color: #60916a;">{{ $reviews->count() }} avis</a>
                @else
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4" fill="#e5e7eb" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <a href="#avis" class="text-xs hover:underline" style="color: #60916a;">Donner mon avis</a>
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
                    <span class="text-base line-through" style="color: #60916a;">
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
                                   class="flex-1 text-sm px-3 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-600"
                                   style="border-color: #d1d5db;">
                            <button type="submit"
                                    class="text-white text-sm font-semibold px-4 py-2 rounded-lg transition"
                                    style="background-color: #276e44;"
                                    onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
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

                {{-- Quantité + bouton --}}
                <div class="flex items-center gap-4 pt-2">
                    <div class="flex items-center rounded-xl overflow-hidden" style="border: 1px solid #b0f1b9;">
                        <button type="button"
                                @click="qty = Math.max(1, qty - 1)"
                                class="px-3.5 py-2.5 transition" style="color: #276e44;" onmouseover="this.style.backgroundColor='#f0fdf4'" onmouseout="this.style.backgroundColor='transparent'">−</button>
                        <input type="number" name="quantity" x-model="qty"
                               min="1" max="99"
                               class="w-12 text-center py-2.5 border-0 text-sm focus:outline-none" style="color: #276e44;">
                        <button type="button"
                                @click="qty = Math.min(99, qty + 1)"
                                class="px-3.5 py-2.5 transition" style="color: #276e44;" onmouseover="this.style.backgroundColor='#f0fdf4'" onmouseout="this.style.backgroundColor='transparent'">+</button>
                    </div>
                    <button type="submit"
                            {{ $product->stock_status !== 'instock' ? 'disabled' : '' }}
                            class="flex-1 text-white py-3 px-6 rounded-xl font-semibold text-sm transition disabled:cursor-not-allowed"
                            style="background-color: {{ $product->stock_status !== 'instock' ? '#9ca3af' : '#276e44' }};"
                            {{ $product->stock_status === 'instock' ? 'onmouseover=this.style.opacity=0.9 onmouseout=this.style.opacity=1' : '' }}>
                        {{ $product->stock_status === 'instock' ? 'Ajouter au panier' : 'Produit épuisé' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Description complète --}}
@if($product->description)
    <section class="py-16" style="background-color: #c9fad9;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #60916a;">Tout savoir</p>
            <h2 class="text-2xl font-semibold mb-6" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Description
            </h2>
            <div class="product-description max-w-3xl">
                {!! $product->description !!}
            </div>
        </div>
    </section>
@endif

{{-- Avis clients --}}
<section id="avis" class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #60916a;">Retours d'experience</p>
        <h2 class="text-2xl font-semibold mb-8" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Avis clients
            @if($reviews->isNotEmpty())
                <span class="text-base font-normal" style="color: #60916a;">({{ $reviews->count() }})</span>
            @endif
        </h2>

        {{-- Résumé note moyenne --}}
        @if($reviews->isNotEmpty())
            @php $avgRating = round($reviews->avg('rating'), 1); @endphp
            <div class="flex items-center gap-3 mb-8">
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5" fill="{{ $i <= round($avgRating) ? '#f59e0b' : '#e5e7eb' }}" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-lg font-semibold" style="color: #276e44;">{{ number_format($avgRating, 1, ',', '') }}</span>
                <span class="text-sm" style="color: #6b7280;">sur 5</span>
            </div>
        @endif

        {{-- Liste des avis --}}
        @if($reviews->isNotEmpty())
            <div class="space-y-6 mb-10">
                @foreach($reviews as $review)
                    <div class="rounded-xl p-5" style="background-color: #f0fdf4; border: 1px solid #b0f1b9;">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-sm" style="color: #276e44;">{{ $review->author_name }}</span>
                                    @if($review->is_verified_buyer)
                                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium" style="background-color: #ecfdf5; color: #276e44; border: 1px solid #b0f1b9;">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Achat verifie
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4" fill="{{ $i <= $review->rating ? '#f59e0b' : '#d1d5db' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs shrink-0" style="color: #9ca3af;">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <h4 class="font-semibold text-sm mb-1" style="color: #374151;">{{ $review->title }}</h4>
                        <p class="text-sm leading-relaxed" style="color: #6b7280;">{{ $review->body }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm mb-8" style="color: #6b7280;">Aucun avis pour le moment. Soyez le premier a donner votre avis !</p>
        @endif

        {{-- Formulaire d'avis --}}
        <div class="max-w-xl rounded-2xl p-6" style="background-color: #f0fdf4; border: 1px solid #b0f1b9;" x-data="{ rating: 0 }">
            <h3 class="text-lg font-semibold mb-5" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Laisser un avis
            </h3>

            @if(session('review_success'))
                <div class="rounded-xl p-4" style="background-color: #ecfdf5; border: 1px solid #b0f1b9; color: #276e44;">
                    <p class="text-sm font-medium">{{ session('review_success') }}</p>
                </div>
            @else
                <form action="{{ route('shop.review.store', $product) }}" method="POST" class="space-y-4" data-turbo="false">
                    @csrf

                    {{-- Note --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre note *</label>
                        <div class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}"
                                        class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-8 h-8 transition-colors" :fill="rating >= {{ $i }} ? '#f59e0b' : '#d1d5db'" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                        @error('rating') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nom --}}
                        <div>
                            <label for="author_name" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre nom *</label>
                            <input type="text" id="author_name" name="author_name" required
                                   value="{{ old('author_name', auth()->user()?->name) }}"
                                   class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-600"
                                   style="border-color: #b0f1b9; background-color: #ffffff;">
                            @error('author_name') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="author_email" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre email *</label>
                            <input type="email" id="author_email" name="author_email" required
                                   value="{{ old('author_email', auth()->user()?->email) }}"
                                   class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-600"
                                   style="border-color: #b0f1b9; background-color: #ffffff;">
                            <p class="text-xs mt-1" style="color: #9ca3af;">Ne sera pas affiche publiquement</p>
                            @error('author_email') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Titre --}}
                    <div>
                        <label for="review_title" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Titre de l'avis *</label>
                        <input type="text" id="review_title" name="title" required
                               value="{{ old('title') }}"
                               placeholder="Resumez votre experience"
                               class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-600"
                               style="border-color: #b0f1b9; background-color: #ffffff;">
                        @error('title') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                    </div>

                    {{-- Corps --}}
                    <div>
                        <label for="review_body" class="mb-1.5 block text-sm font-medium" style="color: #374151;">Votre avis *</label>
                        <textarea id="review_body" name="body" required rows="4"
                                  placeholder="Partagez votre experience avec ce produit..."
                                  class="w-full text-sm px-3 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-green-600 resize-y"
                                  style="border-color: #b0f1b9; background-color: #ffffff;">{{ old('body') }}</textarea>
                        @error('body') <p class="text-xs mt-1" style="color: #dc2626;">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="text-white py-3 px-6 rounded-xl font-semibold text-sm transition"
                            style="background-color: #276e44;"
                            onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
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
            <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #60916a;">Boutique</p>
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
function productForm(basePrice, salePrice) {
    return {
        qty: 1,
        addonTotal: 0,
        get total() {
            return ((salePrice ?? basePrice) + this.addonTotal) * this.qty;
        },
        formatPrice(val) {
            return val.toFixed(2).replace('.', ',') + ' €';
        }
    }
}
</script>
</x-layouts.app>
