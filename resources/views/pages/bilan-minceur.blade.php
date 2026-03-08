<x-layouts.app
    title="Votre Bilan Minceur Personnalisé"
    meta-description="Répondez à notre questionnaire minceur en 2 minutes pour préparer votre bilan personnalisé à l'Institut Corps à Cœur. Perte de poids, remodelage et raffermissement sur mesure à Mézidon-Canon."
    :canonical="route('bilan-minceur.show')">

{{-- Breadcrumb --}}
<nav class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 text-sm" style="color: #60916a;">
    <a href="{{ route('home') }}" class="hover:opacity-70 transition-opacity">Accueil</a>
    <span class="mx-1.5">/</span>
    <a href="{{ route('page.show', 'amincissement') }}" class="hover:opacity-70 transition-opacity">Amincissement</a>
    <span class="mx-1.5">/</span>
    <span style="color: #276e44;" class="font-medium">Votre bilan minceur personnalisé</span>
</nav>

{{-- Hero --}}
<header class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6 text-center">
    <h1 class="text-3xl sm:text-4xl font-semibold italic mb-3"
        style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
        Votre Bilan Minceur Personnalisé
    </h1>
    <p class="text-base italic max-w-2xl mx-auto" style="color: #60916a;">
        Prenez le contrôle de votre silhouette dès aujourd'hui.
    </p>
    <div class="mt-5 mx-auto w-16 border-t-2" style="border-color: #b0f1b9;"></div>
