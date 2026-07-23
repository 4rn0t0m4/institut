@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Modifier les adresses - {{ $order->number }}" :breadcrumbs="['Commandes' => route('admin.orders.index'), $order->number => route('admin.orders.show', $order), 'Adresses' => null]" />

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('admin.orders.update-addresses', $order) }}">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                {{-- Facturation --}}
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Adresse de facturation</h3>
                </div>

                <div class="space-y-5 px-5 py-6 md:px-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="billing_first_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Prénom *</label>
                            <input type="text" id="billing_first_name" name="billing_first_name" value="{{ old('billing_first_name', $order->billing_first_name) }}" required
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('billing_first_name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="billing_last_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nom *</label>
                            <input type="text" id="billing_last_name" name="billing_last_name" value="{{ old('billing_last_name', $order->billing_last_name) }}" required
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('billing_last_name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="billing_email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                        <input type="email" id="billing_email" name="billing_email" value="{{ old('billing_email', $order->billing_email) }}" required
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('billing_email') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="billing_phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                        <input type="tel" id="billing_phone" name="billing_phone" value="{{ old('billing_phone', $order->billing_phone) }}"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('billing_phone') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="billing_address_1" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse *</label>
                        <input type="text" id="billing_address_1" name="billing_address_1" value="{{ old('billing_address_1', $order->billing_address_1) }}" required
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('billing_address_1') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="billing_address_2" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Complément d'adresse</label>
                        <input type="text" id="billing_address_2" name="billing_address_2" value="{{ old('billing_address_2', $order->billing_address_2) }}"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('billing_address_2') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="billing_postcode" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Code postal *</label>
                            <input type="text" id="billing_postcode" name="billing_postcode" value="{{ old('billing_postcode', $order->billing_postcode) }}" required
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('billing_postcode') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="billing_city" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Ville *</label>
                            <input type="text" id="billing_city" name="billing_city" value="{{ old('billing_city', $order->billing_city) }}" required
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('billing_city') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="billing_country" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Pays *</label>
                        <input type="text" id="billing_country" name="billing_country" value="{{ old('billing_country', $order->billing_country) }}" placeholder="FR, BE, ES, IT..." required maxlength="2"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('billing_country') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Livraison --}}
                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800 md:px-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Adresse de livraison</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Laissez vides pour utiliser l'adresse de facturation</p>
                </div>

                <div class="space-y-5 px-5 py-6 md:px-6 border-t border-gray-200 dark:border-gray-800">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="shipping_first_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Prénom</label>
                            <input type="text" id="shipping_first_name" name="shipping_first_name" value="{{ old('shipping_first_name', $order->shipping_first_name) }}"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('shipping_first_name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="shipping_last_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                            <input type="text" id="shipping_last_name" name="shipping_last_name" value="{{ old('shipping_last_name', $order->shipping_last_name) }}"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('shipping_last_name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="shipping_address_1" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse</label>
                        <input type="text" id="shipping_address_1" name="shipping_address_1" value="{{ old('shipping_address_1', $order->shipping_address_1) }}"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('shipping_address_1') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="shipping_address_2" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Complément d'adresse</label>
                        <input type="text" id="shipping_address_2" name="shipping_address_2" value="{{ old('shipping_address_2', $order->shipping_address_2) }}"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('shipping_address_2') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="shipping_postcode" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Code postal</label>
                            <input type="text" id="shipping_postcode" name="shipping_postcode" value="{{ old('shipping_postcode', $order->shipping_postcode) }}"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('shipping_postcode') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="shipping_city" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Ville</label>
                            <input type="text" id="shipping_city" name="shipping_city" value="{{ old('shipping_city', $order->shipping_city) }}"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            @error('shipping_city') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="shipping_country" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Pays</label>
                        <input type="text" id="shipping_country" name="shipping_country" value="{{ old('shipping_country', $order->shipping_country) }}" placeholder="FR, BE, ES, IT..." maxlength="2"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                        @error('shipping_country') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="px-5 py-6 border-t border-gray-200 dark:border-gray-800 md:px-6 flex gap-3">
                    <button type="submit" class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                        Mettre à jour
                    </button>
                    <a href="{{ route('admin.orders.show', $order) }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                        Annuler
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
