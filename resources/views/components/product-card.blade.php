@props(['product'])

<article class="group flex flex-col bg-white border rounded-xl overflow-hidden hover:shadow-md transition-shadow"
         style="border-color: #b0f1b9;">
    {{-- Image --}}
    <a href="{{ route('shop.show', $product->slug) }}"
       class="block aspect-square overflow-hidden" style="background-color: #f0fdf4;">
        @if($product->featured_image_id)
            <img src="{{ $product->featuredImage->url ?? '' }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                 loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center" style="color: #b0f1b9;">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
    </a>

    {{-- Infos --}}
    <div class="p-3 flex flex-col flex-1">
        @if($product->category)
            <p class="text-xs mb-1" style="color: #60916a;">{{ $product->category->name }}</p>
        @endif
        <a href="{{ route('shop.show', $product->slug) }}"
           class="text-sm font-medium leading-snug flex-1 hover:opacity-70 transition-opacity"
           style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            {{ $product->name }}
        </a>
        <div class="flex items-center justify-between mt-3">
            <div>
                @if($product->sale_price)
                    <span class="text-xs line-through mr-1" style="color: #60916a;">{{ number_format($product->price, 2, ',', ' ') }} €</span>
                    <span class="text-sm font-semibold" style="color: #276e44;">{{ number_format($product->sale_price, 2, ',', ' ') }} €</span>
                @else
                    <span class="text-sm font-semibold" style="color: #276e44;">{{ number_format($product->price, 2, ',', ' ') }} €</span>
                @endif
            </div>
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit"
                        class="text-xs px-2.5 py-1 rounded-lg text-white transition hover:opacity-90"
                        style="background-color: #276e44;"
                        {{ $product->stock_status !== 'instock' ? 'disabled' : '' }}>
                    {{ $product->stock_status === 'instock' ? 'Ajouter' : 'Épuisé' }}
                </button>
            </form>
        </div>
    </div>
</article>
