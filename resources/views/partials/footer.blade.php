<footer class="bg-gray-50 border-t border-gray-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-sm text-gray-600">
            <div>
                <p class="font-semibold text-gray-900 mb-2">Institut Corps à Coeur</p>
                <p>Soins naturels & cosmétiques bio</p>
            </div>
            <div>
                <p class="font-semibold text-gray-900 mb-2">Navigation</p>
                <ul class="space-y-1">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-green-700">Boutique</a></li>
                    <li><a href="/a-propos" class="hover:text-green-700">À propos</a></li>
                    <li><a href="/contact" class="hover:text-green-700">Contact</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold text-gray-900 mb-2">Informations</p>
                <ul class="space-y-1">
                    <li><a href="/mentions-legales" class="hover:text-green-700">Mentions légales</a></li>
                    <li><a href="/politique-de-confidentialite" class="hover:text-green-700">Confidentialité</a></li>
                    <li><a href="/cgv" class="hover:text-green-700">CGV</a></li>
                </ul>
            </div>
        </div>
        <p class="mt-8 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Institut Corps à Coeur — Tous droits réservés
        </p>
    </div>
</footer>
