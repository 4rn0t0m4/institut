<div x-data="{ selected: null, submitting: false }"
     class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm"
     x-show="true"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0">

    {{-- Progression --}}
    @if($total > 0)
        <div class="mb-8">
            <div class="flex justify-between items-center text-xs text-gray-400 mb-2">
                <span>Question {{ $answered + 1 }} sur {{ $total }}</span>
                <span>{{ $total > 0 ? round(($answered) / $total * 100) : 0 }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-700 ease-out"
                     style="width: {{ $total > 0 ? round($answered / $total * 100) : 0 }}%"></div>
            </div>
        </div>
    @endif

    <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">{{ $question->title }}</h2>

    @if($question->question && $question->question !== $question->title)
        <p class="text-gray-500 text-sm mb-6">{{ $question->question }}</p>
    @else
        <div class="mb-6"></div>
    @endif

    <form action="{{ route('quiz.answer', ['question' => $question->id]) }}" method="POST"
          data-turbo="false"
          x-ref="form"
          @submit="submitting = true">
        @csrf

        <div class="space-y-3 mb-6">
            @foreach($question->choices as $choice)
                <label class="group flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                       :class="selected === {{ $choice->id }}
                           ? 'border-green-500 bg-green-50 shadow-sm'
                           : 'border-gray-100 hover:border-green-200 hover:bg-green-50/50'"
                       @click="
                           selected = {{ $choice->id }};
                           setTimeout(() => { $refs.form.submit() }, 350);
                       ">
                    <input type="radio" name="choice_id" value="{{ $choice->id }}"
                           class="text-green-600 focus:ring-green-500 shrink-0"
                           :checked="selected === {{ $choice->id }}"
                           required>
                    <span class="text-sm sm:text-base text-gray-700 group-hover:text-gray-900 transition-colors">{{ $choice->label }}</span>

                    {{-- Flèche animée au survol --}}
                    <svg class="w-5 h-5 text-green-500 ml-auto shrink-0 opacity-0 -translate-x-2 transition-all duration-200"
                         :class="selected === {{ $choice->id }} ? 'opacity-100 translate-x-0' : 'group-hover:opacity-50 group-hover:translate-x-0'"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </label>
            @endforeach
        </div>

        @if($question->accept_comments)
            <div class="mb-6">
                <label class="block text-sm text-gray-500 mb-2">{{ $question->comments_label ?? 'Un commentaire ? (optionnel)' }}</label>
                <textarea name="comment" rows="2"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-green-500 focus:border-green-500 transition"
                          @click.stop></textarea>
            </div>

            {{-- Bouton visible seulement si commentaire autorisé (le form ne s'auto-submit pas) --}}
            <button type="submit"
                    class="w-full bg-green-700 text-white py-3 rounded-xl font-medium hover:bg-green-800 transition disabled:opacity-50"
                    :disabled="!selected || submitting">
                <span x-show="!submitting">Suivant →</span>
                <span x-show="submitting" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Chargement...
                </span>
            </button>
        @endif

        {{-- Indicateur de chargement pendant la transition --}}
        <div x-show="submitting" x-cloak class="flex items-center justify-center gap-2 text-sm text-green-600 mt-4">
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>Question suivante...</span>
        </div>
    </form>
</div>
