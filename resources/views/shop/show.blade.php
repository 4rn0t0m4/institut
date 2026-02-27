<x-layouts.app :title="$product->name" :meta-description="$product->short_description">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Fil d'ariane --}}
    <nav class="text-xs text-gray-400 mb-6 flex items-center gap-2">
        <a href="{{ route('shop.index') }}" class="hover:text-green-700">Boutique</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('shop.index', ['categorie' => $product->category->slug]) }}"
               class="hover:text-green-700">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

        {{-- Galerie --}}
        <div x-data="{ active: 0 }">
            <div class="aspect-square bg-gray-50 rounded-xl overflow-hidden mb-3">
                @if($product->featured_image_id && $product->featuredImage)
                    <img :src="active === 0 ? '{{ $product->featuredImage->url }}' : ''"
                         src="{{ $product->featuredImage->url }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-200">
                        <svg class="w-24 h-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
        </div>

        {{-- Infos produit --}}
        <div x-data="productForm({{ $product->price }}, {{ $product->sale_price ?? 'null' }})">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">
                {{ $product->category?->name }}
            </p>
            <h1 class="text-2xl font-semibold text-gray-900 mb-3">{{ $product->name }}</h1>

            {{-- Prix --}}
            <div class="flex items-baseline gap-3 mb-4">
                <span class="text-2xl font-bold text-green-700" x-text="formatPrice(total)"></span>
                @if($product->sale_price)
                    <span class="text-sm text-gray-400 line-through">
                        {{ number_format($product->price, 2, ',', ' ') }} €
                    </span>
                @endif
            </div>

            {{-- Description courte --}}
            @if($product->short_description)
                <div class="text-sm text-gray-600 mb-6 leading-relaxed">
                    {!! $product->short_description !!}
                </div>
            @endif

            {{-- Stock --}}
            @if($product->stock_status !== 'instock')
                <p class="text-sm text-red-600 mb-4">Produit épuisé</p>
            @endif

            {{-- Formulaire ajout panier --}}
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Addons --}}
                @if($product->addons->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($product->addons as $addon)
                            <x-product-addon :addon="$addon"/>
                        @endforeach
                    </div>
                @endif

                {{-- Quantité + bouton --}}
                <div class="flex items-center gap-4 pt-2">
                    <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                        <button type="button"
                                @click="qty = Math.max(1, qty - 1)"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-50">−</button>
                        <input type="number" name="quantity" x-model="qty"
                               min="1" max="99"
                               class="w-12 text-center py-2 border-0 text-sm focus:outline-none">
                        <button type="button"
                                @click="qty = Math.min(99, qty + 1)"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-50">+</button>
                    </div>
                    <button type="submit"
                            {{ $product->stock_status !== 'instock' ? 'disabled' : '' }}
                            class="flex-1 bg-green-700 text-white py-2.5 px-6 rounded font-medium hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed transition">
                        Ajouter au panier
                    </button>
                </div>
            </form>

            {{-- Description longue --}}
            @if($product->description)
                <details class="mt-8 border-t border-gray-100 pt-6">
                    <summary class="text-sm font-medium text-gray-700 cursor-pointer select-none">
                        Description complète
                    </summary>
                    <div class="mt-4 text-sm text-gray-600 leading-relaxed prose max-w-none">
                        {!! $product->description !!}
                    </div>
                </details>
            @endif
        </div>
    </div>

    {{-- Produits similaires --}}
    @if($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Vous aimerez aussi</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
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
