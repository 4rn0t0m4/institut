<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

class CleanPageContent extends Command
{
    protected $signature = 'pages:clean {--dry-run : Preview changes without saving}';

    protected $description = 'Clean page content: fix broken image URLs (.jpg/.png → .webp) and remove WordPress block comments';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $pages = Page::all();
        $totalImages = 0;
        $totalWpBlocks = 0;

        foreach ($pages as $page) {
            $original = $page->content;
            $content = $original;

            // 1. Fix image extensions: .jpg/.jpeg/.png → .webp where webp exists
            $content = preg_replace_callback(
                '/(?<=src=["\'])([^"\']+)\.(jpg|jpeg|png)(?=["\'])/',
                function ($matches) use (&$totalImages) {
                    $path = public_path(ltrim($matches[1].'.'.$matches[2], '/'));
                    $webpPath = public_path(ltrim($matches[1].'.webp', '/'));

                    if (! file_exists($path) && file_exists($webpPath)) {
                        $totalImages++;

                        return $matches[1].'.webp';
                    }

                    return $matches[0];
                },
                $content
            );

            // 2. Remove WordPress block comments (<!-- wp:xxx --> and <!-- /wp:xxx -->)
            $before = $content;
            $content = preg_replace('/<!--\s*\/?wp:[^>]*-->\s*/', '', $content);
            if ($content !== $before) {
                $totalWpBlocks++;
            }

            // 3. Remove wp-block-* CSS classes
            $content = preg_replace('/\s*class="wp-block-[^"]*"/', '', $content);

            // 4. Clean up excessive whitespace left behind
            $content = preg_replace('/\n{3,}/', "\n\n", $content);
            $content = trim($content);

            if ($content !== $original) {
                if ($dryRun) {
                    $this->info("Would update: {$page->slug}");
                } else {
                    $page->content = $content;
                    $page->save();
                    $this->info("Updated: {$page->slug}");
                }
            }
        }

        $this->newLine();
        $this->info("Images fixed: {$totalImages}");
        $this->info("Pages with WP blocks cleaned: {$totalWpBlocks}");

        if ($dryRun) {
            $this->warn('Dry run — no changes saved. Run without --dry-run to apply.');
        }

        return Command::SUCCESS;
    }
}
