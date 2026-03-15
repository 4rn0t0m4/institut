<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ExportEnrichedProducts extends Command
{
    protected $signature = 'products:export-enriched
                            {--output=enriched_products.sql : Fichier SQL de sortie}';

    protected $description = 'Exporte les champs enrichis des produits en SQL UPDATE pour import en prod';

    public function handle(): int
    {
        $products = Product::whereNotNull('benefits')
            ->where('benefits', '!=', '')
            ->get(['id', 'benefits', 'usage_instructions', 'composition', 'team_recommendation', 'unit_measure', 'description']);

        if ($products->isEmpty()) {
            $this->warn('Aucun produit enrichi trouvé.');

            return 1;
        }

        $output = $this->option('output');
        $lines = [];
        $lines[] = '-- Export des champs enrichis — '.now()->toDateTimeString();
        $lines[] = '-- '.$products->count().' produit(s)';
        $lines[] = '';
        $lines[] = 'START TRANSACTION;';
        $lines[] = '';

        foreach ($products as $product) {
            $lines[] = sprintf(
                'UPDATE products SET benefits=%s, usage_instructions=%s, composition=%s, team_recommendation=%s, unit_measure=%s, description=%s WHERE id=%d;',
                $this->quote($product->benefits),
                $this->quote($product->usage_instructions),
                $this->quote($product->composition),
                $this->quote($product->team_recommendation),
                $this->quote($product->unit_measure),
                $this->quote($product->description),
                $product->id
            );
        }

        $lines[] = '';
        $lines[] = 'COMMIT;';

        file_put_contents($output, implode("\n", $lines));

        $this->info("Fichier généré : {$output} ({$products->count()} produits)");
        $this->line('Appliquer en prod :');
        $this->line("  ssh instiqh@ssh.cluster130.hosting.ovh.net \"mysql -h instiqhapp.mysql.db -u instiqhapp -p'MOT_DE_PASSE' instiqhapp\" < {$output}");

        return 0;
    }

    private function quote(?string $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return "'".addslashes($value)."'";
    }
}
