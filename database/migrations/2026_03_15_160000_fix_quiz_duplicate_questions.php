<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige l'état du quiz après un échec partiel de migration.
 *
 * La migration précédente a échoué à mi-parcours (erreur created_at sur quiz_choices),
 * laissant une question orpheline sans choix et des sort_order doublés (+4 au lieu de +2).
 */
return new class extends Migration
{
    public function up(): void
    {
        $quizId = DB::table('quizzes')->where('slug', 'diagnostic-de-peau')->value('id');

        if (! $quizId) {
            return;
        }

        // Identifier les 2 nouvelles questions contextuelles (objectif + routine)
        // Elles ont 'goto' = 'next' sur tous leurs choix et 0 points
        $allQuestions = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('sort_order')
            ->get();

        // Trouver les questions orphelines (sans choix) — créées par la 1ère exécution ratée
        $orphanIds = [];
        foreach ($allQuestions as $q) {
            $choiceCount = DB::table('quiz_choices')->where('question_id', $q->id)->count();
            if ($choiceCount === 0) {
                $orphanIds[] = $q->id;
            }
        }

        // Supprimer les questions orphelines
        if (! empty($orphanIds)) {
            DB::table('quiz_answers')->whereIn('question_id', $orphanIds)->delete();
            DB::table('quiz_questions')->whereIn('id', $orphanIds)->delete();
        }

        // Maintenant, renuméroter tous les sort_order proprement (1, 2, 3, ...)
        $remaining = DB::table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->orderBy('sort_order')
            ->get();

        foreach ($remaining->values() as $i => $q) {
            DB::table('quiz_questions')
                ->where('id', $q->id)
                ->update(['sort_order' => $i + 1]);
        }

        // Vérifier et corriger les goto des choix qui pointent vers des IDs de questions
        // Les goto numériques doivent pointer vers des IDs de questions existantes
        $validIds = $remaining->pluck('id')->toArray();

        $allChoices = DB::table('quiz_choices')
            ->whereIn('question_id', $validIds)
            ->get();

        foreach ($allChoices as $choice) {
            if ($choice->goto && is_numeric($choice->goto) && ! in_array((int) $choice->goto, $validIds)) {
                // Ce goto pointe vers une question qui n'existe plus (l'orpheline supprimée)
                // On le met en 'next' par sécurité
                DB::table('quiz_choices')
                    ->where('id', $choice->id)
                    ->update(['goto' => 'next']);
            }
        }
    }

    public function down(): void
    {
        // Non réversible
    }
};
