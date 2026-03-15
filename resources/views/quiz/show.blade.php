<x-layouts.app :title="$quiz->title">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- En-tête du quiz --}}
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-4">
            <svg class="w-7 h-7 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-2">{{ $quiz->title }}</h1>
        @if($quiz->description ?? false)
            <p class="text-gray-500 max-w-md mx-auto">{{ $quiz->description }}</p>
        @endif
    </div>

    @include('quiz.partials.question', [
        'question' => $firstQuestion,
        'answered' => $answered ?? 0,
        'total'    => $total ?? $quiz->questions()->count(),
    ])
</div>
</x-layouts.app>
