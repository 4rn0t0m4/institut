<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Crée les souscriptions webhook sur l'API Boxtal v3.
 *
 * Usage :
 *   php artisan boxtal:subscribe              # Crée les souscriptions
 *   php artisan boxtal:subscribe --list        # Liste les souscriptions existantes
 *   php artisan boxtal:subscribe --delete={id} # Supprime une souscription
 */
class BoxtalSubscribe extends Command
{
    protected $signature = 'boxtal:subscribe
        {--list : Lister les souscriptions existantes}
        {--delete= : Supprimer une souscription par son ID}
        {--url= : URL de callback personnalisée}';

    protected $description = 'Gérer les souscriptions webhook Boxtal v3';

    private function baseUrl(): string
    {
        return rtrim(config('shipping.boxtal.v3_base_url', 'https://api.boxtal.com'), '/');
    }

    private function auth(): string
    {
        return base64_encode(
            config('shipping.boxtal.v3_access_key').':'.config('shipping.boxtal.v3_secret_key')
        );
    }

    public function handle(): int
    {
        if (! config('shipping.boxtal.v3_access_key') || ! config('shipping.boxtal.v3_secret_key')) {
            $this->error('BOXTAL_V3_ACCESS_KEY et BOXTAL_V3_SECRET_KEY doivent être configurés.');

            return self::FAILURE;
        }

        if ($this->option('list')) {
            return $this->listSubscriptions();
        }

        if ($this->option('delete')) {
            return $this->deleteSubscription($this->option('delete'));
        }

        return $this->createSubscriptions();
    }

    private function listSubscriptions(): int
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$this->auth(),
            'Accept' => 'application/json',
        ])->get($this->baseUrl().'/shipping/v3.1/subscription');

        if (! $response->successful()) {
            $this->error('Erreur API : '.$response->status().' — '.$response->body());

            return self::FAILURE;
        }

        $subscriptions = $response->json('content') ?? $response->json();

        if (empty($subscriptions)) {
            $this->info('Aucune souscription trouvée.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Event Type', 'Callback URL', 'Webhook Secret'],
            collect($subscriptions)->map(fn ($s) => [
                $s['id'] ?? '-',
                $s['eventType'] ?? '-',
                $s['callbackUrl'] ?? '-',
                isset($s['webhookValidationKey']) ? substr($s['webhookValidationKey'], 0, 20).'...' : '-',
            ])->toArray()
        );

        return self::SUCCESS;
    }

    private function deleteSubscription(string $id): int
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$this->auth(),
        ])->delete($this->baseUrl().'/shipping/v3.1/subscription/'.$id);

        if ($response->successful()) {
            $this->info("Souscription {$id} supprimée.");

            return self::SUCCESS;
        }

        $this->error('Erreur : '.$response->status().' — '.$response->body());

        return self::FAILURE;
    }

    private function createSubscriptions(): int
    {
        $callbackUrl = $this->option('url') ?? url('/api/boxtal/webhook');

        $this->info("URL de callback : {$callbackUrl}");

        $eventTypes = ['TRACKING_UPDATED', 'DOCUMENT_CREATED'];
        $created = 0;

        foreach ($eventTypes as $eventType) {
            $this->line("Création souscription pour {$eventType}...");

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$this->auth(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl().'/shipping/v3.1/subscription', [
                'eventType' => $eventType,
                'callbackUrl' => $callbackUrl,
                'webhookSecret' => bin2hex(random_bytes(32)),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $webhookSecret = $data['webhookValidationKey'] ?? $data['content']['webhookValidationKey'] ?? null;

                $this->info("  Souscription créée pour {$eventType}");

                if ($webhookSecret) {
                    $this->warn("  Webhook secret : {$webhookSecret}");
                    $this->warn("  -> Ajoute BOXTAL_V3_WEBHOOK_SECRET={$webhookSecret} dans ton .env");
                }

                $created++;
            } else {
                $this->error("  Erreur pour {$eventType} : {$response->status()} — {$response->body()}");
            }
        }

        if ($created > 0) {
            $this->newLine();
            $this->info("{$created} souscription(s) créée(s).");
            $this->warn('N\'oublie pas d\'ajouter BOXTAL_V3_WEBHOOK_SECRET dans ton .env !');
        }

        return $created > 0 ? self::SUCCESS : self::FAILURE;
    }
}
