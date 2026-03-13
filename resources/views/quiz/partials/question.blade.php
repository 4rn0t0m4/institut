<div x-data class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm">

    {{-- Progression --}}
    @if($total > 0)
        <div class="mb-6">
            <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                <span>Question {{ $answered + 1 }} / {{ $total }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-green-600 h-1.5 rounded-full transition-all duration-500"
                     style="width: {{ $total > 0 ? round($answered / $total * 100) : 0 }}%"></div>
            </div>
        </div>
    @endif

    <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $question->title }}</h2>

    @if($question->question && $question->question !== $question->title)
        <p class="text-gray-600 text-sm mb-6">{{ $question->question }}</p>
    @endif

    <form action="{{ route('quiz.answer', ['question' => $question->id]) }}" method="POST" data-turbo="false">
        @csrf

        <div class="space-y-3 mb-6">
            @foreach($question->choices as $choice)
                <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 cursor-pointer
                              hover:border-green-300 hover:bg-green-50 transition has-[:checked]:border-green-500
                              has-[:checked]:bg-green-50">
                    <input type="radio" name="choice_id" value="{{ $choice->id }}"
                           class="text-green-600 focus:ring-green-500" required>
                    <span class="text-sm text-gray-800">{{ $choice->label }}</span>
                </label>
            @endforeach
        </div>

        @if($question->accept_comments)
            <div class="mb-6">
                <label class="block text-sm text-gray-600 mb-2">Commentaire (optionnel)</label>
                <textarea name="comment" rows="2"
                          class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"></textarea>
            </div>
        @endif

        <button type="submit"
                class="w-full bg-green-700 text-white py-3 rounded-xl font-medium hover:bg-green-800 transition">
            Suivant →
        </button>
    </form>
</div>
