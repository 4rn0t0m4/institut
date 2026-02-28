<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    {{-- Main column --}}
    <div class="xl:col-span-2 space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="space-y-5">
                {{-- Title --}}
                <div>
                    <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Titre *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $page->title ?? '') }}" required
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('title') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug ?? '') }}" placeholder="Auto-genere si vide"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('slug') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Content --}}
                <div>
                    <label for="content" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Contenu</label>
                    <textarea id="content" name="content" rows="12"
                        class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">{{ old('content', $page->content ?? '') }}</textarea>
                    @error('content') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">SEO</h3>
            <div class="space-y-5">
                <div>
                    <label for="meta_title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta titre</label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('meta_title') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta description</label>
                    <textarea id="meta_description" name="meta_description" rows="2"
                        class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                    @error('meta_description') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Publication --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Publication</h3>

            <div class="space-y-4">
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Statut *</label>
                    <select id="status" name="status" required
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                        <option value="draft" {{ old('status', $page->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="published" {{ old('status', $page->status ?? '') === 'published' ? 'selected' : '' }}>Publiee</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="parent_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Page parente</label>
                    <select id="parent_id" name="parent_id"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                        <option value="">— Aucune —</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $page->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="template" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Template</label>
                    <input type="text" id="template" name="template" value="{{ old('template', $page->template ?? '') }}" placeholder="default"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('template') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" class="flex-1 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600">
                {{ isset($page) && $page->exists ? 'Mettre a jour' : 'Creer la page' }}
            </button>
            <a href="{{ route('admin.pages.index') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                Annuler
            </a>
        </div>
    </div>
</div>
