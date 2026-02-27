<?php

namespace App\Console\Commands\Migrate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

abstract class WpImportCommand extends Command
{
    protected function wp(): \Illuminate\Database\Connection
    {
        return DB::connection('wp_legacy');
    }

    protected function postMeta(int $postId): array
    {
        return $this->wp()
            ->table('postmeta')
            ->where('post_id', $postId)
            ->pluck('meta_value', 'meta_key')
            ->all();
    }

    protected function getMeta(int $postId, string $key, mixed $default = null): mixed
    {
        $val = $this->wp()
            ->table('postmeta')
            ->where('post_id', $postId)
            ->where('meta_key', $key)
            ->value('meta_value');

        return $val ?? $default;
    }

    protected function attachmentUrl(int $attachId): ?string
    {
        if (! $attachId) return null;

        $file = $this->wp()
            ->table('postmeta')
            ->where('post_id', $attachId)
            ->where('meta_key', '_wp_attached_file')
            ->value('meta_value');

        return $file ? '/wp-content/uploads/' . $file : null;
    }

    /** Vide une table Laravel en désactivant temporairement les FK */
    protected function safeTruncate(string $table): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table($table)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function printResult(string $entity, int $created, int $skipped = 0): void
    {
        $this->info("  ✓ {$entity} : {$created} importés" . ($skipped ? ", {$skipped} ignorés" : ''));
    }
}
