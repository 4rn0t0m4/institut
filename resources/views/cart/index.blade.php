<x-layouts.app title="Mon panier">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Mon panier</h1>

    <div id="cart-flash"></div>

    @if(session('warnings'))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <ul class="text-sm text-amber-800">
                    @foreach(session('warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(empty($items))
        <div class="text-center py-16 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-lg mb-4">Votre panier est vide</p>
            <a href="{{ route('shop.index') }}"
               class="inline-block bg-green-700 text-white px-6 py-2.5 rounded font-medium hover:bg-green-800 transition">
                Continuer mes achats
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Articles --}}
            <div class="lg:col-span-2 space-y-3">
                @foreach($items as $key => $item)
                    @include('cart.partials.item', ['item' => $item])
                @endforeach

                {{-- Cadeau offert --}}
                @php
                    $gift = config('promotions.gift');
                    $giftPromoActive = $gift['enabled'] && now()->between($gift['starts_at'], $gift['ends_at']);
                    $giftEligible = $giftPromoActive && $subtotal >= $gift['min_cart_value'];
                    $giftChoice = session('promo_gift');
                    if (!$giftEligible && $giftChoice) {
                        session()->forget('promo_gift');
                        $giftChoice = null;
                    }
                @endphp

                @if($giftEligible)
                    <div class="rounded-xl border border-green-200 bg-green-50/50 p-4">
                        @if($giftChoice)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $gift['options'][$giftChoice] }}</p>
                                        <p class="text-xs text-green-600">Cadeau offert - Fete des meres</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-green-700">0,00 €</span>
                                    <form action="{{ route('cart.gift.remove') }}" method="POST" data-turbo="false">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition">Changer</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <p class="text-sm font-semibold text-green-800 mb-2">Choisissez votre cadeau offert !</p>
                            <p class="text-xs text-green-700 mb-3">Fete des meres : une trousse de maquillage personnalisable offerte.</p>
                            <form action="{{ route('cart.gift') }}" method="POST" data-turbo="false" class="flex gap-2">
                                @csrf
                                @foreach($gift['options'] as $optKey => $label)
                                    <button type="submit" name="gift_option" value="{{ $optKey }}"
                                            class="flex-1 text-sm font-medium py-2 px-3 rounded-lg border border-green-300 bg-white text-green-800 hover:bg-green-100 hover:border-green-500 transition text-center">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </form>
                        @endif
                    </div>
                @elseif($giftPromoActive && $subtotal > 0)
                    @php $remaining = $gift['min_cart_value'] - $subtotal; @endphp
                    <div class="rounded-lg bg-gray-50 border border-gray-200 px-4 py-3">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Fete des meres :</span> plus que <strong class="text-green-700">{{ number_format($remaining, 2, ',', ' ') }} €</strong> pour recevoir une trousse offerte !
                        </p>
                    </div>
                @endif
            </div>

            {{-- Récap commande --}}
            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-xl p-6 space-y-4 sticky top-24">
                    <h2 class="font-semibold text-gray-900">Récapitulatif</h2>

                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Sous-total</span>
                        <span id="cart-subtotal">{{ number_format($subtotal, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-400">
                        <span>Livraison</span>
                        <span>Calculée à l'étape suivante</span>
                    </div>
                    <div class="border-t border-gray-200 pt-4 flex justify-between font-semibold text-gray-900">
                        <span>Total</span>
                        <span>{{ number_format($subtotal, 2, ',', ' ') }} €</span>
                    </div>

                    <a href="{{ route('checkout.index') }}"
                       class="block w-full text-center bg-green-700 text-white py-3 rounded font-medium hover:bg-green-800 transition">
                        Commander
                    </a>
                    <a href="{{ route('shop.index') }}"
                       class="block w-full text-center text-sm text-gray-500 hover:text-green-700 transition">
                        Continuer mes achats
                    </a>
                </div>
            </div>

        </div>
    @endif

    {{-- Suggestions --}}
    @if($suggestions->isNotEmpty())
    <div class="mt-16">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Vous aimerez peut-être</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($suggestions as $product)
            <a href="{{ $product->url() }}" class="group block bg-white rounded-xl border border-gray-100 hover:border-green-200 hover:shadow-sm transition overflow-hidden">
                <div class="aspect-square bg-gray-50 overflow-hidden">
                    @if($product->featuredImage)
                        <img src="{{ $product->featuredImage->url }}"
                             alt="{{ $product->featuredImage->alt ?: $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <p class="text-sm font-medium text-gray-800 line-clamp-2 group-hover:text-green-700 transition">{{ $product->name }}</p>
                    <p class="mt-1 text-sm font-semibold text-green-700">{{ number_format($product->currentPrice(), 2, ',', ' ') }} €</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
</x-layouts.app>
