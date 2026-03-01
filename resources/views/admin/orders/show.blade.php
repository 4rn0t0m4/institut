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
