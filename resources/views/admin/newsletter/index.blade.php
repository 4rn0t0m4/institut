@extends('admin.layouts.app')

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        tinymce.init({
            selector: '.tinymce-full',
            width: '100%',
            min_width: 600,
            height: 400,
            menubar: false,
            plugins: 'lists link image table code fullscreen media hr wordcount',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link image media | hr blockquote | code fullscreen',
            content_css: isDark ? 'dark' : 'default',
            skin: isDark ? 'oxide-dark' : 'oxide',
            branding: false,
            promotion: false,
            language: 'fr_FR',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/langs7/fr_FR.js',
            automatic_uploads: true,
            images_reuse_filename: false,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '/admin/editor-upload');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.upload.onprogress = function (e) { progress(e.loaded / e.total * 100); };
                    xhr.onload = function () {
                        if (xhr.status !== 200) { reject('Erreur upload (' + xhr.status + '): ' + xhr.responseText); return; }
                        try {
                            var json = JSON.parse(xhr.responseText);
                            resolve(json.location);
                        } catch (e) { reject('Réponse invalide: ' + xhr.responseText.substring(0, 100)); }
                    };
                    xhr.onerror = function () { reject('Erreur réseau'); };
                    var fd = new FormData();
                    fd.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(fd);
                });
            },
        });
    });
</script>
@endpush

@section('content')
    <x-admin.page-breadcrumb title="Envoyer un email" :breadcrumbs="['Newsletter' => null]" />

    @if(session('success'))
        <div class="mb-6 rounded-xl px-4 py-3 text-sm font-medium" style="background-color: #ecfdf5; color: #065f46;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.newsletter.send') }}"
          x-data="{ showConfirm: false, confirmed: false }"
          @submit.prevent="if (confirmed) { $el.submit() } else { showConfirm = true }">
        @csrf

        <div class="max-w-4xl space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Envoyer un email aux clients</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            L'email sera envoyé à <strong class="text-gray-700 dark:text-gray-300">{{ $recipientCount }}</strong> adresse(s) unique(s).
                        </p>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- Sujet --}}
                    <div>
                        <label for="subject" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Objet de l'email *
                        </label>
                        <input type="text" id="subject" name="subject"
                               value="{{ old('subject') }}"
                               placeholder="Ex : Nouveautés de l'été chez Institut Corps à Coeur"
                               required
                               class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('subject') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Contenu --}}
                    <div>
                        <label for="content" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Contenu de l'email *
                        </label>
                        <textarea id="content" name="content" class="tinymce-full" rows="10">{{ old('content') }}</textarea>
                        @error('content') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg px-6 py-3 text-sm font-medium text-white transition-colors" style="background-color: #276e44;" onmouseover="this.style.backgroundColor='#1e5435'" onmouseout="this.style.backgroundColor='#276e44'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Envoyer à {{ $recipientCount }} client(s)
                </button>
            </div>
        </div>

        {{-- Modale de confirmation --}}
        <div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.5);">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6 max-w-md w-full mx-4" @click.outside="showConfirm = false">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #ecfdf5;">
                        <svg class="w-5 h-5" style="color: #276e44;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Confirmer l'envoi</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Vous allez envoyer cet email à <strong>{{ $recipientCount }}</strong> client(s). Cette action est irréversible.
                </p>
                <div class="flex gap-3">
                    <button type="button" @click="showConfirm = false" class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Annuler
                    </button>
                    <button type="button" @click="confirmed = true; showConfirm = false; $nextTick(() => $el.closest('form').submit())" class="flex-1 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition-colors" style="background-color: #276e44;">
                        Confirmer l'envoi
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
