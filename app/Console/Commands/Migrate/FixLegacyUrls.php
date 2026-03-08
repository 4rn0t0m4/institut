<?php

namespace App\Console\Commands\Migrate;

use App\Models\Page;
use Illuminate\Console\Command;

class FixLegacyUrls extends Command
{
    protected $signature = 'migrate:fix-legacy-urls';
    protected $description = 'Remplace les URLs WordPress et embeds YouTube dans le contenu des pages';

    public function handle(): void
    {
        $localFiles = collect(scandir(storage_path('app/public/media')))
            ->filter(fn ($f) => $f !== '.' && $f !== '..');

        $updated = 0;

        Page::where('content', 'like', '%wp-content/uploads%')
            ->orWhere('content', 'like', '%institutcorpsacoeur.fr/wp-content%')
            ->orWhere('content', 'like', '%wp:embed%')
            ->get()
            ->each(function ($page) use ($localFiles, &$updated) {
                $content = $page->content;
                $original = $content;

                // 1. Remplacer les URLs d'images WordPress
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

                // 2. Convertir les blocs wp:embed YouTube en iframes
                $content = $this->convertYouTubeEmbeds($content);

                if ($content !== $original) {
                    $page->content = $content;
                    $page->save();
                    $updated++;
                    $this->info("  ✓ {$page->slug}");
                }
            });

        $this->info("Terminé — {$updated} pages mises à jour.");
    }

    private function convertYouTubeEmbeds(string $content): string
    {
        // Remplace <!-- wp:embed {youtube...} --> ... <!-- /wp:embed --> par un iframe responsive
        return preg_replace_callback(
            '#<!-- wp:embed \{[^}]*youtube[^}]*\} -->\s*.*?\s*<!-- /wp:embed -->#s',
            function ($match) {
                // Extraire l'URL YouTube du bloc
                if (!preg_match('#https?://(?:www\.)?(?:youtube\.com/(?:watch\?v=|shorts/)|youtu\.be/)([a-zA-Z0-9_-]+)#', $match[0], $urlMatch)) {
                    return $match[0]; // Pas d'URL trouvée, on ne touche pas
                }

                $videoId = $urlMatch[1];

                return '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px;margin:1rem 0">'
                    . '<iframe src="https://www.youtube-nocookie.com/embed/' . $videoId . '" '
                    . 'style="position:absolute;top:0;left:0;width:100%;height:100%" '
                    . 'frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" '
                    . 'allowfullscreen loading="lazy"></iframe>'
                    . '</div>';
            },
            $content
        );
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
