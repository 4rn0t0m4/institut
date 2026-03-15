<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\QuizCompletion;

class QuizAiController extends Controller
{
    // Catégorie parente "Produits Visage"
    private const VISAGE_PARENT_CATEGORY_ID = 2;

    public function products(QuizCompletion $completion)
    {
        $completion->load(['answers.question.choices', 'result', 'quiz']);

        // Produits visage actifs avec catégorie et image
        $products = Product::with(['category', 'featuredImage'])
            ->where('is_active', true)
            ->whereHas('category', function ($q) {
                $q->where('parent_id', self::VISAGE_PARENT_CATEGORY_ID)
                    ->orWhere('id', self::VISAGE_PARENT_CATEGORY_ID);
            })
            ->get()
            ->map(fn (Product $p) => [
                'name' => $p->name,
                'price' => $p->currentPrice(),
                'category' => $p->category?->name,
                'description' => trim(strip_tags($p->short_description ?? '')),
                'url' => '/'.ltrim(parse_url($p->url(), PHP_URL_PATH), '/'),
                'image' => $p->featuredImage?->url,
            ]);

        // Réponses du quiz formatées
        $answers = $completion->answers->map(fn ($a) => [
            'question' => $a->question->title,
            'answer' => $a->question->choices->firstWhere('id', $a->answer)?->label ?? $a->answer,
        ]);

        // Extraire objectif et routine des 2 premières questions contextuelles
        $goal = null;
        $routine = null;
        foreach ($completion->answers as $answer) {
            $questionTitle = $answer->question->title ?? '';
            $choiceLabel = $answer->question->choices->firstWhere('id', $answer->answer)?->label;

            if (str_contains($questionTitle, 'objectif') && $choiceLabel) {
                $goal = $choiceLabel;
            }
            if (str_contains($questionTitle, 'routine') && $choiceLabel) {
                $routine = $choiceLabel;
            }
        }

        return response()->json([
            'skinType' => $completion->result?->title ?? 'Non déterminé',
            'skinDescription' => $completion->result?->description ? strip_tags($completion->result->description) : null,
            'goal' => $goal,
            'routine' => $routine,
            'answers' => $answers,
            'products' => $products,
        ]);
    }
}
