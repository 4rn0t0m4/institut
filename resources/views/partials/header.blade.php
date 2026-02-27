<header class="sticky top-0 z-50 bg-white border-b border-gray-200" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="/" class="font-semibold text-lg tracking-tight text-green-800">
                Institut Corps à Coeur
            </a>

            {{-- Nav desktop --}}
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-700">
                @php
                    $menu = \App\Models\Menu::where('location','primary')
                            ->with(['items.children'])->first();
                @endphp
                @if($menu)
                    @foreach($menu->items as $item)
                        @if($item->children->isEmpty())
                            <a href="{{ $item->url }}" class="hover:text-green-700 transition-colors">
                                {{ $item->label }}
                            </a>
                        @else
                            <div class="relative" x-data="{ open: false }"
                                 @mouseenter="open=true" @mouseleave="open=false">
                                <button class="flex items-center gap-1 hover:text-green-700 transition-colors">
                                    {{ $item->label }}
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-100 rounded shadow-lg py-1 z-50">
                                    @foreach($item->children as $child)
                                        <a href="{{ $child->url }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700">
                                            {{ $child->label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-4">
                {{-- Panier --}}
                <a href="{{ route('cart.index') }}" class="relative flex items-center gap-1 text-sm font-medium text-gray-700 hover:text-green-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <turbo-frame id="cart-count">
                        @php $count = session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0; @endphp
                        @if($count > 0)
                            <span class="bg-green-700 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $count }}
                            </span>
                        @endif
                    </turbo-frame>
                </a>

                {{-- Burger mobile --}}
                <button class="md:hidden" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                    <svg class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Nav mobile --}}
    <div x-show="mobileOpen" x-transition class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-2">
        @if($menu ?? false)
            @foreach($menu->items as $item)
                <a href="{{ $item->url }}" class="block py-2 text-sm text-gray-700 hover:text-green-700">
                    {{ $item->label }}
                </a>
                @foreach($item->children as $child)
                    <a href="{{ $child->url }}" class="block py-2 pl-4 text-sm text-gray-500 hover:text-green-700">
                        {{ $child->label }}
                    </a>
                @endforeach
            @endforeach
        @endif
    </div>
</header>
