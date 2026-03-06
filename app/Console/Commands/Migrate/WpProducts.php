<?php

namespace App\Console\Commands\Migrate;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class WpProducts extends WpImportCommand
{
    protected $signature   = 'migrate:wp-products';
    protected $description = 'Importe les produits WooCommerce depuis WordPress';

    public function handle(): void
    {
        $this->info('Import produits...');

        // Charger la map des catégories (créée par migrate:wp-categories)
        $catMapFile = storage_path('wp_category_map.json');
        $categoryMap = file_exists($catMapFile) ? json_decode(file_get_contents($catMapFile), true) : [];

        // Map term_id => laravel category_id par slug
        $wpCatToLaravel = [];
        foreach ($categoryMap as $wpTermId => $laravelId) {
            $wpCatToLaravel[$wpTermId] = $laravelId;
        }

        $this->safeTruncate('products');

        $products = $this->wp()
            ->table('posts')
            ->where('post_type', 'product')
            ->where('post_status', 'publish')
            ->select('ID', 'post_title', 'post_name', 'post_content', 'post_excerpt')
            ->orderBy('ID')
            ->get();

        $created = 0;

        foreach ($products as $p) {
            $meta = $this->postMeta($p->ID);

            // Catégorie principale
            $catId = $this->getProductCategoryId($p->ID, $wpCatToLaravel);

            // Image principale
            $thumbnailId  = (int) ($meta['_thumbnail_id'] ?? 0);
            $featuredPath = $thumbnailId ? $this->attachmentUrl($thumbnailId) : null;

            // Galerie
            $galleryIds = [];
            if (!empty($meta['_product_image_gallery'])) {
                $galleryIds = array_filter(explode(',', $meta['_product_image_gallery']));
            }

            $slug = Str::slug($p->post_name ?: $p->post_title);

            // Dédoublonner les slugs
            $finalSlug = $slug;
            $i = 1;
            while (Product::where('slug', $finalSlug)->exists()) {
                $finalSlug = $slug . '-' . $i++;
            }

            Product::create([
                'category_id'       => $catId,
                'name'              => $p->post_title,
                'slug'              => $finalSlug,
                'short_description' => strip_tags($p->post_excerpt) ?: null,
                'description'       => $p->post_content ?: null,
                'price'             => (float) ($meta['_regular_price'] ?? $meta['_price'] ?? 0),
                'sale_price'        => !empty($meta['_sale_price']) ? (float) $meta['_sale_price'] : null,
                'sku'               => $meta['_sku'] ?? null,
                'stock_quantity'    => isset($meta['_stock']) ? (int) $meta['_stock'] : null,
                'manage_stock'      => ($meta['_manage_stock'] ?? 'no') === 'yes',
                'stock_status'      => $meta['_stock_status'] ?? 'instock',
                'weight'            => !empty($meta['_weight']) ? (float) $meta['_weight'] : null,
                'is_virtual'        => ($meta['_virtual'] ?? 'no') === 'yes',
                'is_downloadable'   => ($meta['_downloadable'] ?? 'no') === 'yes',
                'is_active'         => true,
                'featured_image_id' => null, // on lie via Media après
                'gallery_image_ids' => $galleryIds ?: null,
            ]);

            $created++;
        }

        $this->printResult('Produits', $created);

        // Sauvegarder la map WP post_id => Laravel product id
        $productMap = [];
        foreach ($products as $p) {
            $product = Product::where('slug', Str::slug($p->post_name ?: $p->post_title))->first();
            if ($product) $productMap[$p->ID] = $product->id;
        }
        file_put_contents(storage_path('wp_product_map.json'), json_encode($productMap));
    }

    private function getProductCategoryId(int $postId, array $wpCatToLaravel): ?int
    {
        $termIds = $this->wp()
            ->table('term_relationships as tr')
            ->join('term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
            ->where('tr.object_id', $postId)
            ->where('tt.taxonomy', 'product_cat')
            ->pluck('tt.term_id')
            ->all();

        // Privilégier la sous-catégorie (enfant) plutôt que la catégorie parente
        $childId = null;
        $parentId = null;

        foreach ($termIds as $termId) {
            if (isset($wpCatToLaravel[$termId])) {
                $cat = ProductCategory::find($wpCatToLaravel[$termId]);
                if ($cat && $cat->parent_id) {
                    $childId = $cat->id;
                } elseif ($cat) {
                    $parentId = $cat->id;
                }
            }
        }

        return $childId ?? $parentId;
    }
}
