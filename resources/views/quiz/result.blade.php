<x-layouts.app :title="'Résultat — ' . $quiz->title">
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Résultat principal --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-8 sm:p-10 shadow-sm">

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-5">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <p class="text-sm text-gray-400 uppercase tracking-wide mb-1">Ton diagnostic</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">{{ $quiz->title }}</h1>

            @if($completion->result)
                <div class="inline-block mt-3 px-4 py-1.5 rounded-full bg-green-100 text-green-800 font-semibold text-lg">
                    {{ $completion->result->title }}
                </div>
            @endif
        </div>

        @if($completion->result)
            @if($completion->result->description)
                <div class="text-gray-600 text-sm sm:text-base leading-relaxed mt-6 border-t border-gray-100 pt-6">
                    {!! $completion->result->description !!}
                </div>
            @endif

            @if($completion->result->image)
                <img src="{{ $completion->result->image }}" alt="{{ $completion->result->title }}"
                     class="rounded-xl mx-auto mt-6 max-h-48 object-cover">
            @endif
        @else
            <p class="text-gray-600 text-center mt-6">Merci d'avoir complété ce questionnaire !</p>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('quiz.show') }}"
               class="inline-flex items-center justify-center gap-2 border border-gray-200 text-gray-600 py-2.5 px-6 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Recommencer
            </a>
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center justify-center gap-2 bg-green-700 text-white py-2.5 px-6 rounded-xl font-medium hover:bg-green-800 transition text-sm">
                Découvrir nos soins
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
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
                <p class="text-xs text-gray-400">Sélectionnées par notre conseillère beauté IA</p>
            </div>
        </div>

        {{-- Loading state --}}
        <div x-show="loading && !response" class="space-y-4">
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <svg class="animate-spin h-5 w-5 text-green-600 shrink-0" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span>Analyse de ton profil et sélection des soins adaptés...</span>
            </div>
            {{-- Skeleton placeholders --}}
            <div class="animate-pulse space-y-3">
                <div class="h-4 bg-gray-100 rounded w-3/4"></div>
                <div class="h-4 bg-gray-100 rounded w-full"></div>
                <div class="h-4 bg-gray-100 rounded w-5/6"></div>
                <div class="h-4 bg-gray-100 rounded w-2/3"></div>
            </div>
        </div>

        {{-- Error state --}}
        <div x-show="error" x-cloak class="text-sm text-red-600 bg-red-50 rounded-xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
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
        products: [],

        async fetchRecommendation() {
            this.loading = true;

            try {
                const dataRes = await fetch('/api/quiz/' + completionId + '/ai-data');
                if (!dataRes.ok) throw new Error('Impossible de charger les données');
                const data = await dataRes.json();

                // Stocker les produits pour enrichir les liens avec photos
                this.products = data.products || [];

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
                this.error = 'Impossible de générer les recommandations pour le moment. Tu peux découvrir nos soins directement dans la boutique.';
                console.error('AI recommendation error:', e);
            } finally {
                this.loading = false;
            }
        },

        // Trouver l'image d'un produit par son URL
        findProductImage(url) {
            const path = url.replace(/^https?:\/\/[^/]+/, '');
            const product = this.products.find(p => p.url === path || url.endsWith(p.url));
            return product?.image || null;
        },

        renderMarkdown(md) {
            return md
                // Supprimer les images markdown générées par l'IA
                .replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '')
                // Lien produit + prix sur la même ligne ou la suivante : [Nom](url) — prix€
                .replace(/\[([^\]]+)\]\(([^)]+)\)\s*[—–-]\s*(\d+[.,]?\d*)\s*€/g, (match, text, url, price) => {
                    const img = this.findProductImage(url);
                    const cleanText = text.replace(/\*\*/g, '');
                    const imgHtml = img
                        ? `<img src="${img}" alt="" class="w-16 h-16 object-cover rounded-lg shrink-0">`
                        : '';
                    return `<a href="${url}" class="flex items-center gap-3 p-3 my-2 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50/50 transition no-underline group">${imgHtml}<span class="flex flex-col min-w-0"><span class="text-sm font-medium text-gray-900 group-hover:text-green-800">${cleanText}</span><span class="text-sm text-green-700 font-semibold">${price} €</span></span></a>`;
                })
                // Liens restants (sans prix)
                .replace(/\[([^\]]+)\]\(([^)]+)\)/g, (match, text, url) => {
                    const img = this.findProductImage(url);
                    const cleanText = text.replace(/\*\*/g, '');
                    if (img) {
                        return `<a href="${url}" class="flex items-center gap-3 p-3 my-2 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50/50 transition no-underline group"><img src="${img}" alt="" class="w-16 h-16 object-cover rounded-lg shrink-0"><span class="text-sm font-medium text-gray-900 group-hover:text-green-800">${cleanText}</span></a>`;
                    }
                    return `<a href="${url}" class="text-green-700 underline hover:text-green-900">${cleanText}</a>`;
                })
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                .replace(/^### (.+)$/gm, '<h4 class="font-semibold text-gray-900 mt-5 mb-2">$1</h4>')
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
