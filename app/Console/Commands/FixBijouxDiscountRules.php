<?php

namespace App\Console\Commands;

use App\Models\DiscountRule;
use Illuminate\Console\Command;

class FixBijouxDiscountRules extends Command
{
    protected $signature = 'fix:bijoux-discount-rules';

    protected $description = 'Corrige les règles de remise bijoux : cible catégorie Bijoux (id 5) et quantités min';

    public function handle(): int
    {
        $rule2 = DiscountRule::find(1);
        $rule3 = DiscountRule::find(2);

        if (! $rule2 || ! $rule3) {
            $this->error('Règles de remise bijoux introuvables (id 1 et 2).');

            return self::FAILURE;
        }

        $rule2->update(['target_categories' => [5], 'min_quantity' => 2]);
        $rule3->update(['target_categories' => [5], 'min_quantity' => 3]);

        $this->info('Règles de remise bijoux corrigées :');
        $this->line("  - #{$rule2->id} \"{$rule2->name}\" → catégorie Bijoux, min_quantity=2");
        $this->line("  - #{$rule3->id} \"{$rule3->name}\" → catégorie Bijoux, min_quantity=3");

        return self::SUCCESS;
    }
}
