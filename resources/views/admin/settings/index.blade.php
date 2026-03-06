@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Paramètres" :breadcrumbs="['Paramètres' => null]" />

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="max-w-2xl space-y-6">
            {{-- Tracking & Analytics --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Tracking & Analytics</h3>
                <div class="space-y-5">
                    <div>
                        <label for="google_analytics_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Google Analytics ID</label>
                        <input type="text" id="google_analytics_id" name="google_analytics_id"
                            value="{{ old('google_analytics_id', $settings['google_analytics_id']) }}"
                            placeholder="GT-XXXXXXXX"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Identifiant Google Analytics (ex: GT-NS94DCGX)</p>
                        @error('google_analytics_id') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="google_ads_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Google Ads ID</label>
                        <input type="text" id="google_ads_id" name="google_ads_id"
                            value="{{ old('google_ads_id', $settings['google_ads_id']) }}"
                            placeholder="AW-XXXXXXXXXXX"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Identifiant Google Ads pour le suivi des conversions (ex: AW-17605875471)</p>
                        @error('google_ads_id') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Analytics API (Spatie) --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Google Analytics API</h3>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                    Pour afficher les statistiques de visite dans le tableau de bord, configurez l'accès à l'API Google Analytics Data.
                </p>
                <div class="space-y-5">
                    <div>
                        <label for="analytics_property_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Property ID (GA4)</label>
                        <input type="text" id="analytics_property_id" name="analytics_property_id"
                            value="{{ old('analytics_property_id', $settings['analytics_property_id']) }}"
                            placeholder="123456789"
                            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Disponible dans Google Analytics : Admin > Paramètres de la propriété</p>
                        @error('analytics_property_id') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Fichier credentials (Service Account)</label>
                        <div class="flex items-center gap-3">
                            @if ($credentialsExist)
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium" style="background-color: #ecfdf5; color: #065f46;">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Fichier présent
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium" style="background-color: #fef2f2; color: #991b1b;">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Fichier manquant
                                </span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                            Déposez le fichier JSON du service account dans :<br>
                            <code class="text-xs" style="color: #6b7280;">storage/app/analytics/service-account-credentials.json</code>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div>
                <button type="submit" class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                    Enregistrer
                </button>
            </div>
        </div>
    </form>

@endsection
