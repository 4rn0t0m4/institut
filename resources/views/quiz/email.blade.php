<x-layouts.app :title="'Ton résultat est prêt — ' . $quiz->title">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- En-tête --}}
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-4">
            <svg class="w-7 h-7 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-2">{{ $quiz->title }}</h1>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-8 sm:p-10 shadow-sm"
         x-data="{ submitting: false }">

        {{-- Barre de progression complète --}}
        <div class="mb-8">
            <div class="flex justify-between items-center text-xs text-gray-400 mb-2">
                <span>Diagnostic terminé</span>
                <span>100%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Ton diagnostic est prêt !</h2>
            <p class="text-gray-500 text-sm">Entre ton adresse email pour découvrir ton résultat personnalisé et le recevoir par email.</p>
        </div>

        <form action="{{ route('quiz.email.submit', ['completion' => $completion->id]) }}" method="POST"
              data-turbo="false"
              @submit="submitting = true">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Adresse email</label>
                <input type="email" name="email" id="email" required
                       value="{{ old('email', auth()->user()?->email) }}"
                       placeholder="ton.email@exemple.fr"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-green-500 focus:border-green-500 transition @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-green-700 text-white py-3 rounded-xl font-medium hover:bg-green-800 transition disabled:opacity-50"
                    :disabled="submitting">
                <span x-show="!submitting">Découvrir mon résultat</span>
                <span x-show="submitting" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Chargement...
                </span>
            </button>

            <p class="text-xs text-gray-400 text-center mt-4">
                Ton email ne sera utilisé que pour t'envoyer ton résultat et des conseils personnalisés.
            </p>
        </form>
    </div>
</div>
</x-layouts.app>
