@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Tableau de bord" :breadcrumbs="['Tableau de bord' => null]" />

    {{-- Metric cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
        {{-- Orders --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Commandes</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ number_format($metrics['orders_count']) }}
                    </h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Revenue --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Chiffre d'affaires</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ number_format($metrics['revenue'], 2, ',', ' ') }} &euro;
                    </h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 text-success-500 dark:bg-success-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Produits</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ number_format($metrics['products_count']) }}
                    </h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-500 dark:bg-orange-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pages --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Pages</span>
                    <h4 class="mt-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ number_format($metrics['pages_count']) }}
                    </h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="mt-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Commandes recentes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">ID</th>
                            <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Client</th>
                            <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Statut</th>
                            <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total</th>
                            <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-brand-500 hover:underline">#{{ $order->id }}</a>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $order->user?->name ?? 'Invite' }}
                                </td>
                                <td class="px-5 py-4">
                                    <x-admin.badge :status="$order->status" />
                                </td>
                                <td class="px-5 py-4 text-sm text-right text-gray-700 dark:text-gray-300">
                                    {{ number_format($order->total, 2, ',', ' ') }} &euro;
                                </td>
                                <td class="px-5 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ $order->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Aucune commande pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
