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

        {{-- Stock --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Stock</h3>

            <div>
                <label for="stock_quantity" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Quantite en stock</label>
                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}" min="0"
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90" />
                @error('stock_quantity') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
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
