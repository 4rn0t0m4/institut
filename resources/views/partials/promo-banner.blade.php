@php
    $promoGift = config('promotions.gift');
    $promoActive = $promoGift['enabled'] && now()->between($promoGift['starts_at'], $promoGift['ends_at']);
@endphp

@if($promoActive)
<div class="bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-200 rounded-xl p-5 sm:p-6">
    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
        <div class="text-4xl shrink-0">🎁</div>
        <div class="text-center sm:text-left flex-1">
            <p class="text-base sm:text-lg font-bold text-pink-900">Offre Fete des meres</p>
            <p class="text-sm text-pink-800 mt-1">
                Une trousse de maquillage personnalisable <strong>offerte</strong> des {{ number_format($promoGift['min_cart_value'], 0) }} € d'achat !
                <span class="text-pink-500 text-xs font-medium ml-1">Jusqu'au 31 mai</span>
            </p>
        </div>
        <a href="{{ route('shop.index') }}"
           class="shrink-0 inline-flex items-center gap-2 bg-pink-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-pink-700 transition">
            En profiter
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</div>
@endif
