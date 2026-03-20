{{-- Shared form for create/edit --}}
@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var isDark = document.documentElement.classList.contains('dark');

    // Description courte — barre d'outils minimale
    tinymce.init({
        selector: '.tinymce-light',
        height: 300,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'bold italic | bullist numlist | link | removeformat',
        content_css: isDark ? 'dark' : 'default',
        skin: isDark ? 'oxide-dark' : 'oxide',
        language: 'fr_FR',
        branding: false,
        promotion: false,
        statusbar: false,
    });

    // Description complète — barre d'outils riche
    tinymce.init({
        selector: '.tinymce-full',
        height: 400,
        menubar: false,
        plugins: 'lists link table image code',
        toolbar: 'bold italic underline | blocks | bullist numlist | table | link image | code | removeformat',
        content_css: isDark ? 'dark' : 'default',
        skin: isDark ? 'oxide-dark' : 'oxide',
        language: 'fr_FR',
        branding: false,
        promotion: false,
    });
});
</script>
@endpush
<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    {{-- Main column --}}
    <div class="xl:col-span-2 space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Informations</h3>

            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nom *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug ?? '') }}" placeholder="Auto-genere si vide"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('slug') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Short description --}}
                <div>
                    <label for="short_description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description courte</label>
                    <textarea id="short_description" name="short_description" class="tinymce-light">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                    @error('short_description') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea id="description" name="description" class="tinymce-full">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Recommandation équipe --}}
                <div>
                    <label for="team_recommendation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Notre conseil <span class="font-normal text-gray-400">(encart doré sur la fiche produit)</span>
                    </label>
                    <textarea id="team_recommendation" name="team_recommendation" rows="3" maxlength="1000"
                        placeholder="Ex : Idéal après une séance de massage, à appliquer sur peau encore humide..."
                        class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">{{ old('team_recommendation', $product->team_recommendation ?? '') }}</textarea>
                    @error('team_recommendation') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Bienfaits --}}
                <div>
                    <label for="benefits" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bienfaits <span class="font-normal text-gray-400">(accordéon fiche produit)</span>
                    </label>
                    <textarea id="benefits" name="benefits" class="tinymce-light">{{ old('benefits', $product->benefits ?? '') }}</textarea>
                    @error('benefits') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Utilisation --}}
                <div>
                    <label for="usage_instructions" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Utilisation <span class="font-normal text-gray-400">(accordéon fiche produit)</span>
                    </label>
                    <textarea id="usage_instructions" name="usage_instructions" class="tinymce-light">{{ old('usage_instructions', $product->usage_instructions ?? '') }}</textarea>
                    @error('usage_instructions') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Composition --}}
                <div>
                    <label for="composition" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Composition / Ingrédients <span class="font-normal text-gray-400">(accordéon fiche produit)</span>
                    </label>
                    <textarea id="composition" name="composition" class="tinymce-light">{{ old('composition', $product->composition ?? '') }}</textarea>
                    @error('composition') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Tarification</h3>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Prix TTC *</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $product->price ?? '') }}" step="0.01" min="0" required
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    <p class="mt-1 text-xs text-gray-400">TVA 20% incluse</p>
                    @error('price') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="sale_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Prix promo TTC</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" step="0.01" min="0"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    <p class="mt-1 text-xs text-gray-400">TVA 20% incluse</p>
                    @error('sale_price') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="sku" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('sku') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="unit_measure" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Contenu net</label>
                    <input type="text" id="unit_measure" name="unit_measure" value="{{ old('unit_measure', $product->unit_measure ?? '') }}" placeholder="ex : 100 ml, 50 g"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p class="mt-1 text-xs text-gray-400">Google Shopping (obligatoire EU)</p>
                    @error('unit_measure') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar column --}}
    <div class="space-y-6">
        {{-- Status --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Publication</h3>

            <div class="space-y-4">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                        class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">Visible sur le site</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                        class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">Mis en avant</span>
                </label>
            </div>
        </div>

        {{-- Personnalisation --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Personnalisation</h3>

            <div class="space-y-4">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="personalizable" value="0">
                    <input type="checkbox" name="personalizable" value="1" {{ old('personalizable', $product->personalizable ?? false) ? 'checked' : '' }}
                        class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">Activer la personnalisation</span>
                </label>
                <p class="text-xs text-gray-400">Le client pourra saisir un texte, choisir une police et une couleur.</p>
                <div>
                    <label for="personalization_price" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Supplément personnalisation
                    </label>
                    <input type="number" id="personalization_price" name="personalization_price"
                        value="{{ old('personalization_price', $product->personalization_price ?? '') }}"
                        step="0.01" min="0" placeholder="0.00 = gratuit"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    <p class="mt-1 text-xs text-gray-400">Laisser vide si la personnalisation est gratuite</p>
                    @error('personalization_price') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Category --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Categorie</h3>

            <select name="category_id"
                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                <option value="">— Aucune —</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->parent ? '— ' : '' }}{{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
        </div>

        {{-- Brand --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Marque</h3>

            <select name="brand_id"
                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                <option value="">— Aucune —</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand_id') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
        </div>

        {{-- Tags --}}
        @if(isset($tags) && $tags->count())
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Tags</h3>

            @php $selectedTags = old('tags', isset($product) ? $product->tags->pluck('id')->toArray() : []); @endphp
            <div class="space-y-2">
                @foreach ($tags as $tag)
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}
                            class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Photo principale --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Photo principale</h3>

            @if(isset($product) && $product->featuredImage)
                <div class="mb-4">
                    <img src="{{ $product->featuredImage->url }}" alt="{{ $product->featuredImage->alt }}"
                        class="mb-2 h-24 w-24 rounded-lg object-cover" style="max-width:100px;max-height:100px;" />
                    @if($product->featuredImage->alt)
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">{{ $product->featuredImage->alt }}</p>
                    @endif
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remove_featured_image" value="1"
                            class="h-4 w-4 rounded border-gray-300 text-error-500 focus:ring-error-500 dark:border-gray-700" />
                        <span class="text-sm text-error-600 dark:text-error-400">Supprimer</span>
                    </label>
                </div>
            @endif

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ isset($product) && $product->featuredImage ? 'Choisir une nouvelle photo' : 'Ajouter une photo' }}
                </label>
                <input type="file" name="featured_image" accept="image/*"
                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-sm text-gray-800 file:mr-3 file:rounded file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100 dark:border-gray-800 dark:text-white/90 dark:file:bg-brand-900/20 dark:file:text-brand-400" />
                @error('featured_image') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Galerie --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Galerie</h3>

            @if(isset($product) && $product->galleryImages()->isNotEmpty())
                <div class="mb-4 flex flex-wrap gap-3">
                    @foreach($product->galleryImages() as $img)
                        <div class="flex flex-col items-center gap-1">
                            <img src="{{ $img->url }}" alt="{{ $img->alt }}"
                                class="h-16 w-16 rounded-lg object-cover" style="max-width:60px;max-height:60px;" />
                            <label class="flex items-center gap-1">
                                <input type="checkbox" name="gallery_remove[]" value="{{ $img->id }}"
                                    class="h-3.5 w-3.5 rounded border-gray-300 text-error-500 focus:ring-error-500 dark:border-gray-700" />
                                <span class="text-xs text-error-600 dark:text-error-400">Retirer</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Ajouter des photos</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple
                    class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-2 text-sm text-gray-800 file:mr-3 file:rounded file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100 dark:border-gray-800 dark:text-white/90 dark:file:bg-brand-900/20 dark:file:text-brand-400" />
                @error('gallery_images.*') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Alertes stock --}}
        @if(isset($product) && $product->exists)
            @php $pendingAlerts = $product->stockNotifications()->whereNull('notified_at')->count(); @endphp
            @if($pendingAlerts > 0)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-900/20 md:p-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-800/30">
                            <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">{{ $pendingAlerts }} alerte{{ $pendingAlerts > 1 ? 's' : '' }} en attente</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400">{{ $pendingAlerts }} personne{{ $pendingAlerts > 1 ? 's' : '' }} ser{{ $pendingAlerts > 1 ? 'ont notifiée' : 'a notifiée' }}{{ $pendingAlerts > 1 ? 's' : '' }} au retour en stock</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Stock --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Stock</h3>

            <div class="space-y-5">
                <div>
                    <label for="stock_status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Statut du stock</label>
                    <select id="stock_status" name="stock_status"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                        <option value="instock" {{ old('stock_status', $product->stock_status ?? 'instock') === 'instock' ? 'selected' : '' }}>En stock</option>
                        <option value="outofstock" {{ old('stock_status', $product->stock_status ?? '') === 'outofstock' ? 'selected' : '' }}>Rupture de stock</option>
                        <option value="onbackorder" {{ old('stock_status', $product->stock_status ?? '') === 'onbackorder' ? 'selected' : '' }}>En commande</option>
                    </select>
                    @error('stock_status') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="stock_quantity" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Quantite en stock</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}" min="0"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                    @error('stock_quantity') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" class="flex-1 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white hover:bg-brand-600">
                {{ isset($product) && $product->exists ? 'Mettre a jour' : 'Creer le produit' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                Annuler
            </a>
        </div>
    </div>
</div>
