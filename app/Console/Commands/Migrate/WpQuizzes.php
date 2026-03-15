<?php

namespace App\Console\Commands\Migrate;

use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizChoice;
use App\Models\QuizCompletion;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Support\Str;

class WpQuizzes extends WpImportCommand
{
    protected $signature = 'migrate:wp-quizzes';

    protected $description = 'Importe les quiz Chained Quiz depuis WordPress';

    public function handle(): void
    {
        $this->info('Import quiz...');

        QuizAnswer::query()->delete();
        QuizCompletion::query()->delete();
        QuizChoice::query()->delete();
        QuizResult::query()->delete();
        QuizQuestion::query()->delete();
        $this->safeTruncate('quizzes');

        $wpQuizzes = $this->wp()->table('chained_quizzes')->get();
        $created = 0;

        foreach ($wpQuizzes as $wq) {
            $quiz = Quiz::create([
                'title' => $wq->title,
                'slug' => Str::slug($wq->title) ?: 'quiz-'.$wq->id,
                'description' => null,
                'result_template' => $wq->output ?: null,
                'require_login' => (bool) ($wq->require_login ?? false),
                'times_to_take' => (int) ($wq->times_to_take ?? 0),
                'show_progress' => true,
                'auto_continue' => (bool) ($wq->autocontinue ?? false),
                'email_required' => (bool) ($wq->email_required ?? false),
                'email_user' => (bool) ($wq->email_user ?? false),
                'email_admin' => (bool) ($wq->email_admin ?? false),
                'admin_email' => $wq->admin_email ?? null,
                'is_active' => true,
            ]);

            $this->importQuestions($wq->id, $quiz->id);
            $this->importResults($wq->id, $quiz->id);
            $this->importCompletions($wq->id, $quiz->id);

            $created++;
        }

        $this->printResult('Quiz', $created);
    }

    private function importQuestions(int $wpQuizId, int $laravelQuizId): void
    {
        $questions = $this->wp()
            ->table('chained_questions')
            ->where('quiz_id', $wpQuizId)
            ->orderBy('sort_order')
            ->get();

        // Map wp question_id => laravel question_id pour le goto des choix
        $questionIdMap = [];

        foreach ($questions as $wq) {
            $question = QuizQuestion::create([
                'quiz_id' => $laravelQuizId,
                'title' => $wq->title ?: null,
                'question' => $wq->question,
                'type' => 'single',
                'sort_order' => (int) $wq->sort_order,
                'accept_comments' => (bool) ($wq->accept_comments ?? false),
                'comments_label' => $wq->accept_comments_label ?? null,
            ]);

            $questionIdMap[$wq->id] = $question->id;
        }

        // Importer les choix (séparément pour avoir la map complète)
        foreach ($questions as $wq) {
            $choices = $this->wp()
                ->table('chained_choices')
                ->where('question_id', $wq->id)
                ->orderBy('id')
                ->get();

            $sort = 0;
            foreach ($choices as $wc) {
                // Résoudre le "goto" : id de la prochaine question ou 'next'/'end'
                $goto = 'next';
                if (! empty($wc->goto) && $wc->goto !== 'next' && is_numeric($wc->goto)) {
                    $goto = isset($questionIdMap[(int) $wc->goto])
                        ? (string) $questionIdMap[(int) $wc->goto]
                        : 'next';
                } elseif (! empty($wc->goto)) {
                    $goto = $wc->goto;
                }

                QuizChoice::create([
                    'question_id' => $questionIdMap[$wq->id],
                    'label' => $wc->choice,
                    'points' => (float) ($wc->points ?? 0),
                    'is_correct' => (bool) ($wc->is_correct ?? false),
                    'goto' => $goto,
                    'sort_order' => $sort++,
                ]);
            }
        }
    }

    private function importResults(int $wpQuizId, int $laravelQuizId): void
    {
        $results = $this->wp()
            ->table('chained_results')
            ->where('quiz_id', $wpQuizId)
            ->get();

        foreach ($results as $wr) {
            QuizResult::create([
                'quiz_id' => $laravelQuizId,
                'title' => $wr->title,
                'description' => $wr->description ?? null,
                'points_min' => (float) ($wr->points_bottom ?? 0),
                'points_max' => (float) ($wr->points_top ?? 999),
                'redirect_url' => $wr->redirect_url ?? null,
            ]);
        }
    }

    private function importCompletions(int $wpQuizId, int $laravelQuizId): void
    {
        $completions = $this->wp()
            ->table('chained_completed')
            ->where('quiz_id', $wpQuizId)
            ->get();

        $userMap = file_exists(storage_path('wp_user_map.json'))
            ? json_decode(file_get_contents(storage_path('wp_user_map.json')), true)
            : [];

        foreach ($completions as $wc) {
            QuizCompletion::create([
                'quiz_id' => $laravelQuizId,
                'result_id' => null, // on ne mappe pas les résultats par ID
                'user_id' => isset($userMap[$wc->user_id]) ? $userMap[$wc->user_id] : null,
                'score' => (float) ($wc->points ?? 0),
                'email' => $wc->email ?? null,
                'ip' => $wc->ip ?? null,
                'source_url' => $wc->source_url ?? null,
                'snapshot' => $wc->snapshot ? json_decode($wc->snapshot, true) : null,
                'created_at' => $wc->datetime ?? now(),
            ]);
        }
    }
}
