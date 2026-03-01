<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b relative" style="border-color: #b0f1b9;"
        x-data="{ mobileOpen: false, soinsOpen: false, boutiqueOpen: false }">

    @php
        $menu = \App\Models\Menu::where('location','primary')
                ->with(['items.children'])->first();

        $soinsItems   = $menu ? $menu->items->filter(fn($i) => !in_array($i->id, [19, 40])) : collect();
        $boutiqueItem = $menu ? $menu->items->firstWhere('id', 19) : null;
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
                <a href="https://www.planity.com" target="_blank" rel="noopener"
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
                <a href="{{ route('cart.index') }}"
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
                </a>

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
                @foreach($soinsItems as $item)
                    <div>
                        <a href="{{ $item->url }}"
                           class="text-sm font-semibold hover:opacity-70 transition-opacity"
                           style="color: #276e44;">
                            {{ $item->label }}
                        </a>
                        @if($item->children->isNotEmpty())
                            <ul class="mt-3 space-y-2">
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
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- DROPDOWN "Boutique" (pleine largeur) --}}
    @if($boutiqueItem && $boutiqueItem->children->isNotEmpty())
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center gap-8">
                    <a href="{{ route('shop.index') }}"
                       class="text-sm font-semibold hover:opacity-70 transition-opacity"
                       style="color: #276e44;">
                        Tous les produits
                    </a>
                    @foreach($boutiqueItem->children->sortBy('sort_order') as $child)
                        @if($child->url !== '#')
                            <a href="{{ $child->url }}"
                               class="text-sm hover:opacity-70 transition-opacity"
                               style="color: #60916a;">
                                {{ $child->label }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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

        {{-- Boutique (accordéon) --}}
        @if($boutiqueItem && $boutiqueItem->children->isNotEmpty())
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
                <div x-show="subOpen" x-cloak x-transition class="pl-4 space-y-1 pb-1">
                    <a href="{{ route('shop.index') }}"
                       class="block py-2 text-sm font-medium"
                       style="color: #276e44;">
                        Tous les produits
                    </a>
                    @foreach($boutiqueItem->children->sortBy('sort_order') as $child)
                        @if($child->url !== '#')
                            <a href="{{ $child->url }}"
                               class="block py-2 text-sm"
                               style="color: #60916a;">
                                {{ $child->label }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

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
            <a href="https://www.planity.com" target="_blank" rel="noopener"
               class="block mt-2 text-center font-semibold py-2.5 rounded-lg text-white"
               style="background-color: #276e44;">
                Prendre rendez-vous
            </a>
        </div>
    </div>
</header>
