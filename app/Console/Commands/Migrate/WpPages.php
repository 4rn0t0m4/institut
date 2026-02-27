<?php

namespace App\Console\Commands\Migrate;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class WpPages extends WpImportCommand
{
    protected $signature   = 'migrate:wp-pages';
    protected $description = 'Importe les pages et articles de blog depuis WordPress';

    public function handle(): void
    {
        $this->importPages();
        $this->importPosts();
    }

    private function importPages(): void
    {
        $this->info('Import pages statiques...');
        $this->safeTruncate('pages');

        $wpPages = $this->wp()
            ->table('posts')
            ->where('post_type', 'page')
            ->whereNotIn('post_status', ['trash', 'auto-draft'])
            ->orderBy('menu_order')
            ->get();

        $idMap   = [];
        $created = 0;

        // 1er pass : créer sans parent
        foreach ($wpPages as $p) {
            $meta = $this->postMeta($p->ID);
            $slug = Str::slug($p->post_name ?: $p->post_title);

            $finalSlug = $slug ?: 'page-' . $p->ID;
            $i = 1;
            while (Page::where('slug', $finalSlug)->exists()) {
                $finalSlug = $slug . '-' . $i++;
            }

            $page = Page::create([
                'title'        => $p->post_title ?: '(sans titre)',
                'slug'         => $finalSlug,
                'content'      => $p->post_content ?: null,
                'status'       => $p->post_status === 'publish' ? 'published' : 'draft',
                'meta_title'   => $meta['_yoast_wpseo_title'] ?? null,
                'meta_description' => $meta['_yoast_wpseo_metadesc'] ?? null,
                'sort_order'   => $p->menu_order,
                'published_at' => $p->post_date !== '0000-00-00 00:00:00' ? $p->post_date : null,
            ]);

            $idMap[$p->ID] = ['laravel_id' => $page->id, 'wp_parent' => (int) $p->post_parent];
            $created++;
        }

        // 2e pass : relier les parents
        foreach ($idMap as $wpId => $data) {
            if ($data['wp_parent'] && isset($idMap[$data['wp_parent']])) {
                Page::where('id', $data['laravel_id'])
                    ->update(['parent_id' => $idMap[$data['wp_parent']]['laravel_id']]);
            }
        }

        $this->printResult('Pages', $created);
    }

    private function importPosts(): void
    {
        $this->info('Import articles de blog...');
        $this->safeTruncate('posts');

        $userMap = [];
        $wpPosts = $this->wp()
            ->table('posts')
            ->where('post_type', 'post')
            ->where('post_status', 'publish')
            ->orderBy('post_date', 'desc')
            ->get();

        $created = 0;

        foreach ($wpPosts as $p) {
            $meta = $this->postMeta($p->ID);

            // Catégories blog
            $categories = $this->wp()
                ->table('term_relationships as tr')
                ->join('term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                ->join('terms as t', 'tt.term_id', '=', 't.term_id')
                ->where('tr.object_id', $p->ID)
                ->where('tt.taxonomy', 'category')
                ->pluck('t.slug')
                ->all();

            $tags = $this->wp()
                ->table('term_relationships as tr')
                ->join('term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                ->join('terms as t', 'tt.term_id', '=', 't.term_id')
                ->where('tr.object_id', $p->ID)
                ->where('tt.taxonomy', 'post_tag')
                ->pluck('t.slug')
                ->all();

            $slug = Str::slug($p->post_name ?: $p->post_title);
            $finalSlug = $slug ?: 'article-' . $p->ID;
            $i = 1;
            while (Post::where('slug', $finalSlug)->exists()) {
                $finalSlug = $slug . '-' . $i++;
            }

            Post::create([
                'user_id'          => null,
                'title'            => $p->post_title,
                'slug'             => $finalSlug,
                'excerpt'          => strip_tags($p->post_excerpt) ?: null,
                'content'          => $p->post_content ?: null,
                'status'           => 'published',
                'meta_title'       => $meta['_yoast_wpseo_title'] ?? null,
                'meta_description' => $meta['_yoast_wpseo_metadesc'] ?? null,
                'categories'       => $categories ?: null,
                'tags'             => $tags ?: null,
                'published_at'     => $p->post_date,
                'created_at'       => $p->post_date,
            ]);

            $created++;
        }

        $this->printResult('Articles', $created);
    }
}
