<x-layouts.app title="Nos marques — Cosmétiques naturels et professionnels" meta-description="Découvrez les marques sélectionnées par Institut Corps à Cœur : Eskalia, JADEA, Charme d'Orient, Nakupenda. Cosmétiques naturels testés en institut par une esthéticienne passionnée.">

<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm mb-8" style="color: #60916a;">
            <a href="{{ route('home') }}" class="hover:underline">Accueil</a>
            <span class="mx-2">›</span>
            <span>Nos marques</span>
        </nav>

        <h1 class="text-3xl md:text-4xl font-semibold mb-4" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Nos marques
        </h1>
        <p class="text-lg mb-12 max-w-3xl" style="color: #60916a;">
            Chaque marque proposée chez Corps à Cœur a été rigoureusement testée en institut.
            Je sélectionne uniquement des gammes dont j'ai pu vérifier l'efficacité et la qualité des ingrédients.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($brands as $brand)
                <a href="{{ route('brands.show', $brand) }}"
                   class="group rounded-2xl overflow-hidden border transition hover:shadow-lg"
                   style="border-color: {{ $brand->color ?? '#276e44' }}30;">
                    @if($brand->image_path)
                        <div class="aspect-[16/9] overflow-hidden">
                            <img src="{{ asset('storage/' . $brand->image_path) }}" alt="{{ $brand->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="aspect-[16/9]" style="background: linear-gradient(135deg, {{ $brand->color ?? '#276e44' }}15 0%, {{ $brand->color ?? '#276e44' }}30 100%);"></div>
                    @endif
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2" style="color: {{ $brand->color ?? '#276e44' }}; font-family: 'Source Serif Pro', Georgia, serif;">
                            {{ $brand->name }}
                        </h2>
                        @if($brand->description)
                            <p class="text-sm leading-relaxed mb-3" style="color: #60916a;">{{ $brand->description }}</p>
                        @endif
                        <span class="text-sm font-medium" style="color: {{ $brand->color ?? '#276e44' }};">
                            {{ $brand->products_count }} produit{{ $brand->products_count > 1 ? 's' : '' }} — Découvrir →
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

</x-layouts.app>
