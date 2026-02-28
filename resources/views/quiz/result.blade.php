<x-layouts.app :title="'Résultat — ' . $quiz->title">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">

    <div class="bg-white border border-gray-100 rounded-2xl p-10 shadow-sm">

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ $quiz->title }}</h1>

        @if($completion->result)
            <h2 class="text-xl font-bold text-green-700 mb-4">{{ $completion->result->title }}</h2>

            @if($completion->result->description)
                <div class="text-gray-600 text-sm leading-relaxed mb-6 text-left">
                    {!! $completion->result->description !!}
                </div>
            @endif

            @if($completion->result->image)
                <img src="{{ $completion->result->image }}" alt="{{ $completion->result->title }}"
                     class="rounded-xl mx-auto mb-6 max-h-48 object-cover">
            @endif
        @else
            <p class="text-gray-600 mb-6">Merci d'avoir complété ce quiz !</p>
        @endif

        <p class="text-sm text-gray-400 mb-8">Score : {{ $completion->score }} point(s)</p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if($completion->result?->redirect_url)
                <a href="{{ $completion->result->redirect_url }}"
                   class="inline-block bg-green-700 text-white py-2.5 px-6 rounded-xl font-medium hover:bg-green-800 transition text-sm">
                    Voir les produits recommandés
                </a>
            @endif
            <a href="{{ route('quiz.show', $quiz->slug) }}"
               class="inline-block border border-gray-200 text-gray-600 py-2.5 px-6 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
                Recommencer le quiz
            </a>
        </div>
    </div>
</div>
</x-layouts.app>
