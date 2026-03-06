@extends('admin.layouts.app')

@section('content')
    <x-admin.page-breadcrumb title="Produits" :breadcrumbs="['Produits' => null]" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        {{-- Header --}}
        <div class="flex flex-col gap-4 px-5 py-4 border-b border-gray-200 dark:border-gray-800 md:px-6 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                    class="h-10 rounded-lg border border-gray-200 bg-transparent px-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30" />
                <select name="category"
                    class="h-10 rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-700 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                    <option value="">Toutes les categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->parent ? '— ' : '' }}{{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status"
                    class="h-10 rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-700 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Visible</option>
                    <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Masque</option>
                </select>
                <select name="brand"
                    class="h-10 rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-700 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/3 dark:text-white/90">
                    <option value="">Toutes les marques</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600">
                    Filtrer
                </button>
                @if (request()->hasAny(['search', 'category', 'status', 'brand']))
                    <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Reinitialiser</a>
                @endif
            </form>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau produit
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                @php
                    $currentSort = request('sort', 'created_at');
                    $currentDir = request('dir', 'desc');
                    $sortUrl = function (string $col) use ($currentSort, $currentDir) {
                        $dir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
                        return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $dir]);
                    };
                    $sortIcon = function (string $col) use ($currentSort, $currentDir) {
                        if ($currentSort !== $col) return '';
                        return $currentDir === 'asc' ? ' ↑' : ' ↓';
                    };
                @endphp
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                            <a href="{{ $sortUrl('name') }}" class="hover:text-gray-700 dark:hover:text-white/80">Nom{!! $sortIcon('name') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Categorie</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Prix</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                            <a href="{{ $sortUrl('stock_quantity') }}" class="hover:text-gray-700 dark:hover:text-white/80">Stock{!! $sortIcon('stock_quantity') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Vendus</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                            <a href="{{ $sortUrl('is_active') }}" class="hover:text-gray-700 dark:hover:text-white/80">Statut{!! $sortIcon('is_active') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-sm font-medium text-gray-800 hover:text-brand-500 dark:text-white/90">{{ $product->name }}</a>
                                @if ($product->sku)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $product->category?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                @php
                                    $priceTTC = $product->sale_price ?? $product->price;
                                    $priceHT = $priceTTC / 1.20;
                                @endphp
                                @if ($product->sale_price)
                                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($priceHT, 2, ',', ' ') }} &euro; HT</span>
                                    <br><span class="text-xs text-gray-400"><span class="line-through">{{ number_format($product->price, 2, ',', ' ') }}</span> {{ number_format($product->sale_price, 2, ',', ' ') }} &euro; TTC</span>
                                @else
                                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format($priceHT, 2, ',', ' ') }} &euro; HT</span>
                                    <br><span class="text-xs text-gray-400">{{ number_format($priceTTC, 2, ',', ' ') }} &euro; TTC</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                @if ($product->manage_stock)
                                    @if ($product->stock_quantity <= 0)
                                        <span class="text-error-500 font-medium">0</span>
                                    @elseif ($product->stock_quantity <= 5)
                                        <span class="text-warning-500 font-medium">{{ $product->stock_quantity }}</span>
                                    @else
                                        {{ $product->stock_quantity }}
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                {{ (int) $product->total_sold }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($product->is_active)
                                    <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">Visible</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">Masque</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ $product->url() }}" target="_blank"
                                        class="text-gray-500 hover:text-brand-500 dark:text-gray-400" title="Voir sur le site">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="text-gray-500 hover:text-brand-500 dark:text-gray-400" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                        onsubmit="return confirm('Supprimer ce produit ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-error-500 dark:text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucun produit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
