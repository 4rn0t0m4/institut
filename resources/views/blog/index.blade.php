<x-layouts.app
    title="Blog beauté — Conseils soins naturels"
    meta-description="Découvrez nos conseils beauté : routines de soins, types de peau, actifs naturels, anti-âge… Le blog de l'Institut Corps à Cœur, par une esthéticienne passionnée.">

    @push('json-ld')
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Blog',
        'name' => 'Blog beauté — Institut Corps à Cœur',
        'description' => 'Conseils beauté et soins naturels par une esthéticienne passionnée.',
        'url' => route('blog.index'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush

    {{-- Hero --}}
    <section class="relative overflow-hidden" style="background: linear-gradient(135deg, #276e4415 0%, #276e4430 100%);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 text-center">
            <h1 class="text-3xl md:text-4xl font-semibold mb-4" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                Blog beauté
            </h1>
            <p class="text-lg max-w-2xl mx-auto" style="color: #60916a;">
                Conseils de soins, routines beauté et secrets d'esthéticienne pour une peau naturellement belle.
            </p>
        </div>
    </section>

    {{-- Articles --}}
    <section class="py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <article class="group">
                            <a href="{{ route('blog.show', $post) }}" class="block">
                                @if($post->featured_image)
                                    <div class="aspect-[16/10] rounded-2xl overflow-hidden mb-5">
                                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    </div>
                                @else
                                    <div class="aspect-[16/10] rounded-2xl mb-5 flex items-center justify-center" style="background-color: #276e440d;">
                                        <svg class="w-12 h-12" style="color: #276e4433;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    @if($post->published_at)
                                        <time datetime="{{ $post->published_at->toDateString() }}" class="text-xs font-medium uppercase tracking-wider" style="color: #276e44;">
                                            {{ $post->published_at->translatedFormat('d F Y') }}
                                        </time>
                                    @endif
                                    <h2 class="text-lg font-semibold text-gray-900 group-hover:text-green-700 transition-colors leading-snug">
                                        {{ $post->title }}
                                    </h2>
                                    @if($post->excerpt)
                                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                            {{ $post->excerpt }}
                                        </p>
                                    @endif
                                    <span class="inline-block text-sm font-medium mt-1" style="color: #276e44;">
                                        Lire l'article →
                                    </span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                @if($posts->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                <p class="text-center text-gray-500 py-12">Aucun article pour le moment.</p>
            @endif
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 border-t border-gray-100">
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
                    Me contacter
                </a>
                <a href="{{ route('shop.index') }}"
                   class="inline-block font-medium px-8 py-3.5 rounded-xl transition text-sm border hover:opacity-90"
                   style="border-color: #276e44; color: #276e44;">
                    Voir la boutique
                </a>
            </div>
        </div>
    </section>

</x-layouts.app>
