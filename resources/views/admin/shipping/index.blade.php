@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Livraison" :breadcrumbs="['Livraison' => null]" />

    <form method="POST" action="{{ route('admin.shipping.update') }}">
        @csrf
        @method('PUT')

        <div class="max-w-2xl space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Frais de livraison</h3>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="colissimo_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Colissimo (France)</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="colissimo_price" name="colissimo_price"
                                    value="{{ old('colissimo_price', $shipping['colissimo_price']) }}"
                                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                                <span class="absolute right-3 top-3 text-sm text-gray-400">&euro;</span>
                            </div>
                            @error('colissimo_price') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="boxtal_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Point relais (France)</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="boxtal_price" name="boxtal_price"
                                    value="{{ old('boxtal_price', $shipping['boxtal_price']) }}"
                                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                                <span class="absolute right-3 top-3 text-sm text-gray-400">&euro;</span>
                            </div>
                            @error('boxtal_price') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="boxtal_price_international" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Point relais (International)</label>
                        <div class="relative max-w-xs">
                            <input type="number" step="0.01" min="0" id="boxtal_price_international" name="boxtal_price_international"
                                value="{{ old('boxtal_price_international', $shipping['boxtal_price_international']) }}"
                                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                            <span class="absolute right-3 top-3 text-sm text-gray-400">&euro;</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Belgique, Espagne, Italie</p>
                        @error('boxtal_price_international') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <hr class="border-gray-200 dark:border-gray-800">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="free_threshold_fr" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Franco de port France</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="free_threshold_fr" name="free_threshold_fr"
                                    value="{{ old('free_threshold_fr', $shipping['free_threshold_fr']) }}"
                                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                                <span class="absolute right-3 top-3 text-sm text-gray-400">&euro;</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Livraison point relais gratuite au-dessus de ce montant</p>
                            @error('free_threshold_fr') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="free_threshold_international" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Franco de port International</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" id="free_threshold_international" name="free_threshold_international"
                                    value="{{ old('free_threshold_international', $shipping['free_threshold_international']) }}"
                                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 pr-8 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                                <span class="absolute right-3 top-3 text-sm text-gray-400">&euro;</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Livraison point relais gratuite au-dessus de ce montant</p>
                            @error('free_threshold_international') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="rounded-lg p-3 text-xs text-gray-500 dark:text-gray-400" style="background-color: #f0fdf4;">
                        <p><strong class="text-gray-700 dark:text-gray-300">Retrait à l'institut :</strong> toujours gratuit (non modifiable)</p>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                    Enregistrer
                </button>
            </div>
        </div>
    </form>
@endsection
