<x-layouts.app :title="$post->title" :meta-description="$post->excerpt">
<article class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="text-xs text-gray-400 mb-6 flex items-center gap-2">
        <a href="{{ route('blog.index') }}" class="hover:text-green-700">Blog</a>
        <span>/</span>
        <span class="text-gray-600">{{ $post->title }}</span>
    </nav>

    <header class="mb-8">
        <p class="text-xs text-gray-400 mb-2">
            {{ \Illuminate\Support\Carbon::parse($post->published_at)->translatedFormat('d F Y') }}
        </p>
        <h1 class="text-3xl font-semibold text-gray-900 leading-tight mb-4">{{ $post->title }}</h1>
        @if($post->excerpt)
            <p class="text-lg text-gray-600 leading-relaxed">{{ $post->excerpt }}</p>
        @endif
    </header>

    @if($post->featured_image)
        <div class="aspect-video rounded-xl overflow-hidden bg-gray-50 mb-8">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                 class="w-full h-full object-cover">
        </div>
    @endif

    <div class="prose prose-gray max-w-none text-sm leading-relaxed">
        {!! $post->content !!}
    </div>
</article>

@if($related->isNotEmpty())
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Articles similaires</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach($related as $p)
                <a href="{{ route('blog.show', $p->slug) }}" class="group">
                    <h3 class="font-medium text-gray-900 group-hover:text-green-700 transition text-sm">
                        {{ $p->title }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ \Illuminate\Support\Carbon::parse($p->published_at)->translatedFormat('d F Y') }}
                    </p>
                </a>
            @endforeach
        </div>
    </section>
@endif
</x-layouts.app>
