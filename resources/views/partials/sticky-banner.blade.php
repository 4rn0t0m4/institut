@php $banner = \App\Models\Setting::get('sticky_banner'); @endphp
@if($banner)
    @php $bannerData = is_string($banner) ? json_decode($banner, true) : $banner; @endphp
    @if(!empty($bannerData['text']) && !empty($bannerData['active']))
        <div id="sticky-banner"
             x-data="{ show: !localStorage.getItem('banner-dismissed') }"
             x-show="show"
             class="bg-green-700 text-white text-sm text-center py-2 px-4">
            {{ $bannerData['text'] }}
            @if(!empty($bannerData['link']))
                <a href="{{ $bannerData['link'] }}" class="underline ml-2">
                    {{ $bannerData['link_label'] ?? 'En savoir plus' }}
                </a>
            @endif
            <button @click="show=false; localStorage.setItem('banner-dismissed','1')"
                    class="ml-4 opacity-70 hover:opacity-100" aria-label="Fermer">×</button>
        </div>
    @endif
@endif