</header>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

    {{-- Intro --}}
    <div class="rounded-2xl p-6 sm:p-8 mb-8 text-center" style="background: linear-gradient(135deg, #f0faf3 0%, #e8f5ee 100%); border: 1px solid #b0f1b9;">
        <p class="text-lg font-semibold mb-3" style="color: #276e44;">Prête à transformer votre silhouette ? ✨</p>
        <p class="text-gray-600 mb-4">
            Vous rêvez de vous sentir mieux dans votre corps, de gommer quelques complexes ou de retrouver une peau plus ferme ?
            <strong>Votre transformation commence ici.</strong>
        </p>
        <p class="text-gray-600 mb-4">
            Parce que chaque corps est unique et que les solutions "miracles" universelles n'existent pas, nous avons fait le choix de
            <strong>l'accompagnement sur-mesure</strong>. Chez nous, pas de programme standardisé : nous créons une cure qui s'adapte
            à votre métabolisme, votre mode de vie et vos envies.
        </p>
        <p class="text-sm font-medium mt-4" style="color: #276e44;">
            En prenant 2 minutes pour répondre à ces questions, vous nous permettez de préparer votre accueil et de poser les bases de votre réussite.
        </p>
        <ul class="text-left text-sm text-gray-600 mt-4 space-y-2 max-w-lg mx-auto">
            <li class="flex items-start gap-2">
                <span style="color: #276e44;" class="mt-0.5">✓</span>
                Identifier précisément vos besoins (perte de poids, volume ou fermeté)
            </li>
            <li class="flex items-start gap-2">
                <span style="color: #276e44;" class="mt-0.5">✓</span>
                Préparer votre <strong>Bilan Minceur personnalisé (30 €)</strong>, l'examen clé pour définir votre protocole de soin
            </li>
            <li class="flex items-start gap-2">
                <span style="color: #276e44;" class="mt-0.5">✓</span>
                Envisager ensemble une cure durable sur plusieurs mois pour des résultats réels et visibles
            </li>
        </ul>
        <p class="mt-5 font-semibold" style="color: #276e44;">
            Faites le premier pas vers la version de vous-même que vous allez adorer ! 👇
        </p>
    </div>

    {{-- Message de succès --}}
    @if(session('success'))
    <div class="mb-6 rounded-xl p-5 flex items-start gap-3" style="background: #e8f5ee; border: 1px solid #276e44;">
        <span class="text-2xl">✅</span>
        <p class="text-sm font-medium" style="color: #276e44;">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Formulaire --}}
    <form method="POST" action="{{ route('bilan-minceur.submit') }}" class="space-y-8" data-turbo="false">
        @csrf

        {{-- Section 1 : Coordonnées --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
            <h2 class="text-xl font-semibold mb-1" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                1. Vos Informations de Contact
            </h2>
            <p class="text-sm text-gray-500 mb-6">Champs obligatoires *</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1.5">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                        class="h-11 w-full rounded-lg border px-4 text-sm text-gray-800 focus:outline-none focus:ring-2 {{ $errors->has('prenom') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:ring-green-200 focus:border-green-400' }}">
                    @error('prenom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-1.5">Nom *</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                        class="h-11 w-full rounded-lg border px-4 text-sm text-gray-800 focus:outline-none focus:ring-2 {{ $errors->has('nom') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:ring-green-200 focus:border-green-400' }}">
                    @error('nom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">E-mail *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="h-11 w-full rounded-lg border px-4 text-sm text-gray-800 focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:ring-green-200 focus:border-green-400' }}">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone *</label>
                    <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" required
                        class="h-11 w-full rounded-lg border px-4 text-sm text-gray-800 focus:outline-none focus:ring-2 {{ $errors->has('telephone') ? 'border-red-400 focus:ring-red-200' : 'border-gray-200 focus:ring-green-200 focus:border-green-400' }}">
                    @error('telephone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2 : Profil & Objectifs --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
            <h2 class="text-xl font-semibold mb-1" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                2. Votre Profil &amp; Vos Objectifs
            </h2>
            <p class="text-sm text-gray-500 mb-6">Dites-nous ce que vous souhaitez accomplir</p>

            <div class="mb-5">
                <p class="text-sm font-medium text-gray-700 mb-3">Quel est votre objectif principal ? <span class="text-gray-400 font-normal">(Plusieurs choix possibles)</span> *</p>
                <div class="space-y-3">
                    @foreach(['Perdre du poids', 'Perdre des centimètres', 'Raffermir et tonifier'] as $obj)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="objectifs[]" value="{{ $obj }}"
                            {{ in_array($obj, old('objectifs', [])) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-200">
                        <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $obj }}</span>
                    </label>
                    @endforeach
                </div>
                @error('objectifs') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="objectif_quantite" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Combien de kilos ou de centimètres souhaiteriez-vous perdre ?
                </label>
                <input type="text" id="objectif_quantite" name="objectif_quantite" value="{{ old('objectif_quantite') }}"
                    placeholder="Ex : 5 kilos, 3 cm de tour de hanches…"
                    class="h-11 w-full rounded-lg border border-gray-200 px-4 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 placeholder:text-gray-400">
            </div>

            <div>
                <label for="objectif_delai" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Sous quel délai aimeriez-vous atteindre cet objectif ?
                </label>
                <input type="text" id="objectif_delai" name="objectif_delai" value="{{ old('objectif_delai') }}"
                    placeholder="Ex : 3 mois, avant l'été, pour un événement…"
                    class="h-11 w-full rounded-lg border border-gray-200 px-4 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 placeholder:text-gray-400">
            </div>
        </div>

        {{-- Section 3 : Notre engagement --}}
        <div class="rounded-2xl p-6 sm:p-8" style="background: #f8fdf9; border: 1px solid #b0f1b9;">
            <h2 class="text-xl font-semibold mb-1" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                3. Notre Engagement de Résultats
            </h2>
            <p class="text-sm text-gray-500 mb-5">Lisez attentivement avant de valider</p>

            <div class="space-y-4 mb-6">
                <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100">
                    <div class="text-2xl">💎</div>
                    <div>
                        <p class="font-semibold text-sm text-gray-800 mb-1">Le Bilan Minceur Obligatoire (30 €)</p>
                        <p class="text-sm text-gray-600">
                            Avant de débuter, un bilan complet est indispensable. Il nous permet de comprendre votre problématique
                            et de créer un programme <strong>100 % personnalisé</strong> selon vos envies et vos besoins physiologiques.
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100">
                    <div class="text-2xl">📅</div>
                    <div>
                        <p class="font-semibold text-sm text-gray-800 mb-1">La Notion de Cure</p>
                        <p class="text-sm text-gray-600">
                            Une séance isolée ne suffit pas à transformer votre silhouette. Pour des résultats visibles et profonds,
                            nos cures s'étendent généralement <strong>sur plusieurs mois</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" name="acceptation" value="1"
                    {{ old('acceptation') ? 'checked' : '' }}
                    class="mt-0.5 w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-200">
                <span class="text-sm text-gray-700 group-hover:text-gray-900">
                    <strong>Oui, je comprends</strong> qu'un bilan initial (30 €) et un engagement sur la durée sont nécessaires pour obtenir des résultats durables. *
                </span>
            </label>
            @error('acceptation') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Section 4 : Prochaine étape --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8" x-data="{ action: '{{ old('action', '') }}' }">
            <h2 class="text-xl font-semibold mb-1" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
                4. Prochaine Étape
            </h2>
            <p class="text-sm text-gray-500 mb-6">Comment souhaitez-vous procéder ?</p>

            @error('action') <p class="mb-4 text-xs text-red-500">{{ $message }}</p> @enderror

            <div class="space-y-4">
                {{-- Option Planity --}}
                <label class="flex items-start gap-4 p-5 rounded-xl border-2 cursor-pointer transition-all"
                    :class="action === 'planity' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300'">
                    <input type="radio" name="action" value="planity" x-model="action"
                        class="mt-0.5 text-green-600 focus:ring-green-200">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">Prendre rendez-vous directement pour mon bilan (30 €)</p>
                        <p class="text-xs text-gray-500 mt-1">Vous serez redirigé(e) vers notre agenda en ligne Planity</p>
                    </div>
                </label>

                {{-- Option Rappel --}}
                <label class="flex items-start gap-4 p-5 rounded-xl border-2 cursor-pointer transition-all"
                    :class="action === 'rappel' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300'">
                    <input type="radio" name="action" value="rappel" x-model="action"
                        class="mt-0.5 text-green-600 focus:ring-green-200">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">Être rappelé(e) par notre équipe</p>
                        <p class="text-xs text-gray-500 mt-1">Nous vous contacterons dans les meilleurs délais au numéro indiqué</p>
                    </div>
                </label>
            </div>

            {{-- Bouton submit --}}
            <div class="mt-8">
                <button type="submit"
                    class="w-full py-4 rounded-xl text-white font-semibold text-base transition-all hover:opacity-90 active:scale-98"
                    style="background-color: #276e44;">
                    Envoyer mon questionnaire →
                </button>
                <p class="mt-3 text-center text-xs text-gray-400">
                    🔒 Vos données sont strictement confidentielles et ne sont jamais partagées avec des tiers.
                </p>
            </div>
        </div>

    </form>
</div>

</x-layouts.app>
