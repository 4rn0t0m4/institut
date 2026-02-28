@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier commande {{ $order->number }}" :breadcrumbs="['Commandes' => route('admin.orders.index'), $order->number => route('admin.orders.show', $order), 'Modifier' => null]" />

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <div class="space-y-5">
                    {{-- Status --}}
                    <div>
                        <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Statut *</label>
                        <select id="status" name="status" required
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                            <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="processing" {{ old('status', $order->status) === 'processing' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Terminee</option>
                            <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Annulee</option>
                        </select>
                        @error('status') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tracking carrier --}}
                    <div>
                        <label for="tracking_carrier" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Transporteur</label>
                        <input type="text" id="tracking_carrier" name="tracking_carrier" value="{{ old('tracking_carrier', $order->tracking_carrier ?? '') }}" placeholder="Colissimo, Chronopost..."
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('tracking_carrier') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tracking number --}}
                    <div>
                        <label for="tracking_number" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Numero de suivi</label>
                        <input type="text" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number ?? '') }}"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('tracking_number') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Customer note --}}
                    <div>
                        <label for="customer_note" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                        <textarea id="customer_note" name="customer_note" rows="3"
                            class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">{{ old('customer_note', $order->customer_note ?? '') }}</textarea>
                        @error('customer_note') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                        Mettre a jour
                    </button>
                    <a href="{{ route('admin.orders.show', $order) }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                        Annuler
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
