@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Webhooks Boxtal" :breadcrumbs="['Livraison' => route('admin.shipping.index'), 'Webhooks Boxtal' => null]" />

    <div class="max-w-2xl space-y-6">

        {{-- Messages flash --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Résultats de création --}}
        @if(session('results'))
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <h4 class="mb-2 font-semibold text-blue-900 dark:text-blue-200">Résultat des souscriptions</h4>
                @foreach(session('results') as $result)
                    <div class="mb-2 text-sm">
                        <span class="font-medium">{{ $result['eventType'] }}</span> :
                        @if($result['success'])
                            <span class="text-green-700 dark:text-green-400">Créée</span>
                            @if($result['webhookSecret'] ?? null)
                                <div class="mt-1 rounded bg-yellow-100 p-2 text-xs text-yellow-900 dark:bg-yellow-900/30 dark:text-yellow-200">
                                    <strong>Webhook secret :</strong>
                                    <code class="break-all">{{ $result['webhookSecret'] }}</code>
                                    <br>Ajoute <code>BOXTAL_V3_WEBHOOK_SECRET={{ $result['webhookSecret'] }}</code> dans le <code>.env</code>
                                </div>
                            @endif
                        @else
                            <span class="text-red-700 dark:text-red-400">Erreur : {{ $result['error'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Résultat du test --}}
        @if(session('test_result'))
            @php $test = session('test_result'); @endphp
            <div class="rounded-lg border p-4 text-sm {{ $test['status'] === 200 ? 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300' : 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300' }}">
                <strong>Test webhook :</strong> HTTP {{ $test['status'] }}
                @if(is_array($test['body']))
                    — {{ json_encode($test['body']) }}
                @else
                    — {{ $test['body'] }}
                @endif
                <p class="mt-1 text-xs opacity-75">Vérifie les logs (storage/logs/laravel.log) pour plus de détails.</p>
            </div>
        @endif

        {{-- Erreur de chargement --}}
        @if($error)
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                {{ $error }}
            </div>
        @endif

        {{-- Souscriptions existantes --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Souscriptions actives</h3>

            @if(count($subscriptions) > 0)
                <div class="space-y-3">
                    @foreach($subscriptions as $sub)
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 p-3 dark:border-gray-700">
                            <div>
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ ($sub['eventType'] ?? '') === 'TRACKING_UPDATE' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $sub['eventType'] ?? '-' }}
                                </span>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 break-all">{{ $sub['callbackUrl'] ?? '-' }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.boxtal-subscriptions.destroy', $sub['id'] ?? 0) }}" onsubmit="return confirm('Supprimer cette souscription ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400">Supprimer</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune souscription.</p>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <form method="POST" action="{{ route('admin.boxtal-subscriptions.test') }}" class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                    @csrf
                    <div>
                        <label for="order_number" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">N° de commande</label>
                        <input type="text" id="order_number" name="order_number" required placeholder="CMD-..."
                            class="h-10 w-48 rounded-lg border border-gray-200 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-white/3 dark:text-white/90" />
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Tester (expédier + email)
                    </button>
                </form>
                <p class="mt-1 text-xs text-gray-400">Simule l'expédition : passe la commande en "shipped" et envoie l'email au client.</p>
            </div>
        </div>

        {{-- Créer les souscriptions --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Créer les souscriptions</h3>
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                Crée les souscriptions pour recevoir les notifications de suivi (TRACKING_UPDATE) et les documents d'expédition (SHIPPING_DOCUMENT).
            </p>

            <form method="POST" action="{{ route('admin.boxtal-subscriptions.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="callback_url" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">URL de callback</label>
                    <input type="url" id="callback_url" name="callback_url"
                        value="{{ old('callback_url', url('/api/boxtal/webhook')) }}"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                </div>

                <div class="mb-4">
                    <label for="event_types" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Types d'événements (séparés par des virgules)</label>
                    <input type="text" id="event_types" name="event_types"
                        placeholder="Laisse vide pour tester toutes les variantes"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    <p class="mt-1 text-xs text-gray-400">Ex: TRACKING_UPDATED, DOCUMENT_CREATED</p>
                </div>

                <div class="mb-4">
                    <label for="webhook_secret" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Webhook secret (optionnel, auto-généré si vide)</label>
                    <input type="text" id="webhook_secret" name="webhook_secret"
                        placeholder="Laisse vide pour en générer un automatiquement"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                </div>

                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    Créer les souscriptions
                </button>
            </form>
        </div>
    </div>
@endsection
