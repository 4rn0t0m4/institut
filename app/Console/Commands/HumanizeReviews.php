<?php

namespace App\Console\Commands;

use App\Models\ProductReview;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class HumanizeReviews extends Command
{
    protected $signature = 'reviews:humanize {--dry-run}';
    protected $description = 'Rend les avis produits plus humains via Claude AI';

    public function handle(): int
    {
        $apiKey = env('ANTHROPIC_API_KEY');
        if (!$apiKey) {
            $this->error('ANTHROPIC_API_KEY manquant');
            return 1;
        }

        $http = new Client(['base_uri' => 'https://api.anthropic.com', 'timeout' => 60]);
        $reviews = ProductReview::all();

        $this->info("Traitement de {$reviews->count()} avis...\n");
        $bar = $this->output->createProgressBar($reviews->count());
        $bar->start();

        // Traiter par lots de 10
        foreach ($reviews->chunk(10) as $chunk) {
            $reviewsData = $chunk->map(fn($r) => [
                'id' => $r->id,
                'author_name' => $r->author_name,
                'rating' => $r->rating,
                'title' => $r->title,
                'body' => $r->body,
                'is_verified_buyer' => $r->is_verified_buyer,
            ])->toArray();

            $prompt = <<<PROMPT
Tu es chargé de réécrire des avis clients e-commerce pour les rendre plus authentiques et humains.

Voici les avis à réécrire en JSON :
{$this->jsonEncode($reviewsData)}

Règles IMPORTANTES :
- IMPORTANT : varie fortement les longueurs. Environ 20% des avis doivent être TRÈS courts : juste "Parfait !", "J'en suis ravie", "Très bon produit", "Top, je recommande", "Nickel" — une seule expression ou phrase courte sans développement. Les autres peuvent être moyens ou développés, mais ne dépasse pas la longueur originale
- Ajoute des fautes de frappe naturelles (oublier une lettre, répétition, manque d'accent, etc.) sur ~30% des avis
- Certains auteurs utilisent un pseudo au lieu d'un prénom+initiale (ex: "Marie_33", "beauté34", "mamande2", "ptite_coquette") — varie avec les vrais prénoms aussi
- Quelques avis sans majuscule au début ou sans ponctuation finale
- Certains avis sans titre (retourne titre vide "")
- Environ 30% des avis doivent avoir is_verified_buyer = 0
- Le contenu doit rester cohérent avec le rating (5 étoiles = positif, 4 = légèrement moins enthousiaste)
- Ne change PAS les IDs, product_id, user_id, dates
- Garde les emails tels quels

Retourne UNIQUEMENT un JSON valide (tableau) avec exactement ces clés pour chaque avis :
id, author_name, author_email, rating, title, body, is_verified_buyer
PROMPT;

            try {
                $response = $http->post('/v1/messages', [
                    'headers' => [
                        'x-api-key'         => $apiKey,
                        'anthropic-version' => '2023-06-01',
                        'content-type'      => 'application/json',
                    ],
                    'json' => [
                        'model'      => 'claude-sonnet-4-6',
                        'max_tokens' => 4096,
                        'messages'   => [['role' => 'user', 'content' => $prompt]],
                    ],
                ]);

                $body = json_decode($response->getBody()->getContents(), true);
                $text = $body['content'][0]['text'] ?? '';

                if (preg_match('/\[[\s\S]*\]/u', $text, $m)) {
                    $data = json_decode($m[0], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                        foreach ($data as $item) {
                            if ($this->option('dry-run')) {
                                $this->newLine();
                                $this->line("[{$item['id']}] <fg=cyan>{$item['author_name']}</> ({$item['rating']}★) — {$item['title']}");
                                $this->line("  " . substr($item['body'], 0, 120) . (strlen($item['body']) > 120 ? '…' : ''));
                            } else {
                                ProductReview::where('id', $item['id'])->update([
                                    'author_name'       => $item['author_name'],
                                    'title'             => $item['title'] ?? '',
                                    'body'              => $item['body'],
                                    'is_verified_buyer' => $item['is_verified_buyer'],
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Erreur lot : " . $e->getMessage());
            }

            $bar->advance($chunk->count());
            usleep(500000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info($this->option('dry-run') ? 'Dry-run terminé.' : 'Avis mis à jour.');

        return 0;
    }

    private function jsonEncode(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
