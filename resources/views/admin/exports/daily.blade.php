@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="CA journalier" :breadcrumbs="['Export des ventes' => route('admin.exports.index'), 'CA journalier' => null]" />

    {{-- Onglets --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('admin.exports.index', ['month' => $month]) }}"
           class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-800 transition">
            Par produit
        </a>
        <a href="{{ route('admin.exports.daily', ['month' => $month]) }}"
           class="px-4 py-2 text-sm font-medium rounded-lg bg-brand-500 text-white">
            CA journalier
        </a>
    </div>

    {{-- Filtre par mois --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.exports.daily') }}" class="flex flex-wrap items-end gap-4">
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
            <a href="{{ route('admin.exports.daily.excel', ['month' => $month]) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter Excel
            </a>
        </form>
    </div>

    {{-- Résumé --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-sm text-gray-500 dark:text-gray-400">Commandes</span>
            <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totals['orders'] }}</h4>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-sm text-gray-500 dark:text-gray-400">CA TTC</span>
            <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totals['total_ttc'], 2, ',', ' ') }} €</h4>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-sm text-gray-500 dark:text-gray-400">CA HT</span>
            <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totals['total_ht'], 2, ',', ' ') }} €</h4>
        </div>
    </div>

    {{-- Tableau journalier --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Commandes</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">CA TTC</th>
                        <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">CA HT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($days as $day)
                        @php $date = \Carbon\Carbon::parse($day->date); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $date->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">{{ $day->orders_count }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">{{ number_format($day->total_ttc, 2, ',', ' ') }} €</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 text-right">{{ number_format($day->total_ht, 2, ',', ' ') }} €</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucune vente pour ce mois.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($days->isNotEmpty())
                    <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                        <tr class="font-semibold">
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white">Total</td>
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white text-right">{{ $totals['orders'] }}</td>
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white text-right">{{ number_format($totals['total_ttc'], 2, ',', ' ') }} €</td>
                            <td class="px-5 py-4 text-sm text-gray-900 dark:text-white text-right">{{ number_format($totals['total_ht'], 2, ',', ' ') }} €</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
