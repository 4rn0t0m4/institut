<x-layouts.app :title="$quiz->title">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="text-center mb-10">
        <h1 class="text-3xl font-semibold text-gray-900 mb-3">{{ $quiz->title }}</h1>
        @if($quiz->description ?? false)
            <p class="text-gray-600">{{ $quiz->description }}</p>
        @endif
    </div>

    {{-- La frame qui contient la question courante --}}
    <turbo-frame id="quiz-question">
        @include('quiz.partials.question', [
            'question' => $firstQuestion,
            'answered' => 0,
            'total'    => $quiz->questions()->count(),
        ])
    </turbo-frame>
</div>
</x-layouts.app>
