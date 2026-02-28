@if($posts->isEmpty())
    <p class="text-gray-400 text-center py-16">Aucun article pour l'instant.</p>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        @foreach($posts as $post)
            <article class="group">
                @if($post->featured_image)
                    <a href="{{ route('blog.show', $post->slug) }}">
                        <div class="aspect-video rounded-xl overflow-hidden bg-gray-50 mb-4">
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                    </a>
                @endif
                <div>
                    <p class="text-xs text-gray-400 mb-1">
                        {{ \Illuminate\Support\Carbon::parse($post->published_at)->translatedFormat('d F Y') }}
                    </p>
                    <h2 class="font-semibold text-gray-900 mb-2 group-hover:text-green-700 transition">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    @if($post->excerpt)
                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                    @endif
                    <a href="{{ route('blog.show', $post->slug) }}"
                       class="inline-block mt-3 text-xs text-green-700 font-medium hover:underline">
                        Lire la suite →
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    {{ $posts->links() }}
@endif
