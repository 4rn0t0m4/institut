@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Export des ventes" :breadcrumbs="['Export des ventes' => null]" />

    {{-- Filtre par mois --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.exports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mois</label>
                <input type="month" name="month" id="month" value="{{ $month }}"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-brand-500 focus:border-brand-500">
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Afficher
            </button>
            <a href="{{ route('admin.exports.csv', ['month' => $month]) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter CSV
            </a>
        </form>
    </div>

    {{-- Résumé --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-sm text-gray-500 dark:text-gray-400">Articles vendus</span>
            <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totals['quantity'] }}</h4>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-sm text-gray-500 dark:text-gray-400">Total TTC</span>
            <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totals['total_ttc'], 2, ',', ' ') }} €</h4>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-sm text-gray-500 dark:text-gray-400">Total HT</span>
            <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totals['total_ht'], 2, ',', ' ') }} €</h4>
        </div>
    </div>

    {{-- Tableau des produits --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Produit</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">SKU</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Quantité</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Prix unit. TTC</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total TTC</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total HT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $product->sku ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">{{ $product->total_quantity }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">{{ number_format($product->unit_price, 2, ',', ' ') }} €</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">{{ number_format($product->total_ttc, 2, ',', ' ') }} €</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">{{ number_format($product->total_ht, 2, ',', ' ') }} €</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucune vente pour ce mois.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($products->isNotEmpty())
                    <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="font-semibold">
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white" colspan="2">Total</td>
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white text-right">{{ $totals['quantity'] }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500"></td>
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white text-right">{{ number_format($totals['total_ttc'], 2, ',', ' ') }} €</td>
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white text-right">{{ number_format($totals['total_ht'], 2, ',', ' ') }} €</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
