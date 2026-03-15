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

        // Produits visage actifs avec catégorie
        $products = Product::with('category')
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
            ]);

        // Réponses du quiz formatées
        $answers = $completion->answers->map(fn ($a) => [
            'question' => $a->question->title,
            'answer' => $a->question->choices->firstWhere('id', $a->answer)?->label ?? $a->answer,
        ]);

        return response()->json([
            'skinType' => $completion->result?->title ?? 'Non déterminé',
            'answers' => $answers,
            'products' => $products,
        ]);
    }
}
