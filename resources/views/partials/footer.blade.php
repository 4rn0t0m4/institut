<footer class="border-t mt-16" style="background-color: #c9fad9; border-color: #b0f1b9;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-sm" style="color: #276e44;">

            <div class="md:col-span-1">
                <p class="font-semibold text-base mb-2"
                   style="font-family: 'Source Serif Pro', Georgia, serif;">
                    Institut Corps à Coeur
                </p>
                <p style="color: #60916a; font-style: italic;">Soins naturels &amp; bien-être</p>
                <div class="mt-4 space-y-1 text-xs" style="color: #60916a;">
                    <p>Lun–Ven : 9h–18h</p>
                    <p>Samedi : sur rendez-vous</p>
                    <p class="font-medium" style="color: #276e44;">02 31 20 10 45</p>
                </div>
                <a href="https://www.planity.com" target="_blank" rel="noopener"
                   class="inline-block mt-4 text-xs font-semibold px-4 py-2 rounded-lg text-white"
                   style="background-color: #276e44;">
                    Prendre rendez-vous
                </a>
            </div>

            <div>
                <p class="font-semibold mb-3">Prestations</p>
                <ul class="space-y-1.5" style="color: #60916a;">
                    <li><a href="{{ route('page.show', 'epilations') }}" class="hover:opacity-70 transition-opacity">Épilations</a></li>
                    <li><a href="{{ route('page.show', 'massages-du-monde') }}" class="hover:opacity-70 transition-opacity">Massages</a></li>
                    <li><a href="{{ route('page.show', 'soins-visage') }}" class="hover:opacity-70 transition-opacity">Soins Visage</a></li>
                    <li><a href="{{ route('page.show', 'sportifs') }}" class="hover:opacity-70 transition-opacity">Sportifs</a></li>
                    <li><a href="{{ route('page.show', 'amincissement') }}" class="hover:opacity-70 transition-opacity">Amincissement</a></li>
                </ul>
            </div>

            <div>
                <p class="font-semibold mb-3">Boutique</p>
                <ul class="space-y-1.5" style="color: #60916a;">
                    <li><a href="{{ route('shop.index') }}" class="hover:opacity-70 transition-opacity">Tous les produits</a></li>
                    <li><a href="{{ route('shop.index', ['categorie' => 'produits-visage']) }}" class="hover:opacity-70 transition-opacity">Produits Visage</a></li>
                    <li><a href="{{ route('shop.index', ['categorie' => 'produits-corps']) }}" class="hover:opacity-70 transition-opacity">Produits Corps</a></li>
                    <li><a href="{{ route('shop.index', ['categorie' => 'coffrets-cadeaux']) }}" class="hover:opacity-70 transition-opacity">Coffrets Cadeaux</a></li>
                    <li><a href="{{ route('quiz.show', 'type-de-peau') }}" class="hover:opacity-70 transition-opacity">Quiz Type de Peau</a></li>
                </ul>
            </div>

            <div>
                <p class="font-semibold mb-3">Informations</p>
                <ul class="space-y-1.5" style="color: #60916a;">
                    <li><a href="{{ route('blog.index') }}" class="hover:opacity-70 transition-opacity">Blog</a></li>
                    <li><a href="{{ route('page.show', 'mentions-legales') }}" class="hover:opacity-70 transition-opacity">Mentions légales</a></li>
                    <li><a href="{{ route('page.show', 'politique-de-confidentialite') }}" class="hover:opacity-70 transition-opacity">Confidentialité</a></li>
                    <li><a href="{{ route('page.show', 'cgv') }}" class="hover:opacity-70 transition-opacity">CGV</a></li>
                    <li><a href="{{ route('login') }}" class="hover:opacity-70 transition-opacity">Mon compte</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t text-center text-xs" style="border-color: #b0f1b9; color: #60916a;">
            &copy; {{ date('Y') }} Institut Corps à Coeur — Tous droits réservés
        </div>
    </div>
</footer>
