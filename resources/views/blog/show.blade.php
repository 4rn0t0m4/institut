<x-layouts.app
    :title="$post->meta_title ?: $post->title"
    :meta-description="$post->meta_description ?: $post->excerpt">

    @push('json-ld')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $post->excerpt ?? '',
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'author' => ['@type' => 'Organization', 'name' => 'Institut Corps à Cœur'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Institut Corps à Cœur'],
        'mainEntityOfPage' => route('blog.show', $post),
        'image' => $post->featured_image ? url($post->featured_image) : null,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

    <article>
        {{-- Hero --}}
        <header class="relative overflow-hidden" style="background: linear-gradient(135deg, #276e4415 0%, #276e4430 100%);">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
                <nav class="text-sm mb-6" style="color: #276e4499;">
                    <a href="{{ route('home') }}" class="hover:underline">Accueil</a>
                    <span class="mx-2">›</span>
                    <a href="{{ route('blog.index') }}" class="hover:underline">Blog</a>
                    <span class="mx-2">›</span>
                    <span>{{ $post->title }}</span>
                </nav>

                @if($post->published_at)
                    <time datetime="{{ $post->published_at->toDateString() }}" class="text-sm font-medium uppercase tracking-wider" style="color: #276e44;">
                        {{ $post->published_at->translatedFormat('d F Y') }}
                    </time>
                @endif

                <h1 class="text-3xl md:text-4xl font-semibold leading-tight mt-3" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                    {{ $post->title }}
                </h1>

                @if($post->excerpt)
                    <p class="mt-5 text-lg leading-relaxed" style="color: #60916a;">
                        {{ $post->excerpt }}
                    </p>
                @endif
            </div>
        </header>

        {{-- Featured image --}}
        @if($post->featured_image)
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 mb-12">
                <div class="aspect-[2/1] rounded-2xl overflow-hidden shadow-lg">
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover" loading="eager">
                </div>
            </div>
        @endif

        {{-- Content --}}
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="blog-content">
                {!! $post->content !!}
            </div>
        </div>
    </article>

    <style>
        .blog-content h2 {
            font-family: 'Source Serif Pro', Georgia, serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #276e44;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #276e4433;
        }
        .blog-content h2:first-child { margin-top: 0; }
        .blog-content h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #276e44;
            margin-top: 1.8rem;
            margin-bottom: 0.6rem;
        }
        .blog-content p {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #374151;
            margin-bottom: 1rem;
        }
        .blog-content ul, .blog-content ol {
            list-style: none;
            padding-left: 0;
            margin-bottom: 1.2rem;
        }
        .blog-content ul li, .blog-content ol li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.6rem;
            font-size: 1.05rem;
            line-height: 1.7;
            color: #374151;
        }
        .blog-content ul li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.55rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #276e44;
            opacity: 0.4;
        }
        .blog-content strong { color: #1f2937; font-weight: 600; }
        .blog-content a { color: #276e44; text-decoration: none; font-weight: 500; }
        .blog-content a:hover { text-decoration: underline; }
        .blog-content blockquote {
            border-left: 4px solid #276e44;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            background-color: #276e440a;
            border-radius: 0 0.75rem 0.75rem 0;
            font-style: italic;
            color: #374151;
        }
        .blog-content img {
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            margin: 1.5rem 0;
        }
        .blog-content hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 2rem 0;
        }
    </style>

    {{-- Produits recommandes --}}
    @if($recommendedProducts->isNotEmpty())
        <section class="py-16 border-t border-gray-100">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold mb-2" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                    Nos produits recommandés
                </h2>
                <p class="text-sm mb-8" style="color: #60916a;">Sélectionnés pour accompagner les conseils de cet article.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($recommendedProducts as $product)
                        <x-product-card :product="$product"/>
                    @endforeach
                </div>
                <div class="text-center mt-8">
                    <a href="{{ route('shop.index') }}" class="inline-block font-medium px-8 py-3 rounded-xl transition text-sm border hover:opacity-90"
                       style="border-color: #276e44; color: #276e44;">
                        Voir toute la boutique →
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Articles associes --}}
    @if($relatedPosts->isNotEmpty())
        <section class="py-16 border-t border-gray-100" style="background-color: #f0fdf4;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold mb-8" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                    Autres articles
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($relatedPosts as $related)
                        <article class="group">
                            <a href="{{ route('blog.show', $related) }}" class="block">
                                @if($related->featured_image)
                                    <div class="aspect-[16/10] rounded-2xl overflow-hidden mb-4">
                                        <img src="{{ $related->featured_image }}" alt="{{ $related->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    </div>
                                @else
                                    <div class="aspect-[16/10] rounded-2xl mb-4 flex items-center justify-center" style="background-color: #276e440d;">
                                        <svg class="w-10 h-10" style="color: #276e4433;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                @endif
                                <h3 class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors leading-snug">
                                    {{ $related->title }}
                                </h3>
                                @if($related->published_at)
                                    <time class="text-xs text-gray-500 mt-1 block">{{ $related->published_at->translatedFormat('d F Y') }}</time>
                                @endif
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="py-16 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-semibold mb-4" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Besoin d'un conseil personnalisé ?
            </h2>
            <p class="mb-8 leading-relaxed" style="color: #60916a;">
                En tant qu'esthéticienne, je connais chaque produit pour les utiliser quotidiennement en institut.
                N'hésitez pas à me contacter pour un conseil adapté à votre peau.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('contact.show') }}"
                   class="inline-block font-semibold px-8 py-3.5 rounded-xl transition text-sm text-white hover:opacity-90"
                   style="background-color: #276e44;">
                    Prendre rendez-vous
                </a>
                <a href="{{ route('shop.index') }}"
                   class="inline-block font-medium px-8 py-3.5 rounded-xl transition text-sm border hover:opacity-90"
                   style="border-color: #276e44; color: #276e44;">
                    Découvrir la boutique
                </a>
            </div>
        </div>
    </section>

</x-layouts.app>
