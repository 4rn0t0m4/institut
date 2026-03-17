@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Commande {{ $order->number }}" :breadcrumbs="['Commandes' => route('admin.orders.index'), $order->number => null]" />

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Main --}}
        <div class="xl:col-span-2 space-y-6">
            {{-- Order items --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Articles</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Produit</th>
                                <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Qte</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">P.U.</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $item->product_name }}</div>
                                        @if ($item->sku)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->sku }}</div>
                                        @endif
                                        @foreach($item->addons as $addon)
                                            <div class="text-xs mt-1 {{ $addon->addon_type === 'personalization' ? 'text-brand-500 dark:text-brand-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                                                {{ $addon->addon_label }} : {{ $addon->addon_value }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="px-5 py-4 text-sm text-center text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                                    <td class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-300">{{ number_format($item->unit_price, 2, ',', ' ') }} &euro;</td>
                                    <td class="px-5 py-4 text-sm text-right font-medium text-gray-700 dark:text-gray-300">{{ number_format($item->total, 2, ',', ' ') }} &euro;</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-gray-200 dark:border-gray-800">
                            <tr>
                                <td colspan="3" class="px-5 py-3 text-sm text-right text-gray-500 dark:text-gray-400">Sous-total</td>
                                <td class="px-5 py-3 text-sm text-right text-gray-700 dark:text-gray-300">{{ number_format($order->subtotal, 2, ',', ' ') }} &euro;</td>
                            </tr>
                            @if ($order->discount_total > 0)
                                <tr>
                                    <td colspan="3" class="px-5 py-2 text-sm text-right text-gray-500 dark:text-gray-400">Remise</td>
                                    <td class="px-5 py-2 text-sm text-right text-error-500">-{{ number_format($order->discount_total, 2, ',', ' ') }} &euro;</td>
                                </tr>
                            @endif
                            @if ($order->shipping_total > 0)
                                <tr>
                                    <td colspan="3" class="px-5 py-2 text-sm text-right text-gray-500 dark:text-gray-400">Livraison</td>
                                    <td class="px-5 py-2 text-sm text-right text-gray-700 dark:text-gray-300">{{ number_format($order->shipping_total, 2, ',', ' ') }} &euro;</td>
                                </tr>
                            @endif
                            <tr class="border-t border-gray-200 dark:border-gray-800">
                                <td colspan="3" class="px-5 py-3 text-sm text-right font-semibold text-gray-800 dark:text-white/90">Total</td>
                                <td class="px-5 py-3 text-sm text-right font-bold text-gray-800 dark:text-white/90">{{ number_format($order->total, 2, ',', ' ') }} &euro;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Billing / Shipping --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Facturation</h3>
                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</p>
                        <p>{{ $order->billing_address_1 }}</p>
                        @if ($order->billing_address_2) <p>{{ $order->billing_address_2 }}</p> @endif
                        <p>{{ $order->billing_postcode }} {{ $order->billing_city }}</p>
                        <p>{{ $order->billing_country }}</p>
                        @if ($order->billing_phone) <p>Tel: {{ $order->billing_phone }}</p> @endif
                        <p>{{ $order->billing_email }}</p>
                    </div>
                </div>

                @if ($order->shipping_first_name)
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                        <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Livraison</h3>
                        <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                            <p>{{ $order->shipping_address_1 }}</p>
                            @if ($order->shipping_address_2) <p>{{ $order->shipping_address_2 }}</p> @endif
                            <p>{{ $order->shipping_postcode }} {{ $order->shipping_city }}</p>
                            <p>{{ $order->shipping_country }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Statut</h3>
                <x-admin.badge :status="$order->status" />
                <a href="{{ route('admin.orders.edit', $order) }}" class="mt-4 block text-sm text-brand-500 hover:underline">Modifier le statut</a>

                <form method="POST" action="{{ route('admin.orders.resend-emails', $order) }}" class="mt-3">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-brand-500 dark:text-gray-400 flex items-center gap-1.5" onclick="return confirm('Renvoyer les emails de confirmation ?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Renvoyer les emails
                    </button>
                </form>
            </div>

            {{-- Payment --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Paiement</h3>
                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <p><span class="font-medium">Methode :</span> {{ $order->payment_method ?? '—' }}</p>
                    @if ($order->paid_at)
                        <p><span class="font-medium">Paye le :</span> {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                    @endif
                    @if ($order->stripe_payment_intent_id)
                        <p class="break-all"><span class="font-medium">Stripe :</span> {{ $order->stripe_payment_intent_id }}</p>
                    @endif
                </div>
            </div>

            {{-- Tracking --}}
            @if ($order->tracking_number)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Suivi</h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        @if ($order->tracking_carrier) <p><span class="font-medium">Transporteur :</span> {{ $order->tracking_carrier }}</p> @endif
                        <p><span class="font-medium">N° suivi :</span> {{ $order->tracking_number }}</p>
                    </div>
                </div>
            @endif

            {{-- Expédition --}}
            @if (in_array($order->status, ['processing', 'shipped']) && $order->shipping_key !== 'pickup')
                @if ($order->shipping_key === 'colissimo')
                    {{-- Colissimo : saisie manuelle du suivi --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                        <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Expédition Colissimo</h3>

                        @if ($order->tracking_number)
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <p><span class="font-medium">N° suivi :</span> {{ $order->tracking_number }}</p>
                                <a href="https://www.laposte.fr/outils/suivre-vos-envois?code={{ $order->tracking_number }}" target="_blank" class="inline-flex items-center gap-1 text-brand-500 hover:underline text-sm">
                                    Suivre sur La Poste
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="shipped">
                                <input type="hidden" name="tracking_carrier" value="Colissimo">
                                <div class="mb-3">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Numéro de suivi Colissimo</label>
                                    <input type="text" name="tracking_number" required placeholder="Ex : 6A12345678901"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-2 px-3">
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Marquer expédiée
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    {{-- Boxtal (Mondial Relay, Chronopost, etc.) --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                        <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Boxtal</h3>

                        @if ($order->boxtal_shipping_order_id)
                            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                                <p><span class="font-medium">ID expédition :</span> {{ $order->boxtal_shipping_order_id }}</p>

                                <a href="{{ route('admin.orders.label', $order) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Voir / imprimer l'étiquette
                                </a>

                                <form method="POST" action="{{ route('admin.orders.reset-shipment', $order) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-error-500 hover:underline" onclick="return confirm('Dissocier cette expédition Boxtal ? (ne l\'annule pas sur Boxtal)')">
                                        Dissocier l'expédition
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.orders.create-shipment', $order) }}">
                                @csrf
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    @if ($order->relay_network === 'MONR_NETWORK')
                                        Mondial Relay
                                    @elseif ($order->relay_network === 'CHRP_NETWORK')
                                        Chronopost
                                    @else
                                        {{ $order->shipping_method ?? 'Expédition' }}
                                    @endif
                                    @if ($order->relay_point_code)
                                        <br><span class="text-xs">Relais : {{ $order->relay_point_code }}</span>
                                    @endif
                                </p>

                                @php $pkg = config('shipping.boxtal.default_package'); @endphp
                                <div class="space-y-2 mb-3">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400">Poids (kg)</label>
                                            <input type="number" name="weight" step="0.01" value="{{ $pkg['weight'] }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-1.5 px-2" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400">Longueur (cm)</label>
                                            <input type="number" name="length" value="{{ $pkg['length'] }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-1.5 px-2" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400">Largeur (cm)</label>
                                            <input type="number" name="width" value="{{ $pkg['width'] }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-1.5 px-2" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400">Hauteur (cm)</label>
                                            <input type="number" name="height" value="{{ $pkg['height'] }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm py-1.5 px-2" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors" onclick="return confirm('Créer l\'expédition Boxtal pour cette commande ?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    Créer l'expédition Boxtal
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @endif

            {{-- Gift wrap --}}
            @if ($order->gift_wrap)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Boîte / sac cadeau</h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <p><span class="font-medium">Type :</span> {{ $order->gift_type === 'boite' ? 'Boîte cadeau' : 'Sac cadeau' }}</p>
                        @if ($order->gift_message)
                            <p><span class="font-medium">Message :</span></p>
                            <p class="whitespace-pre-line">{{ $order->gift_message }}</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Customer note --}}
            @if ($order->customer_note)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Note client</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->customer_note }}</p>
                </div>
            @endif

            {{-- Date --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Date</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

@endsection
