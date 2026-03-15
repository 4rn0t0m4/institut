<?php

namespace App\Console\Commands;

use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GenerateProductReviews extends Command
{
    protected $signature = 'reviews:generate
                            {--limit=15         : Nombre de produits à traiter}
                            {--reviews-per=3    : Nombre d\'avis par produit (2-5)}
                            {--output=generated_reviews.sql : Fichier SQL de sortie}
                            {--dry-run          : Afficher sans générer le fichier}';

    protected $description = 'Génère des avis clients réalistes via Claude AI et exporte en SQL';

    private Client $http;

    private array $femaleNames = [
        'Marie', 'Sophie', 'Isabelle', 'Nathalie', 'Céline', 'Sandrine', 'Valérie',
        'Caroline', 'Stéphanie', 'Aurélie', 'Émilie', 'Julie', 'Camille', 'Laura',
        'Mathilde', 'Pauline', 'Charlotte', 'Manon', 'Léa', 'Chloé', 'Sarah',
        'Delphine', 'Virginie', 'Corinne', 'Patricia', 'Christelle', 'Laurence',
        'Amandine', 'Marion', 'Élodie', 'Mélanie', 'Alexandra', 'Lucie', 'Claire',
    ];

    private array $maleNames = [
        'Thomas', 'Nicolas', 'Julien', 'David', 'Sébastien', 'Laurent', 'Pierre',
        'Frédéric', 'Christophe', 'Alexandre', 'Antoine', 'Maxime', 'Guillaume',
    ];

    private array $lastInitials = [
        'B.', 'C.', 'D.', 'F.', 'G.', 'L.', 'M.', 'P.', 'R.', 'S.', 'T.', 'V.',
    ];

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

        $reviewsPer = max(2, min(5, (int) $this->option('reviews-per')));
        $limit = (int) $this->option('limit');

        $products = Product::where('is_active', true)
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->inRandomOrder()
            ->limit($limit)
            ->get(['id', 'name', 'short_description', 'description', 'price', 'sale_price']);

        if ($products->isEmpty()) {
            $this->info('Aucun produit trouvé.');

            return 0;
        }

        $this->info("Génération d'avis pour {$products->count()} produit(s) ({$reviewsPer} avis/produit)...\n");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $allReviews = [];
        $errors = 0;

        foreach ($products as $product) {
            $bar->advance();

            try {
                $reviews = $this->generateReviews($product, $reviewsPer, $apiKey);

                if (! $reviews) {
                    $errors++;

                    continue;
                }

                // Plus de prénoms masculins pour les coffrets (souvent offerts)
                $isCoffret = str_contains(strtolower($product->name), 'coffret');

                foreach ($reviews as $review) {
                    $useMale = $isCoffret ? (rand(1, 100) <= 40) : (rand(1, 100) <= 10);
                    $firstName = $useMale
                        ? $this->maleNames[array_rand($this->maleNames)]
                        : $this->femaleNames[array_rand($this->femaleNames)];
                    $name = $firstName.' '.$this->lastInitials[array_rand($this->lastInitials)];

                    $allReviews[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'author_name' => $name,
                        'author_email' => strtolower(str_replace([' ', '.', 'é', 'è', 'ë', 'ê', 'à', 'ô', 'î', 'ù', 'û', 'ï', 'ç'], ['', '', 'e', 'e', 'e', 'e', 'a', 'o', 'i', 'u', 'u', 'i', 'c'], $name)).rand(10, 99).'@example.fr',
                        'rating' => $review['rating'] ?? 5,
                        'title' => $review['title'] ?? '',
                        'body' => $review['body'] ?? '',
                    ];
                }
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->warn("Erreur pour «{$product->name}» : ".$e->getMessage());
            }

            usleep(400000);
        }

        $bar->finish();
        $this->newLine(2);

        if (empty($allReviews)) {
            $this->warn('Aucun avis généré.');

            return 1;
        }

        if ($this->option('dry-run')) {
            foreach ($allReviews as $r) {
                $this->line("─── <fg=cyan>{$r['product_name']}</> ───");
                $this->line("<fg=yellow>{$r['author_name']}</> — {'★' x {$r['rating']}} {$r['rating']}/5");
                $this->line("<fg=white>{$r['title']}</>");
                $this->line($r['body']);
                $this->newLine();
            }
        } else {
            $this->exportSql($allReviews);
        }

        $this->info('Terminé : '.count($allReviews)." avis générés, {$errors} erreurs.");

        return 0;
    }

    private function generateReviews(Product $product, int $count, string $apiKey): ?array
    {
        $description = strip_tags($product->short_description ?? $product->description ?? '');
        $name = $product->name;
        $price = $product->sale_price ?? $product->price;

        $prompt = <<<PROMPT
Tu es une cliente française qui a acheté des produits cosmétiques/soins en ligne. Génère {$count} avis clients réalistes et variés pour ce produit.

**Produit :** {$name}
**Prix :** {$price} €
**Description :** {$description}

Règles :
- Les avis doivent sembler authentiques (vraies clientes d'un institut de beauté)
- Varier les styles : certains courts et enthousiastes, d'autres plus détaillés
- Notes : uniquement 4 ou 5 sur 5. Majorité de 5/5.
- Avis 100% positifs. Ne mentionne AUCUN point négatif, bémol, réserve ou critique.
- Utiliser un langage naturel, pas marketing. Des phrases simples, du vécu.
- Les situations doivent être variées (peau sensible, peau mature, première commande, cliente fidèle, cadeau, etc.)

Retourne UNIQUEMENT un tableau JSON valide :
[
  {
    "rating": 5,
    "title": "Titre court de l'avis",
    "body": "Corps de l'avis, 2-4 phrases naturelles."
  }
]
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

        if (preg_match('/\[[\s\S]*\]/u', $text, $m)) {
            $data = json_decode($m[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $data;
            }
        }

        return null;
    }

    private function exportSql(array $reviews): void
    {
        $output = $this->option('output');
        $lines = [];
        $lines[] = '-- Avis clients générés — '.now()->toDateTimeString();
        $lines[] = '-- '.count($reviews).' avis';
        $lines[] = '';
        $lines[] = 'START TRANSACTION;';
        $lines[] = '';

        // Dates variées sur les 6 derniers mois
        $now = now();

        foreach ($reviews as $i => $review) {
            $daysAgo = rand(7, 180);
            $createdAt = $now->copy()->subDays($daysAgo)->format('Y-m-d H:i:s');

            $lines[] = sprintf(
                "INSERT INTO product_reviews (product_id, user_id, author_name, author_email, rating, title, body, is_verified_buyer, is_approved, created_at, updated_at) VALUES (%d, NULL, %s, %s, %d, %s, %s, 1, 1, '%s', '%s');",
                $review['product_id'],
                $this->quote($review['author_name']),
                $this->quote($review['author_email']),
                $review['rating'],
                $this->quote($review['title']),
                $this->quote($review['body']),
                $createdAt,
                $createdAt
            );
        }

        $lines[] = '';
        $lines[] = 'COMMIT;';

        file_put_contents($output, implode("\n", $lines));

        $this->info("Fichier généré : {$output}");
        $this->line('Appliquer en prod :');
        $this->line("  ssh instiqh@ssh.cluster130.hosting.ovh.net \"mysql -h instiqhapp.mysql.db -u instiqhapp -p'MOT_DE_PASSE' instiqhapp\" < {$output}");
    }

    private function quote(?string $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return "'".addslashes($value)."'";
    }
}
