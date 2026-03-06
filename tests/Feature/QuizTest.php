<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\QuizChoice;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    private function createQuizWithQuestions(): array
    {
        $quiz = Quiz::create([
            'title' => 'Type de peau', 'slug' => 'type-de-peau',
            'is_active' => true,
        ]);

        $q1 = QuizQuestion::create([
            'quiz_id' => $quiz->id, 'title' => 'Question 1',
            'question' => 'Comment est votre peau ?', 'type' => 'single', 'sort_order' => 1,
        ]);
        $c1a = QuizChoice::create(['question_id' => $q1->id, 'label' => 'Sèche', 'points' => 1, 'sort_order' => 1]);
        $c1b = QuizChoice::create(['question_id' => $q1->id, 'label' => 'Grasse', 'points' => 3, 'sort_order' => 2]);

        $q2 = QuizQuestion::create([
            'quiz_id' => $quiz->id, 'title' => 'Question 2',
            'question' => 'Vos pores sont ?', 'type' => 'single', 'sort_order' => 2,
        ]);
        $c2a = QuizChoice::create(['question_id' => $q2->id, 'label' => 'Serrés', 'points' => 1, 'sort_order' => 1]);
        $c2b = QuizChoice::create(['question_id' => $q2->id, 'label' => 'Dilatés', 'points' => 3, 'sort_order' => 2]);

        $resultDry = QuizResult::create([
            'quiz_id' => $quiz->id, 'title' => 'Peau sèche',
            'description' => 'Votre peau est sèche.',
            'points_min' => 0, 'points_max' => 3,
        ]);
        $resultOily = QuizResult::create([
            'quiz_id' => $quiz->id, 'title' => 'Peau grasse',
            'description' => 'Votre peau est grasse.',
            'points_min' => 4, 'points_max' => 10,
        ]);

        return compact('quiz', 'q1', 'q2', 'c1a', 'c1b', 'c2a', 'c2b', 'resultDry', 'resultOily');
    }

    public function test_quiz_page_loads(): void
    {
        $data = $this->createQuizWithQuestions();

        $response = $this->get(route('quiz.show', 'type-de-peau'));

        $response->assertStatus(200);
        $response->assertSee('Question 1');
    }

    public function test_nonexistent_quiz_returns_404(): void
    {
        $this->get(route('quiz.show', 'inexistant'))->assertStatus(404);
    }

    public function test_answer_question_redirects_to_next(): void
    {
        $data = $this->createQuizWithQuestions();

        // Start quiz to init session
        $this->get(route('quiz.show', 'type-de-peau'));

        $response = $this->post(route('quiz.answer', ['type-de-peau', $data['q1']->id]), [
            'choice_id' => $data['c1a']->id,
        ]);

        $response->assertRedirect(route('quiz.question', ['type-de-peau', $data['q2']->id]));
    }

    public function test_last_answer_redirects_to_result(): void
    {
        $data = $this->createQuizWithQuestions();

        // Start quiz
        $this->get(route('quiz.show', 'type-de-peau'));

        // Answer Q1
        $this->post(route('quiz.answer', ['type-de-peau', $data['q1']->id]), [
            'choice_id' => $data['c1a']->id,
        ]);

        // Answer Q2 (last question)
        $response = $this->post(route('quiz.answer', ['type-de-peau', $data['q2']->id]), [
            'choice_id' => $data['c2a']->id,
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('resultat', $response->headers->get('Location'));
    }

    public function test_quiz_completion_saved_to_database(): void
    {
        $data = $this->createQuizWithQuestions();

        $this->get(route('quiz.show', 'type-de-peau'));

        $this->post(route('quiz.answer', ['type-de-peau', $data['q1']->id]), [
            'choice_id' => $data['c1a']->id,
        ]);
        $this->post(route('quiz.answer', ['type-de-peau', $data['q2']->id]), [
            'choice_id' => $data['c2a']->id,
        ]);

        $this->assertDatabaseHas('quiz_completions', [
            'quiz_id' => $data['quiz']->id,
            'score'   => 2.00, // 1 + 1
        ]);
        $this->assertDatabaseCount('quiz_answers', 2);
    }

    public function test_result_matches_points_range(): void
    {
        $data = $this->createQuizWithQuestions();

        $this->get(route('quiz.show', 'type-de-peau'));

        // Choose high-point answers (3+3=6)
        $this->post(route('quiz.answer', ['type-de-peau', $data['q1']->id]), [
            'choice_id' => $data['c1b']->id,
        ]);
        $this->post(route('quiz.answer', ['type-de-peau', $data['q2']->id]), [
            'choice_id' => $data['c2b']->id,
        ]);

        $this->assertDatabaseHas('quiz_completions', [
            'quiz_id'   => $data['quiz']->id,
            'result_id' => $data['resultOily']->id,
            'score'     => 6.00,
        ]);
    }

    public function test_result_page_displays(): void
    {
        $data = $this->createQuizWithQuestions();

        $this->get(route('quiz.show', 'type-de-peau'));
        $this->post(route('quiz.answer', ['type-de-peau', $data['q1']->id]), ['choice_id' => $data['c1a']->id]);
        $response = $this->post(route('quiz.answer', ['type-de-peau', $data['q2']->id]), ['choice_id' => $data['c2a']->id]);

        $resultUrl = $response->headers->get('Location');
        $this->get($resultUrl)->assertStatus(200);
    }
}
