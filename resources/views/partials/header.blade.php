<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b" style="border-color: #b0f1b9;"
        x-data="{ mobileOpen: false }">
    <div class="w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex items-center justify-between h-16 min-w-0">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="font-semibold text-lg tracking-tight truncate"
               style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Institut Corps à Coeur
            </a>

            {{-- Nav desktop --}}
            <nav class="hidden md:flex items-center gap-4 lg:gap-6 text-sm font-medium min-w-0" style="color: #276e44;">
                @php
                    $menu = \App\Models\Menu::where('location','primary')
                            ->with(['items.children'])->first();
                @endphp
                @if($menu)
                    @foreach($menu->items as $item)
                        @if($item->children->isEmpty())
                            <a href="{{ $item->url }}"
                               class="hover:opacity-70 transition-opacity pb-0.5 border-b-2 truncate {{ request()->url() === $item->url ? 'border-current' : 'border-transparent' }}"
                               style="color: #276e44;">
                                {{ $item->label }}
                            </a>
                        @else
                            <div class="relative" x-data="{ open: false }"
                                 @mouseenter="open=true" @mouseleave="open=false">
                                <button class="flex items-center gap-1 hover:opacity-70 transition-opacity"
                                        style="color: #276e44;">
                                    <span class="truncate">{{ $item->label }}</span>
                                    <svg class="w-3 h-3 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-1"
                                     class="absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-lg py-2 z-50 border"
                                     style="border-color: #b0f1b9;">
                                    @foreach($item->children as $child)
                                        <a href="{{ $child->url }}"
                                           class="block px-4 py-2 text-sm hover:bg-green-50 transition-colors"
                                           style="color: #276e44;">
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('shop.index') }}"
                       class="hover:opacity-70 transition-opacity pb-0.5 border-b-2 {{ request()->routeIs('shop.*') ? 'border-current' : 'border-transparent' }}"
                       style="color: #276e44;">Boutique</a>
                    <a href="{{ route('quiz.show', 'type-de-peau') }}"
                       class="hover:opacity-70 transition-opacity pb-0.5 border-b-2 {{ request()->routeIs('quiz.*') ? 'border-current' : 'border-transparent' }}"
                       style="color: #276e44;">Quiz</a>
                    <a href="{{ route('blog.index') }}"
                       class="hover:opacity-70 transition-opacity pb-0.5 border-b-2 {{ request()->routeIs('blog.*') ? 'border-current' : 'border-transparent' }}"
                       style="color: #276e44;">Blog</a>
                @endif
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

    {{-- Nav mobile --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t px-4 py-4 space-y-1 bg-white"
         style="border-color: #b0f1b9;">
        @if($menu ?? false)
            @foreach($menu->items as $item)
                @if($item->children->isEmpty())
                    <a href="{{ $item->url }}"
                       class="block py-2.5 text-sm font-medium border-b last:border-0"
                       style="color: #276e44; border-color: #c9fad9;">
                        {{ $item->label }}
                    </a>
                @else
                    <div x-data="{ subOpen: false }">
                        <button @click="subOpen = !subOpen"
                                class="flex items-center justify-between w-full py-2.5 text-sm font-medium border-b text-left"
                                style="color: #276e44; border-color: #c9fad9;">
                            {{ $item->label }}
                            <svg class="w-4 h-4 shrink-0 transition-transform duration-200" :class="subOpen ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="subOpen" x-cloak x-transition class="pl-4 space-y-1 pb-1">
                            @foreach($item->children as $child)
                                <a href="{{ $child->url }}"
                                   class="block py-2 text-sm"
                                   style="color: #60916a;">
                                    {{ $child->label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

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
