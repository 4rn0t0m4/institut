<x-layouts.app :title="$page->meta_title ?? $page->title" :meta-description="$page->meta_description">
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if($page->featured_image)
        <div class="aspect-video rounded-xl overflow-hidden bg-gray-50 mb-8">
            <img src="{{ $page->featured_image }}" alt="{{ $page->title }}"
                 class="w-full h-full object-cover">
        </div>
    @endif

    <h1 class="text-3xl font-semibold text-gray-900 mb-8">{{ $page->title }}</h1>

    <div class="prose prose-gray max-w-none text-sm leading-relaxed">
        {!! $page->content !!}
    </div>
</div>
</x-layouts.app>
