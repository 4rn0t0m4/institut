<header class="sticky top-0 z-50 bg-white border-b" style="border-color: #b0f1b9;"
        x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="font-semibold text-lg tracking-tight"
               style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Institut Corps à Coeur
            </a>

            {{-- Nav desktop --}}
            <nav class="hidden md:flex items-center gap-5 text-sm font-medium" style="color: #276e44;">
                @php
                    $menu = \App\Models\Menu::where('location','primary')
                            ->with(['items.children'])->first();
                @endphp
                @if($menu)
                    @foreach($menu->items as $item)
                        @if($item->children->isEmpty())
                            <a href="{{ $item->url }}"
                               class="hover:opacity-70 transition-opacity whitespace-nowrap"
                               style="color: #276e44;">
                                {{ $item->label }}
                            </a>
                        @else
                            <div class="relative" x-data="{ open: false }"
                                 @mouseenter="open=true" @mouseleave="open=false">
                                <button class="flex items-center gap-1 hover:opacity-70 transition-opacity whitespace-nowrap"
                                        style="color: #276e44;">
                                    {{ $item->label }}
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute top-full left-0 mt-1 w-52 bg-white rounded-xl shadow-lg py-2 z-50 border"
                                     style="border-color: #b0f1b9;">
                                    @foreach($item->children as $child)
                                        <a href="{{ $child->url }}"
                                           class="block px-4 py-2 text-sm hover:opacity-70 transition-opacity"
                                           style="color: #276e44;">
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('shop.index') }}" style="color: #276e44;" class="hover:opacity-70 transition-opacity">Boutique</a>
                    <a href="{{ route('quiz.show', 'type-de-peau') }}" style="color: #276e44;" class="hover:opacity-70 transition-opacity">Quiz Type de Peau</a>
                    <a href="{{ route('blog.index') }}" style="color: #276e44;" class="hover:opacity-70 transition-opacity">Blog</a>
                @endif
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-4">

                {{-- RDV (desktop) --}}
                <a href="https://www.planity.com" target="_blank" rel="noopener"
                   class="hidden lg:inline-block text-xs font-semibold px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                   style="background-color: #276e44;">
                    Prendre RDV
                </a>

                {{-- Compte --}}
                @auth
                    <a href="{{ route('account.index') }}"
                       class="hidden md:flex items-center gap-1 text-sm font-medium hover:opacity-70 transition-opacity"
                       style="color: #276e44;">
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
                   class="relative flex items-center gap-1 text-sm font-medium hover:opacity-70 transition-opacity"
                   style="color: #276e44;">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <turbo-frame id="cart-count">
                        @php $count = session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0; @endphp
                        @if($count > 0)
                            <span class="text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"
                                  style="background-color: #276e44;">
                                {{ $count }}
                            </span>
                        @endif
                    </turbo-frame>
                </a>

                {{-- Burger mobile --}}
                <button class="md:hidden" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #276e44;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Nav mobile --}}
    <div x-show="mobileOpen" x-transition
         class="md:hidden border-t px-4 py-4 space-y-1 bg-white"
         style="border-color: #b0f1b9;">
        @if($menu ?? false)
            @foreach($menu->items as $item)
                <a href="{{ $item->url }}"
                   class="block py-2.5 text-sm font-medium border-b last:border-0"
                   style="color: #276e44; border-color: #c9fad9;">
                    {{ $item->label }}
                </a>
                @foreach($item->children as $child)
                    <a href="{{ $child->url }}"
                       class="block py-2 pl-4 text-sm"
                       style="color: #60916a;">
                        {{ $child->label }}
                    </a>
                @endforeach
            @endforeach
        @endif
        <a href="{{ route('login') }}" class="block py-2.5 text-sm" style="color: #276e44;">Connexion</a>
        <a href="https://www.planity.com" target="_blank" rel="noopener"
           class="block mt-3 text-center font-semibold py-2.5 rounded-lg text-white"
           style="background-color: #276e44;">
            Prendre rendez-vous
        </a>
    </div>
</header>
