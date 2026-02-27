<?php

namespace App\Console\Commands\Migrate;

use App\Models\Menu;
use App\Models\MenuItem;

class WpMenus extends WpImportCommand
{
    protected $signature   = 'migrate:wp-menus';
    protected $description = 'Importe la structure de navigation depuis WordPress';

    public function handle(): void
    {
        $this->info('Import menus...');

        MenuItem::query()->delete();
        $this->safeTruncate('menus');

        // Récupérer tous les menus nav WordPress
        $wpMenus = $this->wp()
            ->table('terms as t')
            ->join('term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'nav_menu')
            ->select('t.term_id', 't.name', 't.slug')
            ->get();

        foreach ($wpMenus as $wm) {
            $menu = Menu::create([
                'name'     => $wm->name,
                'location' => $wm->slug === 'menu-principal' ? 'primary' : $wm->slug,
            ]);

            $this->importMenuItems($wm->term_id, $menu->id);
            $this->info("  ✓ Menu « {$wm->name} » importé");
        }

        $this->printResult('Menus', count($wpMenus));
    }

    private function importMenuItems(int $wpMenuTermId, int $laravelMenuId): void
    {
        // Récupérer tous les items du menu
        $wpItems = $this->wp()
            ->table('term_relationships as tr')
            ->join('posts as p', 'tr.object_id', '=', 'p.ID')
            ->where('tr.term_taxonomy_id', function ($q) use ($wpMenuTermId) {
                $q->select('term_taxonomy_id')
                  ->from('term_taxonomy')
                  ->where('term_id', $wpMenuTermId)
                  ->where('taxonomy', 'nav_menu');
            })
            ->where('p.post_type', 'nav_menu_item')
            ->where('p.post_status', 'publish')
            ->select('p.ID', 'p.post_title', 'p.menu_order')
            ->orderBy('p.menu_order')
            ->get();

        $wpIdToLaravel = [];

        // 1er pass : créer les items sans parent
        foreach ($wpItems as $item) {
            $meta = $this->wp()
                ->table('postmeta')
                ->where('post_id', $item->ID)
                ->whereIn('meta_key', [
                    '_menu_item_url',
                    '_menu_item_menu_item_parent',
                    '_menu_item_object',
                    '_menu_item_object_id',
                    '_menu_item_type',
                    '_menu_item_target',
                ])
                ->pluck('meta_value', 'meta_key')
                ->all();

            // Résoudre l'URL
            $url = $this->resolveUrl($meta);

            // Libellé : post_title ou nom de l'objet lié
            $label = $item->post_title ?: $this->resolveLabel($meta);

            $menuItem = MenuItem::create([
                'menu_id'    => $laravelMenuId,
                'parent_id'  => null,
                'label'      => $label ?: 'Lien',
                'url'        => $url,
                'target'     => $meta['_menu_item_target'] ?? '_self',
                'is_mega'    => false,
                'sort_order' => $item->menu_order,
            ]);

            $wpIdToLaravel[$item->ID] = [
                'laravel_id' => $menuItem->id,
                'wp_parent'  => (int) ($meta['_menu_item_menu_item_parent'] ?? 0),
            ];
        }

        // 2e pass : relier les parents
        foreach ($wpIdToLaravel as $wpId => $data) {
            if ($data['wp_parent'] && isset($wpIdToLaravel[$data['wp_parent']])) {
                MenuItem::where('id', $data['laravel_id'])
                    ->update(['parent_id' => $wpIdToLaravel[$data['wp_parent']]['laravel_id']]);
            }
        }
    }

    private function resolveUrl(array $meta): string
    {
        if (!empty($meta['_menu_item_url'])) {
            return $meta['_menu_item_url'];
        }

        $type     = $meta['_menu_item_type'] ?? '';
        $object   = $meta['_menu_item_object'] ?? '';
        $objectId = (int) ($meta['_menu_item_object_id'] ?? 0);

        if ($type === 'post_type' && $objectId) {
            $post = $this->wp()->table('posts')->where('ID', $objectId)->first();
            return $post ? '/' . $post->post_name : '#';
        }

        if ($type === 'taxonomy' && $objectId) {
            $term = $this->wp()->table('terms')->where('term_id', $objectId)->first();
            return $term ? '/' . $object . '/' . $term->slug : '#';
        }

        return '#';
    }

    private function resolveLabel(array $meta): string
    {
        $objectId = (int) ($meta['_menu_item_object_id'] ?? 0);
        if (!$objectId) return '';

        $type = $meta['_menu_item_type'] ?? '';

        if ($type === 'post_type') {
            return $this->wp()->table('posts')->where('ID', $objectId)->value('post_title') ?? '';
        }

        if ($type === 'taxonomy') {
            return $this->wp()->table('terms')->where('term_id', $objectId)->value('name') ?? '';
        }

        return '';
    }
}
