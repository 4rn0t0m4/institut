@php $rule = $discount ?? null; @endphp

<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="space-y-5">

            {{-- Nom --}}
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nom *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $rule?->name) }}" required
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30"
                    placeholder="Ex : Soldes été, Bienvenue -10%…">
                @error('name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            {{-- Code promo --}}
            <div>
                <label for="coupon_code" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Code promo</label>
                <input type="text" id="coupon_code" name="coupon_code" value="{{ old('coupon_code', $rule?->coupon_code) }}"
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30"
                    placeholder="Ex : BIENVENUE10" style="text-transform: uppercase;">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Laisser vide pour une remise automatique (sans code).</p>
                @error('coupon_code') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Type de remise --}}
                <div>
                    <label for="discount_type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Type de remise *</label>
                    <select id="discount_type" name="discount_type" required
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                        <option value="percentage" {{ old('discount_type', $rule?->discount_type) === 'percentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                        <option value="flat" {{ old('discount_type', $rule?->discount_type) === 'flat' ? 'selected' : '' }}>Montant fixe (€)</option>
                    </select>
                    @error('discount_type') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Montant --}}
                <div>
                    <label for="discount_amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Montant *</label>
                    <input type="number" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $rule?->discount_amount) }}" required step="0.01" min="0"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30"
                        placeholder="10">
                    @error('discount_amount') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Condition --}}
            <div>
                <label for="type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Condition d'application *</label>
                <select id="type" name="type" required
                    class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                    <option value="all_products" {{ old('type', $rule?->type) === 'all_products' ? 'selected' : '' }}>Tous les produits</option>
                    <option value="cart_value" {{ old('type', $rule?->type) === 'cart_value' ? 'selected' : '' }}>Montant du panier</option>
                    <option value="quantity" {{ old('type', $rule?->type) === 'quantity' ? 'selected' : '' }}>Quantité d'articles</option>
                    <option value="category" {{ old('type', $rule?->type) === 'category' ? 'selected' : '' }}>Catégorie de produits</option>
                </select>
                @error('type') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Min panier --}}
                <div>
                    <label for="min_cart_value" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Panier minimum (€)</label>
                    <input type="number" id="min_cart_value" name="min_cart_value" value="{{ old('min_cart_value', $rule?->min_cart_value) }}" step="0.01" min="0"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">
                    @error('min_cart_value') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Max panier --}}
                <div>
                    <label for="max_cart_value" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Panier maximum (€)</label>
                    <input type="number" id="max_cart_value" name="max_cart_value" value="{{ old('max_cart_value', $rule?->max_cart_value) }}" step="0.01" min="0"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30">
                    @error('max_cart_value') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Date début --}}
                <div>
                    <label for="starts_at" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Date de début</label>
                    <input type="date" id="starts_at" name="starts_at" value="{{ old('starts_at', $rule?->starts_at?->format('Y-m-d')) }}"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                    @error('starts_at') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>

                {{-- Date fin --}}
                <div>
                    <label for="ends_at" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Date de fin</label>
                    <input type="date" id="ends_at" name="ends_at" value="{{ old('ends_at', $rule?->ends_at?->format('Y-m-d')) }}"
                        class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Laisser vide pour illimité.</p>
                    @error('ends_at') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Actif --}}
                <div class="flex items-center gap-3 pt-2">
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $rule?->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Actif</span>
                    </label>
                </div>

                {{-- Cumulable --}}
                <div class="flex items-center gap-3 pt-2">
                    <input type="hidden" name="stackable" value="0">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="stackable" value="1"
                            {{ old('stackable', $rule?->stackable ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cumulable avec d'autres remises</span>
                    </label>
                </div>
            </div>

        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                {{ $rule ? 'Mettre à jour' : 'Créer' }}
            </button>
            <a href="{{ route('admin.discounts.index') }}" class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800">
                Annuler
            </a>
        </div>
    </div>
</div>
