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
</div>
</x-layouts.app>
