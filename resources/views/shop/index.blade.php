<x-layouts.app title="Boutique" meta-description="Découvrez notre sélection de cosmétiques naturels et produits de beauté. Soins visage, corps, huiles essentielles et accessoires bien-être — Institut Corps à Coeur.">

    @push('json-ld')
    <script type="application/ld+json">
    @php
        $breadcrumbItems = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Boutique', 'item' => route('shop.index')],
        ];
        if (isset($currentCategory) && $currentCategory) {
            $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $currentCategory->name];
        }
    @endphp
    {!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $breadcrumbItems], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Titre + recherche --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">
                @if($currentCategory) {{ $currentCategory->name }}
                @elseif($currentBrand) {{ $currentBrand->name }}
                @else Boutique
                @endif
            </h1>
            <form action="{{ route('shop.index') }}" method="GET" class="flex flex-wrap items-center gap-2"
                  x-data="{ tagOpen: false }" @click.outside="tagOpen = false">
                @if($currentCategory)
                    <input type="hidden" name="categorie" value="{{ $currentCategory->slug }}">
                @endif
                @if($currentBrand)
                    <input type="hidden" name="marque" value="{{ $currentBrand->slug }}">
                @endif
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Rechercher…"
                       class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 w-44">
                <div class="relative">
                    <button type="button" @click="tagOpen = !tagOpen"
                            class="border border-gray-300 rounded px-3 py-1.5 text-sm flex items-center gap-1.5 hover:border-gray-400 transition"
                            style="color: #374151;">
                        Type de peau
                        @php $selectedTags = request('tags', []); @endphp
                        @if(count($selectedTags))
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-white text-xs font-bold" style="background-color: #276e44; font-size: 10px;">{{ count($selectedTags) }}</span>
                        @endif
                        <svg class="w-3 h-3 shrink-0 transition-transform" :class="tagOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="tagOpen" x-cloak x-transition
                         class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-20" style="min-width: 180px;">
                        @foreach($tags as $tag)
                            <label class="flex items-center gap-2 px-3 py-1.5 text-sm hover:bg-gray-50 cursor-pointer" style="color: #374151;">
                                <input type="checkbox" name="tags[]" value="{{ $tag->slug }}"
                                       {{ in_array($tag->slug, $selectedTags) ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                {{ $tag->name }}
                                <span class="text-gray-400 text-xs ml-auto">({{ $tag->products_count }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <button class="bg-green-700 text-white px-3 py-1.5 rounded text-sm hover:bg-green-800 transition">
                    OK
                </button>
            </form>
        </div>

        <div class="flex flex-col lg:flex-row gap-8" x-data="{ filtersOpen: false }">

            {{-- Bouton filtres mobile --}}
            <button @click="filtersOpen = !filtersOpen"
                    class="lg:hidden flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg border border-gray-300 hover:border-gray-400 transition self-start"
                    style="color: #276e44;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtrer
            </button>

            {{-- Sidebar catégories --}}
            <aside class="lg:w-56 flex-shrink-0" :class="filtersOpen ? 'block' : 'hidden lg:block'" style="position: sticky; top: 100px; align-self: flex-start;"
                   x-cloak>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Catégories</p>
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="{{ route('shop.index') }}"
                           class="block px-2 py-1 rounded {{ !$currentCategory ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700 hover:text-green-700' }}">
                            Tous les produits
                        </a>
                    </li>
                    @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('shop.index', ['categorie' => $cat->slug]) }}"
                               class="block px-2 py-1 rounded {{ $currentCategory?->id === $cat->id ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700 hover:text-green-700' }}">
                                {{ $cat->name }}
                            </a>
                            @if($cat->children->isNotEmpty())
                                <ul class="ml-3 mt-1 space-y-1">
                                    @foreach($cat->children as $child)
                                        <li>
                                            <a href="{{ route('shop.index', ['categorie' => $child->slug]) }}"
                                               class="block px-2 py-1 rounded text-xs {{ $currentCategory?->id === $child->id ? 'text-green-700 font-medium' : 'text-gray-500 hover:text-green-700' }}">
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>

                @if($brands->isNotEmpty())
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3 mt-6">Marques</p>
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ route('shop.index', request()->only('categorie', 'q')) }}"
                               class="block px-2 py-1 rounded {{ !$currentBrand ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700 hover:text-green-700' }}">
                                Toutes les marques
                            </a>
                        </li>
                        @foreach($brands as $brand)
                            <li>
                                <a href="{{ route('shop.index', array_merge(request()->only('categorie', 'q'), ['marque' => $brand->slug])) }}"
                                   class="block px-2 py-1 rounded {{ $currentBrand?->id === $brand->id ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700 hover:text-green-700' }}">
                                    {{ $brand->name }} <span class="text-gray-400 text-xs">({{ $brand->products_count }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($tags->isNotEmpty())
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3 mt-6">Type de peau</p>
                    <form method="GET" action="{{ route('shop.index') }}" class="space-y-1 text-sm">
                        @if($currentCategory)<input type="hidden" name="categorie" value="{{ $currentCategory->slug }}">@endif
                        @if($currentBrand)<input type="hidden" name="marque" value="{{ $currentBrand->slug }}">@endif
                        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                        @foreach($tags as $tag)
                            <label class="flex items-center gap-2 px-2 py-1 rounded cursor-pointer hover:bg-gray-50 {{ in_array($tag->slug, $selectedTagSlugs ?? []) ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-700' }}">
                                <input type="checkbox" name="tags[]" value="{{ $tag->slug }}"
                                       {{ in_array($tag->slug, $selectedTagSlugs ?? []) ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500" style="width: 14px; height: 14px;">
                                {{ $tag->name }} <span class="text-gray-400 text-xs ml-auto">({{ $tag->products_count }})</span>
                            </label>
                        @endforeach
                    </form>
                @endif
            </aside>

            {{-- Grille produits --}}
            <div class="flex-1">
                @include('shop.partials.grid')
            </div>

        </div>
    </div>
</x-layouts.app>
