<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;

class RestoreMediaFromLegacy extends Command
{
    protected $signature = 'media:restore-from-legacy {--dry-run} {--product=}';
    protected $description = 'Re-télécharge les images originales depuis le WP legacy et les reconvertit en WebP (source JPEG → WebP)';

    private const WP_BASE_URL = 'https://institutcorpsacoeur.fr';

    public function handle(): int
    {
        // Trouver les médias qui ont été importés depuis WP (on cherche l'URL originale dans wp_legacy)
        $query = Media::where('disk', 'public')
            ->where('mime_type', 'image/webp');

        if ($this->option('product')) {
            $productId = (int) $this->option('product');
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                $this->error("Produit #{$productId} introuvable.");
                return 1;
            }

            $mediaIds = collect([$product->featured_image_id])
                ->merge(json_decode($product->gallery_image_ids ?? '[]', true))
                ->filter()
                ->unique();

            $query->whereIn('id', $mediaIds);
        }

        $medias = $query->get();
        $this->info("Recherche des originaux WP pour {$medias->count()} images...\n");

        // Charger la map WP attachment metadata depuis la base legacy
        $wpAttachments = $this->getWpAttachments();

        $bar = $this->output->createProgressBar($medias->count());
        $bar->start();

        $restored = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($medias as $media) {
            $bar->advance();

            // Trouver l'original WP par le nom de fichier (sans extension webp)
            $baseName = pathinfo($media->filename, PATHINFO_FILENAME);
            // Retirer le suffixe ajouté par la conversion (ex: -scaled, UUID, etc.)
            $wpFile = $this->findWpOriginal($baseName, $media->original_filename, $wpAttachments);

            if (!$wpFile) {
                $skipped++;
                continue;
            }

            $wpUrl = self::WP_BASE_URL . '/wp-content/uploads/' . $wpFile;

            if ($this->option('dry-run')) {
                $this->newLine();
                $this->line("{$media->filename} ← {$wpUrl}");
                $restored++;
                continue;
            }

            try {
                $response = Http::timeout(30)->get($wpUrl);
                if (!$response->successful()) {
                    $this->newLine();
                    $this->warn("HTTP {$response->status()} pour {$wpUrl}");
                    $errors++;
                    continue;
                }

                $tmpFile = tempnam(sys_get_temp_dir(), 'wp_img_');
                file_put_contents($tmpFile, $response->body());

                $newFullPath = storage_path('app/public/' . $media->path);

                Image::make($tmpFile)
                    ->resize(900, 900, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('webp', 78)
                    ->save($newFullPath);

                @unlink($tmpFile);

                [$width, $height] = getimagesize($newFullPath) ?: [null, null];

                $media->update([
                    'size'   => filesize($newFullPath) ?: 0,
                    'width'  => $width,
                    'height' => $height,
                ]);

                $restored++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Erreur {$media->filename}: {$e->getMessage()}");
                $errors++;
            }
        }

        $bar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->info("Dry-run: {$restored} images à restaurer, {$skipped} sans original WP trouvé.");
        } else {
            $this->info("Terminé: {$restored} restaurées, {$skipped} sans original, {$errors} erreurs.");
        }

        return 0;
    }

    /**
     * Charge les fichiers attachés WP depuis la base legacy.
     */
    private function getWpAttachments(): array
    {
        try {
            $rows = DB::connection('wp_legacy')
                ->select("
                    SELECT pm.meta_value AS file_path
                    FROM mod352_postmeta pm
                    JOIN mod352_posts p ON p.ID = pm.post_id
                    WHERE pm.meta_key = '_wp_attached_file'
                    AND p.post_type = 'attachment'
                    AND p.post_status = 'inherit'
                ");

            return array_map(fn($r) => $r->file_path, $rows);
        } catch (\Exception $e) {
            $this->warn("Impossible de se connecter à wp_legacy: {$e->getMessage()}");
            $this->info("Tentative via les noms de fichiers originaux...");
            return [];
        }
    }

    /**
     * Cherche le fichier WP original correspondant à un media converti.
     */
    private function findWpOriginal(string $baseName, ?string $originalFilename, array $wpFiles): ?string
    {
        // Essayer avec le nom original d'abord
        if ($originalFilename) {
            $origBase = pathinfo($originalFilename, PATHINFO_FILENAME);
            foreach ($wpFiles as $wpFile) {
                $wpBase = pathinfo(basename($wpFile), PATHINFO_FILENAME);
                if ($wpBase === $origBase) {
                    return $wpFile;
                }
            }
        }

        // Essayer avec le basename du fichier webp
        foreach ($wpFiles as $wpFile) {
            $wpBase = pathinfo(basename($wpFile), PATHINFO_FILENAME);
            if ($wpBase === $baseName) {
                return $wpFile;
            }
        }

        return null;
    }
}
