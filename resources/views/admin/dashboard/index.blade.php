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
                            <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-brand-500 hover:underline">{{ $order->number }}</a>
                                </td>
                                <td class="px-5 py-4">
                                    @if($order->user_id)
                                        <a href="{{ route('admin.customers.show', $order->user_id) }}" class="text-sm font-medium text-gray-700 hover:underline dark:text-gray-300">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</a>
                                    @else
                                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->billing_email }}</div>
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
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-500 hover:text-brand-500 dark:text-gray-400" title="Voir">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order) }}" class="text-gray-500 hover:text-brand-500 dark:text-gray-400" title="Modifier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Aucune commande pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Google Analytics --}}
    @if ($analyticsConfigured && $analyticsData)
        {{-- Analytics metric cards --}}
        @php
            $today = $analyticsData['visitors_today'];
            $week = $analyticsData['visitors_7days'];
            $month = $analyticsData['visitors_30days'];
            $todayVisitors = $today->sum('activeUsers');
            $todayPageviews = $today->sum('screenPageViews');
            $weekVisitors = $week->sum('activeUsers');
            $weekPageviews = $week->sum('screenPageViews');
            $monthVisitors = $month->sum('activeUsers');
            $monthPageviews = $month->sum('screenPageViews');
        @endphp
        <div class="mt-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Statistiques de visite</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6">
                {{-- Today --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Aujourd'hui</span>
                    <div class="mt-2 flex items-baseline gap-3">
                        <span class="text-2xl font-bold" style="color: #1f2937;">{{ number_format($todayVisitors) }}</span>
                        <span class="text-sm text-gray-500">visiteurs</span>
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($todayPageviews) }} pages vues</div>
                </div>
                {{-- 7 days --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">7 derniers jours</span>
                    <div class="mt-2 flex items-baseline gap-3">
                        <span class="text-2xl font-bold" style="color: #1f2937;">{{ number_format($weekVisitors) }}</span>
                        <span class="text-sm text-gray-500">visiteurs</span>
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($weekPageviews) }} pages vues</div>
                </div>
                {{-- 30 days --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <span class="text-sm text-gray-500 dark:text-gray-400">30 derniers jours</span>
                    <div class="mt-2 flex items-baseline gap-3">
                        <span class="text-2xl font-bold" style="color: #1f2937;">{{ number_format($monthVisitors) }}</span>
                        <span class="text-sm text-gray-500">visiteurs</span>
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($monthPageviews) }} pages vues</div>
                </div>
            </div>
        </div>

        {{-- Top pages & Referrers --}}
        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            {{-- Top pages --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Pages les plus visitées <span class="font-normal text-sm text-gray-400">(30j)</span></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Page</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analyticsData['top_pages'] as $page)
                                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                    <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300 truncate max-w-xs">{{ $page['fullPageUrl'] ?? $page['pageTitle'] ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm text-right font-medium text-gray-700 dark:text-gray-300">{{ number_format($page['screenPageViews'] ?? 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top referrers --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Sources de trafic <span class="font-normal text-sm text-gray-400">(30j)</span></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Référent</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analyticsData['top_referrers'] as $ref)
                                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                    <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $ref['pageReferrer'] ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm text-right font-medium text-gray-700 dark:text-gray-300">{{ number_format($ref['screenPageViews'] ?? 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif ($analyticsError ?? false)
        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Statistiques de visite</h3>
            <p class="text-sm" style="color: #991b1b;">Erreur Google Analytics : {{ $analyticsError }}</p>
            <a href="{{ route('admin.settings.index') }}" class="mt-2 inline-block text-sm text-brand-500 hover:underline">Vérifier la configuration</a>
        </div>
    @elseif (!$analyticsConfigured)
        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Statistiques de visite</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Google Analytics n'est pas encore configuré.</p>
            <a href="{{ route('admin.settings.index') }}" class="mt-2 inline-block text-sm text-brand-500 hover:underline">Configurer dans Paramètres</a>
        </div>
    @endif
@endsection
