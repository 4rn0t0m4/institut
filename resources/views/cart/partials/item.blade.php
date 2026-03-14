@if($item)
<div id="cart-item-{{ $item['key'] }}"
     class="flex items-start gap-4 bg-white border border-gray-100 rounded-lg p-4">

    {{-- Image produit --}}
    <a href="{{ $item['url'] }}" class="w-16 h-16 rounded flex-shrink-0 overflow-hidden bg-gray-100">
        @if(!empty($item['image']))
            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                </svg>
            </div>
        @endif
    </a>

    {{-- Détails --}}
    <div class="flex-1 min-w-0">
        <a href="{{ $item['url'] }}" class="text-sm font-medium text-gray-800 truncate block hover:underline">{{ $item['name'] }}</a>
        @if(!empty($item['addons']))
            <ul class="mt-1 space-y-0.5">
                @foreach($item['addons'] as $addon)
                    @if(!empty($addon['value']))
                        <li class="text-xs text-gray-400">{{ $addon['label'] ?? '' }} : {{ $addon['value'] }}</li>
                    @endif
                @endforeach
            </ul>
        @endif
        @if(!empty($item['personalization']['text']))
            <div class="mt-1 flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-full border border-gray-200 flex-shrink-0" style="background-color: {{ $item['personalization']['color_hex'] ?? '#000' }};"></span>
                <span class="text-xs text-gray-400 truncate">
                    « {{ $item['personalization']['text'] }} » — {{ $item['personalization']['font_label'] ?? '' }}, {{ $item['personalization']['color_label'] ?? '' }}
                </span>
            </div>
        @endif
        <p class="text-sm text-gray-500 mt-1">
            {{ number_format($item['price'] + $item['addon_price'], 2, ',', ' ') }} € / unité
        </p>
    </div>

    {{-- Quantité --}}
    <form action="{{ route('cart.update', $item['key']) }}" method="POST"
          class="flex items-center gap-1" x-data="{ qty: {{ $item['quantity'] }} }" data-turbo="false">
        @csrf @method('PATCH')
        <button type="button" @click="qty = Math.max(0, qty - 1); $nextTick(() => $el.closest('form').submit())"
                class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 text-sm">−</button>
        <input type="number" name="quantity" x-model="qty" min="0" max="99"
               class="w-10 text-center text-sm border border-gray-200 rounded py-0.5 focus:outline-none focus:ring-1 focus:ring-green-600"
               @change="$nextTick(() => $el.closest('form').submit())">
        <button type="button" @click="qty = Math.min(99, qty + 1); $nextTick(() => $el.closest('form').submit())"
                class="w-7 h-7 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-50 text-sm">+</button>
    </form>

    {{-- Prix ligne --}}
    <div class="text-right flex-shrink-0">
        <p class="text-sm font-semibold text-gray-900">
            {{ number_format(($item['price'] + $item['addon_price']) * $item['quantity'], 2, ',', ' ') }} €
        </p>
        <form action="{{ route('cart.remove', $item['key']) }}" method="POST" class="mt-1" data-turbo="false">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition">
                Supprimer
            </button>
        </form>
    </div>

</div>
@endif
