<div class="max-w-xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="space-y-5">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nom *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $brand->name ?? '') }}" required
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                @error('name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $brand->slug ?? '') }}" placeholder="Auto-genere si vide"
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                @error('slug') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Courte description affichée sur la page d'accueil"
                    class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">{{ old('description', $brand->description ?? '') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="color" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Couleur</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="color" name="color" value="{{ old('color', $brand->color ?? '#276e44') }}"
                        class="h-11 w-14 rounded-lg border border-gray-200 bg-transparent p-1 shadow-theme-xs dark:border-gray-800" />
                    <span class="text-xs text-gray-500 dark:text-gray-400">Couleur utilisée pour le fond de la carte sur la page d'accueil</span>
                </div>
                @error('color') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Image</label>
                @if(isset($brand) && $brand->image_path)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $brand->image_path) }}" alt="{{ $brand->name }}" class="h-32 w-auto rounded-lg object-cover">
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/*"
                    class="w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-300 dark:file:bg-brand-500/10 dark:file:text-brand-400" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Image affichée sur la page d'accueil (format paysage recommandé)</p>
                @error('image') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                    {{ isset($brand) && $brand->exists ? 'Mettre a jour' : 'Creer la marque' }}
                </button>
                <a href="{{ route('admin.brands.index') }}" class="rounded-lg border border-gray-200 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                    Annuler
                </a>
            </div>
        </div>
    </div>
</div>
