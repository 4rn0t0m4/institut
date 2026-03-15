<x-layouts.app title="Commande confirmée" :noindex="true">
<div class="max-w-2xl mx-auto px-4 py-16 text-center">

    <div class="mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Commande confirmée !</h1>
        <p class="text-gray-600">
            Merci pour votre commande <strong>{{ $order->number }}</strong>.
            Vous recevrez une confirmation par e-mail à <strong>{{ $order->billing_email }}</strong>.
        </p>
    </div>

    <div class="bg-gray-50 rounded-xl p-6 text-left mb-8">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Détail de la commande</h2>
        <ul class="space-y-2">
            @foreach($order->items as $item)
                <li class="flex justify-between text-sm text-gray-700">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span>{{ number_format($item->total, 2, ',', ' ') }} €</span>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between font-semibold text-gray-900">
            <span>Total</span>
            <span>{{ number_format($order->total, 2, ',', ' ') }} €</span>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        @auth
            <a href="{{ route('account.orders') }}"
               class="inline-block py-2.5 px-6 rounded font-medium transition text-sm text-white"
               style="background-color: #276e44;">
                Voir mes commandes
            </a>
        @else
            <a href="{{ route('register') }}"
               class="inline-block py-2.5 px-6 rounded font-medium transition text-sm text-white"
               style="background-color: #276e44;">
                Créer un compte
            </a>
        @endauth
        <a href="{{ route('shop.index') }}"
           class="inline-block py-2.5 px-6 rounded font-medium transition text-sm border hover:opacity-70"
           style="color: #276e44; border-color: #b0f1b9;">
            Continuer les achats
        </a>
    </div>
</div>

@if(!empty($boxtalPush))
<script>
    (function() {
        fetch('{{ $boxtalPush['url'] }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                payload: @json($boxtalPush['payload']),
                signature: '{{ $boxtalPush['signature'] }}'
            })
        }).catch(function() {});
    })();
</script>
@endif
</x-layouts.app>
