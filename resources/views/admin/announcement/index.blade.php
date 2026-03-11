@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Barre d'annonce" :breadcrumbs="['Barre d\'annonce' => null]" />

    @if(session('success'))
        <div class="mb-6 rounded-xl px-4 py-3 text-sm font-medium" style="background-color: #ecfdf5; color: #065f46;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.announcement.update') }}">
        @csrf
        @method('PUT')

        <div class="max-w-2xl space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Barre d'annonce</h3>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                    Affiche un bandeau vert en haut du site avec un message promotionnel et un bouton. La barre se ferme au clic et ne réapparaît qu'à la prochaine session.
                </p>

                <div class="space-y-5">

                    {{-- Toggle activer --}}
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="banner_active" id="banner_active" value="1"
                                   {{ $banner['active'] ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-brand-500 peer-focus:ring-2 peer-focus:ring-brand-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-full dark:bg-gray-700"></div>
                        </label>
                        <label for="banner_active" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Activer la barre d'annonce
                        </label>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="banner_text" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Message <span class="text-gray-400 font-normal">(300 caractères max)</span>
                        </label>
                        <input type="text" id="banner_text" name="banner_text"
                               value="{{ old('banner_text', $banner['text']) }}"
                               placeholder="Profitez de -10% sur toute la boutique avec le code PRINTEMPS"
                               class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('banner_text') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Bouton : texte + URL --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="banner_link_label" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Texte du bouton
                            </label>
                            <input type="text" id="banner_link_label" name="banner_link_label"
                                   value="{{ old('banner_link_label', $banner['link_label']) }}"
                                   placeholder="Voir les offres"
                                   class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                            @error('banner_link_label') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="banner_link" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                URL du bouton
                            </label>
                            <input type="url" id="banner_link" name="banner_link"
                                   value="{{ old('banner_link', $banner['link']) }}"
                                   placeholder="https://institutcorpsacoeur.fr/boutique"
                                   class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                            @error('banner_link') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Aperçu --}}
                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                        <p class="px-3 py-2 text-xs text-gray-400 bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-gray-700">Aperçu</p>
                        <div class="relative text-white text-sm py-2.5 px-4" style="background-color: #276e44;">
                            <div class="flex items-center justify-center gap-4 flex-wrap">
                                <span id="preview-text" class="text-sm">{{ $banner['text'] ?: 'Votre message apparaîtra ici' }}</span>
                                <span id="preview-btn"
                                      class="shrink-0 rounded-full px-4 py-1 text-xs font-semibold"
                                      style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);">
                                    {{ $banner['link_label'] ?: 'Bouton' }} →
                                </span>
                            </div>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 opacity-60 text-xl leading-none">×</span>
                        </div>
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

    <script>
        document.getElementById('banner_text').addEventListener('input', function() {
            document.getElementById('preview-text').textContent = this.value || 'Votre message apparaîtra ici';
        });
        document.getElementById('banner_link_label').addEventListener('input', function() {
            document.getElementById('preview-btn').textContent = (this.value || 'Bouton') + ' →';
        });
    </script>
@endsection
