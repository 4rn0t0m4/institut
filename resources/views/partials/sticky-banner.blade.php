@php
    $banner = \App\Models\Setting::get('sticky_banner');
    $promoGift = config('promotions.gift');
    $promoActive = $promoGift['enabled'] && now()->between($promoGift['starts_at'], $promoGift['ends_at']);
@endphp
@if($promoActive && !request()->cookie('banner_dismissed'))
    <div x-data="{ show: true }"
         x-show="show"
         class="relative text-white text-sm py-2.5 px-4" style="background: linear-gradient(135deg, #be185d, #9f1239);">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-4 flex-wrap">
            <span>🎁 <strong>Fete des meres :</strong> une trousse personnalisable offerte des {{ number_format($promoGift['min_cart_value'], 0) }} € d'achat !</span>
            <a href="{{ route('shop.index') }}"
               class="shrink-0 rounded-full px-4 py-1 text-xs font-semibold transition hover:brightness-125"
               style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);">
                En profiter →
            </a>
        </div>
        <button @click="show=false; document.cookie='banner_dismissed=1;path=/;max-age=86400;SameSite=Lax'"
                class="absolute right-4 top-1/2 -translate-y-1/2 opacity-60 hover:opacity-100 transition text-xl leading-none"
                aria-label="Fermer">&times;</button>
    </div>
@elseif($banner)
    @php $b = is_string($banner) ? json_decode($banner, true) : $banner; @endphp
    @if(!empty($b['text']) && !empty($b['active']) && !request()->cookie('banner_dismissed'))
        <div x-data="{ show: true }"
             x-show="show"
             class="relative text-white text-sm py-2.5 px-4" style="background-color: #276e44;">
            <div class="max-w-7xl mx-auto flex items-center justify-center gap-4 flex-wrap">
                <span>{{ $b['text'] }}</span>
                @if(!empty($b['link']) && !empty($b['link_label']))
                    <a href="{{ $b['link'] }}"
                       class="shrink-0 rounded-full px-4 py-1 text-xs font-semibold transition hover:brightness-125"
                       style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);">
                        {{ $b['link_label'] }} →
                    </a>
                @endif
            </div>
            <button @click="show=false; document.cookie='banner_dismissed=1;path=/;max-age=86400;SameSite=Lax'"
                    class="absolute right-4 top-1/2 -translate-y-1/2 opacity-60 hover:opacity-100 transition text-xl leading-none"
                    aria-label="Fermer">&times;</button>
        </div>
    @endif
@endif
