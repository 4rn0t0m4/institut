@php $banner = \App\Models\Setting::get('sticky_banner'); @endphp
@if($banner)
    @php $b = is_string($banner) ? json_decode($banner, true) : $banner; @endphp
    @if(!empty($b['text']) && !empty($b['active']))
        <div x-data="{ show: !sessionStorage.getItem('banner-dismissed') }"
             x-show="show" x-cloak
             class="relative text-white text-sm py-2.5 px-4" style="background-color: #276e44;">
            <div class="max-w-7xl mx-auto flex items-center justify-center gap-4 flex-wrap">
                <span>{{ $b['text'] }}</span>
                @if(!empty($b['link']) && !empty($b['link_label']))
                    <a href="{{ $b['link'] }}"
                       class="shrink-0 rounded-full px-4 py-1 text-xs font-semibold transition"
                       style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);"
                       onmouseover="this.style.backgroundColor='rgba(255,255,255,0.3)'"
                       onmouseout="this.style.backgroundColor='rgba(255,255,255,0.2)'">
                        {{ $b['link_label'] }} →
                    </a>
                @endif
            </div>
            <button @click="show=false; sessionStorage.setItem('banner-dismissed','1')"
                    class="absolute right-4 top-1/2 -translate-y-1/2 opacity-60 hover:opacity-100 transition text-xl leading-none"
                    aria-label="Fermer">×</button>
        </div>
    @endif
@endif
