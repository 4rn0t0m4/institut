<?php

namespace App\Console\Commands\Migrate;

use App\Models\ProductCategory;
use Illuminate\Support\Str;

class WpCategories extends WpImportCommand
{
    
    protected $signature   = 'migrate:wp-categories';
    protected $description = 'Importe les catégories de produits depuis WordPress';

    public function handle(): void
    {
        $this->info('Import catégories produits...');

        $this->safeTruncate('product_categories');

        $rows = $this->wp()
            ->table('terms as t')
            ->join('term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'product_cat')
            ->select('t.term_id', 't.name', 't.slug', 'tt.parent', 'tt.description')
            ->orderBy('tt.parent')
            ->get();

        // 1er pass : créer toutes les catégories sans parent
        $idMap = []; // wp_term_id => laravel_id

        foreach ($rows as $row) {
            $cat = ProductCategory::create([
                'name'        => $row->name,
                'slug'        => Str::slug($row->slug) ?: Str::slug($row->name),
                'description' => $row->description ?: null,
                'sort_order'  => 0,
            ]);
            $idMap[$row->term_id] = $cat->id;
        }

        // 2e pass : relier les parents
        foreach ($rows as $row) {
            if ($row->parent && isset($idMap[$row->parent])) {
                ProductCategory::where('id', $idMap[$row->term_id])
                    ->update(['parent_id' => $idMap[$row->parent]]);
            }
        }

        $this->printResult('Catégories', count($rows));
        $this->line('  ID map sauvegardée en cache (fichier temporaire)');

        // Sauvegarder la map pour les autres commandes
        file_put_contents(storage_path('wp_category_map.json'), json_encode($idMap));
    }
}
