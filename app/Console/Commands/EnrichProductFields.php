<?php

namespace App\Console\Commands;

use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class EnrichProductFields extends Command
{
    protected $signature = 'products:enrich
                            {--product=         : ID d\'un produit spécifique}
                            {--limit=0          : Nombre max de produits à traiter (0 = tous)}
                            {--exclude-category= : IDs de catégories à exclure, séparés par des virgules}
                            {--force            : Réécrire les champs déjà remplis}
                            {--dry-run          : Afficher le résultat sans sauvegarder}';

    protected $description = 'Enrichit les champs produit (bienfaits, utilisation, composition, conseil, contenu net) via Claude AI';

    private Client $http;

    public function handle(): int
    {
        $apiKey = env('ANTHROPIC_API_KEY');
        if (! $apiKey) {
            $this->error('ANTHROPIC_API_KEY manquant dans .env');

            return 1;
        }

        $this->http = new Client([
            'base_uri' => 'https://api.anthropic.com',
            'timeout' => 60,
        ]);

        $query = Product::whereNotNull('description')
            ->where('description', '!=', '');

        if ($this->option('product')) {
            $query->where('id', $this->option('product'));
        } else {
            if (! $this->option('force')) {
                // Par défaut, ignorer les produits déjà enrichis
                $query->where(function ($q) {
                    $q->whereNull('benefits')
                        ->orWhere('benefits', '');
                });
            }

            if ($this->option('exclude-category')) {
                $excludeIds = array_map('intval', explode(',', $this->option('exclude-category')));
                $query->whereNotIn('category_id', $excludeIds);
            }
        }

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get();

        if ($products->isEmpty()) {
            $this->info('Aucun produit à traiter.');

            return 0;
        }

        $this->info("Traitement de {$products->count()} produit(s)...\n");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $success = 0;
        $errors = 0;

        foreach ($products as $product) {
            $bar->advance();

            try {
                $result = $this->callClaude($product, $apiKey);

                if (! $result) {
                    $errors++;

                    continue;
                }

                if ($this->option('dry-run')) {
                    $this->newLine(2);
                    $this->line("─── <fg=cyan>{$product->name}</> (ID {$product->id}) ───");
                    foreach ($result as $key => $value) {
                        $this->line("<fg=yellow>{$key}:</> ".(strlen($value) > 200 ? substr(strip_tags($value), 0, 200).'…' : strip_tags($value)));
                    }
                } else {
                    if ($this->option('force')) {
                        $product->update($result);
                    } else {
                        // Ne remplir que les champs vides, SAUF description qu'on nettoie toujours
                        $toUpdate = array_filter($result, fn ($v) => ! empty($v));
                        $toUpdate = array_filter($toUpdate, fn ($k) => $k === 'description' || empty($product->{$k}), ARRAY_FILTER_USE_KEY);
                        if (! empty($toUpdate)) {
                            $product->update($toUpdate);
                        }
                    }
                }

                $success++;

            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->warn("Erreur pour «{$product->name}» : ".$e->getMessage());
            }

            // Pause pour respecter le rate-limit (3 req/s)
            usleep(400000);
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Terminé : <fg=green>{$success}</> succès, <fg=red>{$errors}</> erreurs.");

        return 0;
    }

    private function callClaude(Product $product, string $apiKey): ?array
    {
        $description = strip_tags($product->description ?? '');
        $shortDesc = strip_tags($product->short_description ?? '');
        $name = $product->name;
        $unitMeasure = $product->unit_measure ?? '';

        $prompt = <<<PROMPT
Tu es un expert en cosmétiques et soins beauté. Analyse la fiche produit suivante et extrais les informations structurées.

**Nom du produit :** {$name}
**Description courte :** {$shortDesc}
**Description complète :** {$description}
**Contenu net actuel :** {$unitMeasure}

Retourne UNIQUEMENT un objet JSON valide avec ces clés (en français, HTML simple autorisé avec <ul><li><strong><p> uniquement) :

{
  "team_recommendation": "1-2 phrases de conseil pratique de l'équipe (comment utiliser au mieux, astuce, avec quoi associer). Ton chaleureux et expert. Texte brut sans HTML.",
  "benefits": "<ul><li>Bienfait 1</li><li>Bienfait 2</li>...</ul> (liste des bienfaits/vertus du produit, 3-6 items)",
  "usage_instructions": "<p>Comment utiliser...</p><ul><li>Étape 1</li>...</ul> (mode d'emploi clair et pratique)",
  "composition": "Ingrédients principaux si mentionnés, sinon chaîne vide. Format : texte brut ou <ul><li> si liste.",
  "unit_measure": "Contenu net ex: '100 ml', '50 g', '30 ml'. Chaîne vide si non mentionné ou si déjà renseigné.",
  "description": "Réécriture de la description complète en supprimant les parties déjà couvertes par les champs bienfaits, utilisation et composition. Garde uniquement la présentation générale du produit, son histoire/positionnement, et les informations uniques non extraites ailleurs. Format HTML avec <p> et <strong>. Si la description ne contient rien de plus que ce qui a été extrait, retourne une chaîne vide."
}

Si une information n'est pas disponible dans la description, utilise une chaîne vide "".
Ne génère PAS d'informations inventées non présentes dans la description.
PROMPT;

        $response = $this->http->post('/v1/messages', [
            'headers' => [
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => [
                'model' => 'claude-sonnet-4-5-20250929',
                'max_tokens' => 2048,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        $text = $body['content'][0]['text'] ?? '';

        // Extraire le JSON de la réponse
        if (preg_match('/\{[\s\S]*\}/u', $text, $m)) {
            $data = json_decode($m[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return array_intersect_key($data, array_flip([
                    'team_recommendation', 'benefits', 'usage_instructions', 'composition', 'unit_measure', 'description',
                ]));
            }
        }

        return null;
    }
}
