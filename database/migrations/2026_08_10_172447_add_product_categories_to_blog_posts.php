<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $mappings = [
            // Comment connaître son type de peau → Crèmes Visage, Nettoyant Visage
            'comment-connaitre-son-type-de-peau' => [8, 7],
            // Pourquoi hydrater peau grasse → Crèmes Visage, Nettoyant Visage
            'pourquoi-hydrater-peau-grasse' => [8, 7],
            // Huiles végétales → Huiles de Douche, crèmes mains/huiles/lait
            'bienfaits-huiles-vegetales-peau' => [10, 13],
            // Routine hiver → Crèmes Visage, Baume réparateur, crèmes mains
            'routine-beaute-naturelle-hiver' => [8, 12, 13],
            // Choisir son sérum → Crèmes Visage, Nettoyant Visage
            'comment-choisir-serum-visage' => [8, 7],
            // Diagnostic de peau → Nettoyant Visage, Gommage et Masque
            'pourquoi-faire-diagnostic-de-peau' => [7, 9],
            // Erreurs vieillissement → Crèmes Visage, Gommage et Masque
            'erreurs-accelerent-vieillissement-cutane' => [8, 9],
            // Routine anti-âge → Crèmes Visage, Gommage et Masque, Nettoyant Visage
            'comment-reussir-routine-anti-age' => [8, 9, 7],
            // Charme d'Orient → Gommages corps, Huiles de Douche, Baume réparateur
            'pourquoi-jaime-soins-charme-orient' => [11, 10, 12],
            // Massages du visage → Crèmes Visage, crèmes mains/huiles/lait
            'bienfaits-massages-du-visage' => [8, 13],
            // Préparer peau vacances → Gommages corps, Gommage et Masque, crèmes mains
            'preparer-peau-avant-vacances' => [11, 9, 13],
            // Protéger peau soleil → Crèmes Visage, Baume réparateur
            'proteger-naturellement-peau-soleil' => [8, 12],
        ];

        foreach ($mappings as $slug => $categoryIds) {
            Post::where('slug', $slug)->update(['categories' => json_encode($categoryIds)]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'comment-connaitre-son-type-de-peau',
            'pourquoi-hydrater-peau-grasse',
            'bienfaits-huiles-vegetales-peau',
            'routine-beaute-naturelle-hiver',
            'comment-choisir-serum-visage',
            'pourquoi-faire-diagnostic-de-peau',
            'erreurs-accelerent-vieillissement-cutane',
            'comment-reussir-routine-anti-age',
            'pourquoi-jaime-soins-charme-orient',
            'bienfaits-massages-du-visage',
            'preparer-peau-avant-vacances',
            'proteger-naturellement-peau-soleil',
        ])->update(['categories' => null]);
    }
};
