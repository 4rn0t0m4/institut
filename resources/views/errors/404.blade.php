<x-layouts.app title="Page introuvable">
<div class="flex flex-col items-center justify-center px-4 py-24 text-center">
    <p class="text-7xl font-bold" style="color: #b0f1b9;">404</p>
    <h1 class="mt-4 text-2xl font-semibold" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
        Page introuvable
    </h1>
    <p class="mt-3 text-gray-500 max-w-md">
        La page que vous recherchez n'existe pas ou a été déplacée.
    </p>
    <div class="mt-8 flex gap-4">
        <a href="{{ route('home') }}"
           class="inline-block text-sm font-semibold px-6 py-2.5 rounded-lg text-white transition hover:opacity-90"
           style="background-color: #276e44;">
            Retour à l'accueil
        </a>
        <a href="{{ route('shop.index') }}"
           class="inline-block text-sm font-semibold px-6 py-2.5 rounded-lg border transition hover:opacity-70"
           style="color: #276e44; border-color: #b0f1b9;">
            Voir la boutique
        </a>
    </div>
</div>
</x-layouts.app>
