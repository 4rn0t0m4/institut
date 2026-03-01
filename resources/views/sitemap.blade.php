<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Accueil --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Boutique --}}
    <url>
        <loc>{{ route('shop.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Blog --}}
    <url>
        <loc>{{ route('blog.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    {{-- Pages --}}
    @foreach($pages as $page)
    <url>
        <loc>{{ url($page->parent ? $page->parent->slug . '/' . $page->slug : $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at->toW3cString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    {{-- Produits --}}
    @foreach($products as $product)
    <url>
        <loc>{{ route('shop.show', $product->slug) }}</loc>
        <lastmod>{{ $product->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Articles blog --}}
    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.show', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->toW3cString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
