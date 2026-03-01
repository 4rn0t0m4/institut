@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="{{ $customer->name }}" :breadcrumbs="['Clients' => route('admin.customers.index'), $customer->name => null]" />

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Customer info --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <div class="flex items-center gap-4 mb-5">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-50 text-lg font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $customer->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->email }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Inscrit le</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $customer->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if ($customer->is_admin)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Role</span>
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Admin</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Statistiques</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl p-4" style="background-color: #f9fafb;">
                        <p class="text-2xl font-bold" style="color: #1f2937;">{{ $stats['orders_count'] }}</p>
                        <p class="text-xs mt-1" style="color: #6b7280;">Commande{{ $stats['orders_count'] > 1 ? 's' : '' }}</p>
                    </div>
                    <div class="rounded-xl p-4" style="background-color: #f9fafb;">
                        <p class="text-2xl font-bold" style="color: #276e44;">{{ number_format($stats['total_spent'], 2, ',', ' ') }} &euro;</p>
                        <p class="text-xs mt-1" style="color: #6b7280;">Total dépensé</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders --}}
        <div class="xl:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Commandes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Commande</th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Statut</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-brand-500 hover:underline">
                                            {{ $order->number }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4">
                                        <x-admin.badge :status="$order->status" />
                                    </td>
                                    <td class="px-5 py-4 text-sm text-right font-medium text-gray-700 dark:text-gray-300">
                                        {{ number_format($order->total, 2, ',', ' ') }} &euro;
                                    </td>
                                    <td class="px-5 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-500 hover:text-brand-500 dark:text-gray-400" title="Voir">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Aucune commande pour ce client.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
