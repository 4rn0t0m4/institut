<x-layouts.app title="Boutique">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Titre + recherche --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">
                @if($currentCategory) {{ $currentCategory->name }} @else Boutique @endif
            </h1>
            <form action="{{ route('shop.index') }}" method="GET" class="flex gap-2">
                @if($currentCategory)
                    <input type="hidden" name="categorie" value="{{ $currentCategory->slug }}">
                @endif
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Rechercher…"
                       class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 w-52">
                <button class="bg-green-700 text-white px-3 py-1.5 rounded text-sm hover:bg-green-800 transition">
                    OK
                </button>
            </form>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Sidebar catégories --}}
            <aside class="lg:w-56 flex-shrink-0">
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
            </aside>

            {{-- Grille produits --}}
            <div class="flex-1">
                <turbo-frame id="products-grid">
                    @include('shop.partials.grid')
                </turbo-frame>
            </div>

        </div>
    </div>
</x-layouts.app>
