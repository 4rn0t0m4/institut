@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Clients" :breadcrumbs="['Clients' => null]" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        {{-- Filters --}}
        <div class="flex flex-col gap-4 px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6 sm:flex-row sm:items-center">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email..."
                    class="h-10 rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600">
                    Rechercher
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Reinitialiser</a>
                @endif
            </form>
            <div class="sm:ml-auto text-sm text-gray-500 dark:text-gray-400">
                {{ $customers->total() }} client{{ $customers->total() > 1 ? 's' : '' }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Commandes</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total depense</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Inscrit le</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                        {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-sm font-medium text-gray-800 hover:text-brand-500 dark:text-white/90">
                                            {{ $customer->name }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                {{ $customer->orders_count }}
                            </td>
                            <td class="px-5 py-4 text-sm text-right font-medium text-gray-700 dark:text-gray-300">
                                {{ number_format($customer->orders_total ?? 0, 2, ',', ' ') }} &euro;
                            </td>
                            <td class="px-5 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                {{ $customer->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="text-gray-500 hover:text-brand-500 dark:text-gray-400" title="Voir">
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
                                Aucun client.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
