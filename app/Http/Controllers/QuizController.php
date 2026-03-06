<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizCompletion;
use App\Models\QuizAnswer;
use App\Models\QuizResult;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /** Page d'entrée : affiche la première question */
    public function show(string $slug)
    {
        $quiz = Quiz::where('slug', $slug)->firstOrFail();
        $firstQuestion = $quiz->questions()->with('choices')->orderBy('sort_order')->first();

        if (!$firstQuestion) {
            abort(404);
        }

        // Réinitialise la session pour ce quiz
        session(["quiz.{$quiz->id}.answers" => []]);

        return view('quiz.show', compact('quiz', 'firstQuestion'));
    }

    /** Affiche une question (Turbo Frame) */
    public function question(string $slug, int $questionId)
    {
        $quiz     = Quiz::where('slug', $slug)->firstOrFail();
        $question = QuizQuestion::where('quiz_id', $quiz->id)->findOrFail($questionId);
        $question->load('choices');

        $answers  = session("quiz.{$quiz->id}.answers", []);
        $answered = count($answers);
        $total    = $quiz->questions()->count();

        return view('quiz.partials.question', compact('quiz', 'question', 'answered', 'total'));
    }

    /** Traite la réponse et détermine la prochaine question ou le résultat */
    public function answer(Request $request, string $slug, int $questionId)
    {
        $quiz     = Quiz::where('slug', $slug)->firstOrFail();
        $question = QuizQuestion::where('quiz_id', $quiz->id)
            ->with('choices')
            ->findOrFail($questionId);

        $choiceId = $request->input('choice_id');
        $comment  = $request->input('comment');

        // Récupère le choix sélectionné
        $choice = $question->choices->firstWhere('id', $choiceId);

        // Sauvegarde la réponse en session
        $answers = session("quiz.{$quiz->id}.answers", []);
        $answers[$question->id] = [
            'choice_id' => $choiceId,
            'points'    => $choice?->points ?? 0,
            'comment'   => $comment,
        ];
        session(["quiz.{$quiz->id}.answers" => $answers]);

        // Détermine la prochaine question via goto ou sort_order
        $nextQuestion = $this->resolveNextQuestion($quiz, $question, $choice);

        if ($nextQuestion) {
            return redirect()->route('quiz.question', [$quiz->slug, $nextQuestion->id]);
        }

        // Fin du quiz : calcule le résultat
        $result = $this->computeResult($quiz, $answers);

        // Enregistre la complétion en DB
        $completion = $this->saveCompletion($quiz, $result, $answers, $request);

        return redirect()->route('quiz.result', [$quiz->slug, $completion->id]);
    }

    /** Affiche le résultat */
    public function result(string $slug, int $completionId)
    {
        $quiz       = Quiz::where('slug', $slug)->firstOrFail();
        $completion = QuizCompletion::where('quiz_id', $quiz->id)
            ->with('result')
            ->findOrFail($completionId);

        return view('quiz.result', compact('quiz', 'completion'));
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function resolveNextQuestion(Quiz $quiz, QuizQuestion $current, $choice): ?QuizQuestion
    {
        // Branchement via goto sur le choix
        if ($choice && $choice->goto && $choice->goto !== 'next') {
            if ($choice->goto === 'end') {
                return null;
            }

            return QuizQuestion::where('quiz_id', $quiz->id)
                ->where('id', $choice->goto)
                ->first();
        }

        // Suivante par sort_order
        return QuizQuestion::where('quiz_id', $quiz->id)
            ->where('sort_order', '>', $current->sort_order)
            ->orderBy('sort_order')
            ->first();
    }

    private function computeResult(Quiz $quiz, array $answers): ?QuizResult
    {
        $totalPoints = array_sum(array_column($answers, 'points'));

        return QuizResult::where('quiz_id', $quiz->id)
            ->where('points_min', '<=', $totalPoints)
            ->where('points_max', '>=', $totalPoints)
            ->first()
            ?? QuizResult::where('quiz_id', $quiz->id)->first(); // fallback
    }

    private function saveCompletion(Quiz $quiz, ?QuizResult $result, array $answers, Request $request): QuizCompletion
    {
        $totalPoints = array_sum(array_column($answers, 'points'));

        $completion = QuizCompletion::create([
            'quiz_id'    => $quiz->id,
            'result_id'  => $result?->id,
            'user_id'    => auth()->id(),
            'score'      => $totalPoints,
            'ip'         => $request->ip(),
            'source_url' => url()->previous(),
        ]);

        foreach ($answers as $questionId => $data) {
            QuizAnswer::create([
                'completion_id' => $completion->id,
                'question_id'   => $questionId,
                'answer'        => $data['choice_id'],
                'points'        => $data['points'],
                'comment'       => $data['comment'] ?? null,
            ]);
        }

        return $completion;
    }
}
