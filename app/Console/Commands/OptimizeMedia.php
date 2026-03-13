<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;

class OptimizeMedia extends Command
{
    protected $signature = 'media:optimize {--dry-run} {--reprocess}';
    protected $description = 'Convertit les images locales en WebP et les redimensionne (max 900px, qualité 78)';

    public function handle(): int
    {
        $query = Media::where('disk', 'public')
            ->where('mime_type', 'like', 'image/%');

        if (!$this->option('reprocess')) {
            $query->where('mime_type', '!=', 'image/webp');
        }

        $medias = $query->get();

        $this->info("Traitement de {$medias->count()} images...\n");
        $bar = $this->output->createProgressBar($medias->count());
        $bar->start();

        $converted = 0;
        $skipped   = 0;
        $errors    = 0;

        foreach ($medias as $media) {
            $sourcePath = storage_path('app/public/' . $media->path);

            if (!file_exists($sourcePath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $newFilename = pathinfo($media->filename, PATHINFO_FILENAME) . '.webp';
                $newPath     = 'media/' . $newFilename;
                $newFullPath = storage_path('app/public/' . $newPath);
                $sameFile    = realpath($sourcePath) === realpath($newFullPath) || $sourcePath === $newFullPath;

                if ($this->option('dry-run')) {
                    $this->newLine();
                    $this->line("{$media->filename} ({$this->formatBytes(filesize($sourcePath))}) → {$newFilename}");
                } else {
                    Image::make($sourcePath)
                        ->resize(900, 900, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('webp', 78)
                        ->save($newFullPath);

                    [$width, $height] = getimagesize($newFullPath) ?: [null, null];

                    $media->update([
                        'filename'  => $newFilename,
                        'path'      => $newPath,
                        'url'       => '/storage/' . $newPath,
                        'mime_type' => 'image/webp',
                        'size'      => filesize($newFullPath) ?: 0,
                        'width'     => $width,
                        'height'    => $height,
                    ]);

                    if (!$sameFile) {
                        @unlink($sourcePath);
                    }
                }

                $converted++;
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->warn("Erreur {$media->filename} : " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->info("Dry-run : {$converted} images seraient converties, {$skipped} fichiers introuvables.");
        } else {
            $this->info("Terminé : {$converted} converties, {$skipped} introuvables, {$errors} erreurs.");
        }

        return 0;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' Mo';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' Ko';
        return $bytes . ' B';
    }
}
