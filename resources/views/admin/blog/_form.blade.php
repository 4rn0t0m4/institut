<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    {{-- Main column --}}
    <div class="xl:col-span-2 space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="space-y-5">
                {{-- Title --}}
                <div>
                    <label for="title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Titre *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $post->title ?? '') }}" required
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('title') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $post->slug ?? '') }}" placeholder="Auto-genere si vide"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('slug') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Excerpt --}}
                <div>
                    <label for="excerpt" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Extrait</label>
                    <textarea id="excerpt" name="excerpt" rows="2" placeholder="Court resume affiche sur la page blog"
                        class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                    @error('excerpt') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Content --}}
                <div>
                    <label for="content" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Contenu</label>
                    <textarea id="content" name="content" class="tinymce-full">{{ old('content', $post->content ?? '') }}</textarea>
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
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('meta_title') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="meta_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta description</label>
                    <textarea id="meta_description" name="meta_description" rows="2"
                        class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
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
                        <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="published" {{ old('status', $post->status ?? '') === 'published' ? 'selected' : '' }}>Publie</option>
                    </select>
                    @error('status') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="published_at" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Date de publication</label>
                    <input type="date" id="published_at" name="published_at"
                        value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d') : '') }}"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('published_at') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="featured_image" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Image mise en avant (URL)</label>
                    <input type="text" id="featured_image" name="featured_image" value="{{ old('featured_image', $post->featured_image ?? '') }}" placeholder="/storage/..."
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('featured_image') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                @if(isset($post) && $post->exists)
                    <a href="{{ route('blog.show', $post) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-sm text-brand-500 hover:text-brand-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Voir l'article
                    </a>
                @endif
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" class="flex-1 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600">
                {{ isset($post) && $post->exists ? 'Mettre a jour' : 'Publier' }}
            </button>
            <a href="{{ route('admin.blog.index') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                Annuler
            </a>
        </div>
    </div>
</div>
