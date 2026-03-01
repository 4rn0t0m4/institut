@php $item = $tag ?? null; @endphp

<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="space-y-5">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nom *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $item?->name) }}" required
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30"
                    placeholder="Ex : Peau sèche, Peau grasse…">
                @error('name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $item?->slug) }}"
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30"
                    placeholder="Généré automatiquement si vide">
                @error('slug') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                {{ $item ? 'Mettre à jour' : 'Créer' }}
            </button>
            <a href="{{ route('admin.tags.index') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                Annuler
            </a>
        </div>
    </div>
</div>
