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
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $product->category->parent->name, 'item' => route('shop.index', ['categorie' => $product->category->parent->slug])];
}
if ($product->category) {
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $product->category->name, 'item' => route('shop.index', ['categorie' => $product->category->slug])];
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
                <a href="{{ route('shop.index', ['categorie' => $product->category->parent->slug]) }}"
                   class="hover:underline">{{ $product->category->parent->name }}</a>
            @endif
            <span>/</span>
            <a href="{{ route('shop.index', ['categorie' => $product->category->slug]) }}"
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
            <h1 class="text-3xl md:text-4xl font-semibold leading-tight mb-5" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                {{ $product->name }}
            </h1>

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

    {{-- Description complète --}}
    @if($product->description)
        <div class="mt-16 pt-10" style="border-top: 2px solid #b0f1b9;">
            <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #60916a;">Tout savoir</p>
            <h2 class="text-2xl font-semibold mb-6" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Description
            </h2>
            <div class="product-description max-w-3xl">
                {!! $product->description !!}
            </div>
        </div>
    @endif

    {{-- Produits similaires --}}
    @if($related->isNotEmpty())
        <section class="mt-16 pt-10" style="border-top: 2px solid #b0f1b9;">
            <p class="text-xs uppercase tracking-widest font-medium mb-2" style="color: #60916a;">Boutique</p>
            <h2 class="text-2xl font-semibold mb-8" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Vous aimerez aussi
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                @foreach($related as $p)
                    <x-product-card :product="$p"/>
                @endforeach
            </div>
        </section>
    @endif
</div>

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
