<x-layouts.app title="Commande annulée">
<div class="max-w-xl mx-auto px-4 py-16 text-center">

    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Paiement annulé</h1>
        <p class="text-gray-600 mb-8">
            Votre commande n'a pas été finalisée. Votre panier a été conservé.
        </p>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('cart.index') }}"
           class="inline-block bg-green-700 text-white py-2.5 px-6 rounded font-medium hover:bg-green-800 transition text-sm">
            Retour au panier
        </a>
        <a href="{{ route('shop.index') }}"
           class="inline-block border border-gray-300 text-gray-700 py-2.5 px-6 rounded font-medium hover:bg-gray-50 transition text-sm">
            Continuer les achats
        </a>
    </div>
</div>
</x-layouts.app>
