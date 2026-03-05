<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b relative" style="border-color: #b0f1b9;"
        x-data="{ mobileOpen: false, soinsOpen: false, boutiqueOpen: false }">

    @php
        $menu = \App\Models\Menu::where('location','primary')
                ->with(['items.children'])->first();

        $soinsItems   = $menu ? $menu->items->filter(fn($i) => !in_array($i->id, [19, 40])) : collect();
        $boutiqueItem = $menu ? $menu->items->firstWhere('id', 19) : null;

        // Séparer soins avec/sans enfants
        $soinsWithChildren = $soinsItems->filter(fn($i) => $i->children->isNotEmpty());
        $soinsSolo = $soinsItems->filter(fn($i) => $i->children->isEmpty());

        // Catégories produits pour le méga-menu boutique
        $boutiqueCategories = \App\Models\ProductCategory::whereNull('parent_id')
            ->where('slug', '!=', 'non-classe')
            ->with('children')
            ->get()
            ->filter(function($cat) {
                $ids = $cat->children->pluck('id')->push($cat->id);
                return \App\Models\Product::whereIn('category_id', $ids)->where('is_active', true)->exists();
            });

        // Un produit mis en avant pour l'encart boutique
        $featuredProduct = \App\Models\Product::whereNotNull('featured_image_id')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with('featuredImage')
            ->first()
            ?? \App\Models\Product::whereNotNull('featured_image_id')
                ->where('is_active', true)
                ->with('featuredImage')
                ->inRandomOrder()
                ->first();
    @endphp

    <div class="w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Institut Corps à Coeur" style="height: 48px; width: auto;">
            </a>

            {{-- Nav desktop --}}
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium" style="color: #276e44;">

                {{-- NOS SOINS --}}
                <div @mouseenter="soinsOpen=true; boutiqueOpen=false"
                     @mouseleave="soinsOpen=false">
                    <button class="flex items-center gap-1 hover:opacity-70 transition-opacity py-5"
                            style="color: #276e44;" @click="soinsOpen = !soinsOpen; boutiqueOpen=false">
                        Nos Soins
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200" :class="soinsOpen ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                {{-- BOUTIQUE --}}
                <div @mouseenter="boutiqueOpen=true; soinsOpen=false"
                     @mouseleave="boutiqueOpen=false">
                    <button class="flex items-center gap-1 hover:opacity-70 transition-opacity py-5"
                            style="color: #276e44;" @click="boutiqueOpen = !boutiqueOpen; soinsOpen=false">
                        Boutique
                        <svg class="w-3 h-3 shrink-0 transition-transform duration-200" :class="boutiqueOpen ? 'rotate-180' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                {{-- QUIZ PEAU --}}
                <a href="{{ route('quiz.show', 'type-de-peau') }}"
                   class="hover:opacity-70 transition-opacity pb-0.5 border-b-2 {{ request()->routeIs('quiz.*') ? 'border-current' : 'border-transparent' }}"
                   style="color: #276e44;">
                    Quiz Peau
                </a>

            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-3 shrink-0">

                {{-- RDV (desktop) --}}
                <a href="https://www.planity.com/institut-corps-a-coeur-14270-mezidon-vallee-dauge" target="_blank" rel="noopener"
                   class="hidden lg:inline-block text-xs font-semibold px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                   style="background-color: #276e44;">
                    Prendre RDV
                </a>

                {{-- Compte --}}
                @auth
                    <a href="{{ route('account.index') }}"
                       class="hidden md:flex items-center hover:opacity-70 transition-opacity"
                       style="color: #276e44;" title="Mon compte">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="hidden md:inline-block text-sm font-medium hover:opacity-70 transition-opacity"
                       style="color: #276e44;">
                        Connexion
                    </a>
                @endauth

                {{-- Panier --}}
                <div class="relative" x-data="{ cartOpen: false, cartHtml: '' }">
                    <button @click="cartOpen = !cartOpen; if(cartOpen) fetch('{{ route('cart.mini') }}').then(r => r.text()).then(h => cartHtml = h)"
                            class="relative flex items-center hover:opacity-70 transition-opacity p-1"
                            style="color: #276e44;" title="Mon panier">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <turbo-frame id="cart-count">
                            @php $count = session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0; @endphp
                            @if($count > 0)
                                <span class="absolute top-0 right-0 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold leading-none"
                                      style="background-color: #276e44; font-size: 10px;">
                                    {{ $count }}
                                </span>
                            @endif
                        </turbo-frame>
                    </button>
                    <div x-show="cartOpen" x-cloak @click.away="cartOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border z-50"
                         style="border-color: #b0f1b9;">
                        <div x-html="cartHtml"></div>
                    </div>
                </div>

                {{-- Burger mobile --}}
                <button class="md:hidden p-1 rounded-md hover:bg-green-50 transition-colors"
                        @click="mobileOpen = !mobileOpen" aria-label="Menu">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #276e44;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #276e44;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MÉGA-MENU "Nos Soins" (pleine largeur) --}}
    <div x-show="soinsOpen" x-cloak
         @mouseenter="soinsOpen=true" @mouseleave="soinsOpen=false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute left-0 right-0 bg-white shadow-lg border-t z-50"
         style="border-color: #b0f1b9;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">

                {{-- Colonne 1 : Items sans enfants regroupés --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-4" style="color: #276e44; letter-spacing: 0.1em;">Nos prestations</p>
                    <ul style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($soinsSolo as $item)
                            <li>
                                <a href="{{ $item->url }}"
                                   class="text-sm hover:opacity-70 transition-opacity"
                                   style="color: #60916a; display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #b0f1b9; flex-shrink: 0;"></span>
                                    {{ $item->label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Colonnes 2-4 : Items avec enfants --}}
                @foreach($soinsWithChildren as $item)
                    <div>
                        <a href="{{ $item->url }}"
                           class="text-xs font-bold uppercase tracking-wider hover:opacity-70 transition-opacity"
                           style="color: #276e44; letter-spacing: 0.1em;">
                            {{ $item->label }}
                        </a>
                        <ul class="mt-4" style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @foreach($item->children->sortBy('sort_order') as $child)
                                <li>
                                    <a href="{{ $child->url }}"
                                       class="text-sm hover:opacity-70 transition-opacity"
                                       style="color: #60916a;">
                                        {{ $child->label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

            </div>

            {{-- Encart CTA --}}
            <div class="mt-6" style="background: linear-gradient(135deg, #e8fae8 0%, #f0fdf4 100%); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <p class="text-sm font-semibold" style="color: #276e44;">Envie de prendre soin de vous ?</p>
                    <p class="text-xs mt-0.5" style="color: #60916a;">Réservez votre créneau en ligne en quelques clics</p>
                </div>
                <a href="https://www.planity.com/institut-corps-a-coeur-14270-mezidon-vallee-dauge" target="_blank" rel="noopener"
                   class="text-xs font-semibold px-5 py-2.5 rounded-lg text-white transition hover:opacity-90"
                   style="background-color: #276e44; white-space: nowrap;">
                    Prendre rendez-vous →
                </a>
            </div>
        </div>
    </div>

    {{-- MÉGA-MENU "Boutique" (pleine largeur) --}}
    <div x-show="boutiqueOpen" x-cloak
         @mouseenter="boutiqueOpen=true" @mouseleave="boutiqueOpen=false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute left-0 right-0 bg-white shadow-lg border-t z-50"
         style="border-color: #b0f1b9;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div style="display: grid; grid-template-columns: repeat({{ min($boutiqueCategories->count(), 4) }}, 1fr) {{ $featuredProduct ? '280px' : '' }}; gap: 2rem;">

                {{-- Catégories avec sous-catégories --}}
                @foreach($boutiqueCategories as $cat)
                    <div>
                        <a href="{{ route('shop.index', ['categorie' => $cat->slug]) }}"
                           class="text-xs font-bold uppercase tracking-wider hover:opacity-70 transition-opacity"
                           style="color: #276e44; letter-spacing: 0.1em;">
                            {{ $cat->name }}
                        </a>
                        @if($cat->children->isNotEmpty())
                            <ul class="mt-4" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                @foreach($cat->children as $sub)
                                    <li>
                                        <a href="{{ route('shop.index', ['categorie' => $sub->slug]) }}"
                                           class="text-sm hover:opacity-70 transition-opacity"
                                           style="color: #60916a;">
                                            {{ $sub->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach

                {{-- Encart produit mis en avant --}}
                @if($featuredProduct)
                    <div style="background: linear-gradient(135deg, #e8fae8 0%, #f0fdf4 100%); border-radius: 12px; padding: 1rem; display: flex; flex-direction: column;">
                        <a href="{{ route('shop.show', $featuredProduct->slug) }}" class="block" style="flex: 1;">
                            @if($featuredProduct->featuredImage)
                                <img src="{{ $featuredProduct->featuredImage->url }}"
                                     alt="{{ $featuredProduct->name }}"
                                     style="width: 100%; height: 140px; object-fit: cover; border-radius: 8px;">
                            @endif
                            <p class="text-xs font-semibold mt-3" style="color: #276e44;">{{ $featuredProduct->name }}</p>
                            <p class="text-xs mt-1" style="color: #60916a;">{{ number_format($featuredProduct->price, 2, ',', ' ') }} €</p>
                        </a>
                        <a href="{{ route('shop.index') }}"
                           class="text-xs font-semibold mt-3 hover:opacity-70 transition-opacity"
                           style="color: #276e44; display: inline-flex; align-items: center; gap: 0.25rem;">
                            Voir toute la boutique →
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Nav mobile --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t px-4 py-4 space-y-1 bg-white max-h-[80vh] overflow-y-auto"
         style="border-color: #b0f1b9;">

        {{-- Nos Soins (accordéon) --}}
        <div x-data="{ subOpen: false }">
            <button @click="subOpen = !subOpen"
                    class="flex items-center justify-between w-full py-2.5 text-sm font-semibold border-b text-left"
                    style="color: #276e44; border-color: #c9fad9;">
                Nos Soins
                <svg class="w-4 h-4 shrink-0 transition-transform duration-200" :class="subOpen ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="subOpen" x-cloak x-transition class="pl-4 space-y-0.5 pb-2">
                @foreach($soinsItems as $item)
                    <a href="{{ $item->url }}"
                       class="block py-2 text-sm font-medium"
                       style="color: #276e44;">
                        {{ $item->label }}
                    </a>
                    @foreach($item->children->sortBy('sort_order') as $child)
                        <a href="{{ $child->url }}"
                           class="block py-1.5 pl-4 text-sm"
                           style="color: #60916a;">
                            {{ $child->label }}
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Boutique (accordéon avec sous-catégories) --}}
        <div x-data="{ subOpen: false }">
            <button @click="subOpen = !subOpen"
                    class="flex items-center justify-between w-full py-2.5 text-sm font-semibold border-b text-left"
                    style="color: #276e44; border-color: #c9fad9;">
                Boutique
                <svg class="w-4 h-4 shrink-0 transition-transform duration-200" :class="subOpen ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="subOpen" x-cloak x-transition class="pl-4 pb-2">
                <a href="{{ route('shop.index') }}"
                   class="block py-2 text-sm font-medium"
                   style="color: #276e44;">
                    Tous les produits
                </a>
                @foreach($boutiqueCategories as $cat)
                    <a href="{{ route('shop.index', ['categorie' => $cat->slug]) }}"
                       class="block py-2 text-sm font-medium"
                       style="color: #276e44;">
                        {{ $cat->name }}
                    </a>
                    @foreach($cat->children as $sub)
                        <a href="{{ route('shop.index', ['categorie' => $sub->slug]) }}"
                           class="block py-1.5 pl-4 text-sm"
                           style="color: #60916a;">
                            {{ $sub->name }}
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Liens directs --}}
        <a href="{{ route('quiz.show', 'type-de-peau') }}"
           class="block py-2.5 text-sm font-semibold border-b"
           style="color: #276e44; border-color: #c9fad9;">
            Quiz Peau
        </a>

        {{-- Compte + RDV --}}
        <div class="border-t pt-3 space-y-1" style="border-color: #c9fad9;">
            @auth
                <a href="{{ route('account.index') }}" class="block py-2.5 text-sm font-medium" style="color: #276e44;">
                    Mon compte
                </a>
            @else
                <a href="{{ route('login') }}" class="block py-2.5 text-sm" style="color: #276e44;">Connexion</a>
            @endauth
            <a href="https://www.planity.com/institut-corps-a-coeur-14270-mezidon-vallee-dauge" target="_blank" rel="noopener"
               class="block mt-2 text-center font-semibold py-2.5 rounded-lg text-white"
               style="background-color: #276e44;">
                Prendre rendez-vous
            </a>
        </div>
    </div>
</header>
