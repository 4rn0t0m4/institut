<?php

namespace App\Console\Commands\Migrate;

use App\Models\Page;
use Illuminate\Console\Command;

class FixLegacyUrls extends Command
{
    protected $signature = 'migrate:fix-legacy-urls';
    protected $description = 'Remplace les URLs WordPress par les chemins locaux dans le contenu des pages';

    public function handle(): void
    {
        $localFiles = collect(scandir(storage_path('app/public/media')))
            ->filter(fn ($f) => $f !== '.' && $f !== '..');

        $updated = 0;

        Page::where('content', 'like', '%wp-content/uploads%')
            ->orWhere('content', 'like', '%institutcorpsacoeur.fr/wp-content%')
            ->get()
            ->each(function ($page) use ($localFiles, &$updated) {
                $content = $page->content;
                $original = $content;

                preg_match_all(
                    '#https?://institutcorpsacoeur\.fr/wp-content/uploads/[^"\s<>)]+#',
                    $content,
                    $matches
                );

                foreach (array_unique($matches[0]) as $url) {
                    $local = $this->resolveLocal($url, $localFiles);
                    if ($local) {
                        $content = str_replace($url, $local, $content);
                    } else {
                        $this->warn("  Pas de fichier local pour: {$url}");
                    }
                }

                if ($content !== $original) {
                    $page->content = $content;
                    $page->save();
                    $updated++;
                    $this->info("  ✓ {$page->slug}");
                }
            });

        $this->info("Terminé — {$updated} pages mises à jour.");
    }

    private function resolveLocal(string $url, $localFiles): ?string
    {
        $fn = basename($url);

        // Exact match
        if ($localFiles->contains($fn)) {
            return '/storage/media/' . $fn;
        }

        // Strip WP dimension suffix: name-NNNxNNN.ext → name.ext
        $stripped = preg_replace('/-\d+x\d+(\.\w+)$/', '$1', $fn);
        if ($localFiles->contains($stripped)) {
            return '/storage/media/' . $stripped;
        }

        // Fuzzy: find any local file starting with the same base name
        $base = pathinfo($stripped, PATHINFO_FILENAME);
        $candidates = $localFiles->filter(fn ($f) => str_starts_with($f, $base));
        if ($candidates->isNotEmpty()) {
            return '/storage/media/' . $candidates->first();
        }

        return null;
    }
}
