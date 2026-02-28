<?php

namespace App\Console\Commands\Migrate;

use App\Models\Media;
use App\Models\Product;

class WpMedia extends WpImportCommand
{
    protected $signature   = 'migrate:wp-media';
    protected $description = 'Importe les médias WP et relie les images aux produits';

    private const WP_BASE_URL = 'https://institutcorpsacoeur.fr';

    public function handle(): void
    {
        $this->info('Import médias WordPress...');

        // 1. Importer tous les attachements WP dans la table media
        $this->importAttachments();

        // 2. Lier les images aux produits
        $this->linkProductImages();

        // 3. Convertir les IDs galerie WP en IDs Media Laravel
        $this->linkGalleryImages();

        $this->info('Terminé.');
    }

    private function importAttachments(): void
    {
        $this->safeTruncate('media');

        $attachments = $this->wp()
            ->select("
                SELECT p.ID, p.post_title, p.post_mime_type, p.guid,
                       pm_file.meta_value AS file_path,
                       pm_alt.meta_value  AS alt_text,
                       pm_meta.meta_value AS attachment_meta
                FROM mod352_posts p
                LEFT JOIN mod352_postmeta pm_file
                    ON pm_file.post_id = p.ID AND pm_file.meta_key = '_wp_attached_file'
                LEFT JOIN mod352_postmeta pm_alt
                    ON pm_alt.post_id = p.ID AND pm_alt.meta_key = '_wp_attachment_image_alt'
                LEFT JOIN mod352_postmeta pm_meta
                    ON pm_meta.post_id = p.ID AND pm_meta.meta_key = '_wp_attachment_metadata'
                WHERE p.post_type = 'attachment'
                AND p.post_status = 'inherit'
                ORDER BY p.ID
            ");

        $created   = 0;
        $mediaMap  = []; // WP attachment ID => Laravel Media ID

        foreach ($attachments as $att) {
            if (empty($att->file_path)) {
                continue;
            }

            $filename = basename($att->file_path);
            $path     = 'wp-content/uploads/' . $att->file_path;
            $url      = self::WP_BASE_URL . '/wp-content/uploads/' . $att->file_path;

            // Dimensions via les métadonnées sérialisées
            $width = $height = null;
            if ($att->attachment_meta) {
                $meta = @unserialize($att->attachment_meta);
                if (is_array($meta)) {
                    $width  = $meta['width']  ?? null;
                    $height = $meta['height'] ?? null;
                }
            }

            $media = Media::create([
                'filename'          => $filename,
                'original_filename' => $filename,
                'disk'              => 'external',
                'path'              => $path,
                'url'               => $url,
                'mime_type'         => $att->post_mime_type ?: 'image/jpeg',
                'size'              => 0,
                'width'             => $width,
                'height'            => $height,
                'alt'               => $att->alt_text ?: $att->post_title,
                'title'             => $att->post_title,
            ]);

            $mediaMap[$att->ID] = $media->id;
            $created++;
        }

        // Sauvegarder la map WP attachment ID => Laravel Media ID
        file_put_contents(storage_path('wp_media_map.json'), json_encode($mediaMap));

        $this->printResult('Médias', $created);
    }

    private function linkGalleryImages(): void
    {
        $this->info('  Conversion IDs galerie → Media...');

        $mediaMap   = json_decode(file_get_contents(storage_path('wp_media_map.json')), true);
        $productMap = [];
        $mapFile    = storage_path('wp_product_map.json');
        if (file_exists($mapFile)) {
            $productMap = json_decode(file_get_contents($mapFile), true);
        }

        $linked = 0;

        foreach ($productMap as $wpPostId => $laravelProductId) {
            $galleryRaw = $this->getMeta($wpPostId, '_product_image_gallery');
            if (empty($galleryRaw)) {
                continue;
            }

            $wpIds     = array_filter(explode(',', $galleryRaw));
            $mediaIds  = [];

            foreach ($wpIds as $wpId) {
                $wpId = (int) trim($wpId);
                if (isset($mediaMap[$wpId])) {
                    $mediaIds[] = $mediaMap[$wpId];
                }
            }

            if (!empty($mediaIds)) {
                Product::where('id', $laravelProductId)->update([
                    'gallery_image_ids' => json_encode($mediaIds),
                ]);
                $linked++;
            }
        }

        $this->info("  ✓ {$linked} galeries converties");
    }

    private function linkProductImages(): void
    {
        $this->info('  Liaison images → produits...');

        $mediaMap = json_decode(file_get_contents(storage_path('wp_media_map.json')), true);
        $productMap = [];
        $mapFile = storage_path('wp_product_map.json');
        if (file_exists($mapFile)) {
            $productMap = json_decode(file_get_contents($mapFile), true);
        }

        if (empty($productMap)) {
            $this->warn('  Aucune map produits trouvée. Lancez d\'abord migrate:wp-products.');
            return;
        }

        $linked = 0;

        // Pour chaque produit WP, lire _thumbnail_id et lier
        foreach ($productMap as $wpPostId => $laravelProductId) {
            $thumbnailId = (int) $this->getMeta($wpPostId, '_thumbnail_id');

            if ($thumbnailId && isset($mediaMap[$thumbnailId])) {
                Product::where('id', $laravelProductId)->update([
                    'featured_image_id' => $mediaMap[$thumbnailId],
                ]);
                $linked++;
            }
        }

        $this->info("  ✓ {$linked} produits liés à leur image");
    }
}
