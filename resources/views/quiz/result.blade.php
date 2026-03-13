<x-layouts.app :title="'Résultat — ' . $quiz->title">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="bg-white border border-gray-100 rounded-2xl p-10 shadow-sm text-center">

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

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('quiz.show') }}"
               class="inline-block border border-gray-200 text-gray-600 py-2.5 px-6 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
                Recommencer le quiz
            </a>
        </div>
    </div>

    {{-- Recommandation IA --}}
    @if($completion->result)
    <div class="mt-8 bg-white border border-gray-100 rounded-2xl p-8 shadow-sm"
         x-data="aiRecommendation({{ $completion->id }})" x-init="fetchRecommendation()">

        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Nos recommandations personnalisées</h3>
                <p class="text-xs text-gray-400">Analyse par notre conseillère beauté IA</p>
            </div>
        </div>

        {{-- Loading state --}}
        <div x-show="loading && !response" class="flex items-center gap-3 text-sm text-gray-500">
            <svg class="animate-spin h-5 w-5 text-green-600" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>Analyse de votre profil en cours...</span>
        </div>

        {{-- Error state --}}
        <div x-show="error" x-cloak class="text-sm text-red-600 bg-red-50 rounded-lg p-4">
            <span x-text="error"></span>
        </div>

        {{-- AI response (streamed markdown) --}}
        <div x-show="response" x-cloak
             class="prose prose-sm prose-green max-w-none text-gray-700"
             x-html="renderedResponse">
        </div>
    </div>
    @endif
</div>

@if($completion->result)
<script>
function aiRecommendation(completionId) {
    return {
        loading: false,
        response: '',
        renderedResponse: '',
        error: null,

        async fetchRecommendation() {
            this.loading = true;

            try {
                // 1. Récupérer les données du quiz + produits
                const dataRes = await fetch('/api/quiz/' + completionId + '/ai-data');
                if (!dataRes.ok) throw new Error('Impossible de charger les données');
                const data = await dataRes.json();

                // 2. Envoyer au proxy Vercel (streaming)
                const proxyUrl = '{{ config("services.claude_proxy.url", "https://icc-claude-proxy.vercel.app") }}/api/chat';
                const streamRes = await fetch(proxyUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });

                if (!streamRes.ok) throw new Error('Erreur du service IA');

                const reader = streamRes.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');
                    buffer = lines.pop();

                    for (const line of lines) {
                        if (!line.startsWith('data: ')) continue;
                        const payload = line.slice(6);
                        if (payload === '[DONE]') break;

                        try {
                            const { text, error } = JSON.parse(payload);
                            if (error) throw new Error(error);
                            if (text) {
                                this.response += text;
                                this.renderedResponse = this.renderMarkdown(this.response);
                            }
                        } catch (e) {
                            if (e.message !== error) console.warn('Parse error:', e);
                        }
                    }
                }
            } catch (e) {
                this.error = 'Impossible de générer les recommandations. Veuillez réessayer.';
                console.error('AI recommendation error:', e);
            } finally {
                this.loading = false;
            }
        },

        renderMarkdown(md) {
            return md
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                .replace(/^### (.+)$/gm, '<h4 class="font-semibold text-gray-900 mt-4 mb-1">$1</h4>')
                .replace(/^## (.+)$/gm, '<h3 class="font-bold text-gray-900 mt-5 mb-2">$1</h3>')
                .replace(/^- (.+)$/gm, '<li class="ml-4">$1</li>')
                .replace(/(<li.*<\/li>\n?)+/g, '<ul class="list-disc space-y-1 my-2">$&</ul>')
                .replace(/\n\n/g, '</p><p class="mb-2">')
                .replace(/\n/g, '<br>')
                .replace(/^/, '<p class="mb-2">')
                .replace(/$/, '</p>');
        }
    }
}
</script>
@endif
</x-layouts.app>
