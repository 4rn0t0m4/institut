<x-layouts.app title="Contact — Institut Corps à Coeur" meta-description="Contactez l'Institut Corps à Coeur à Mézidon Canon. Formulaire de contact, téléphone et adresse pour toute question sur nos soins et produits.">

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <h1 class="text-3xl font-semibold mb-2" style="color: #276e44; font-family: 'Source Serif Pro', Georgia, serif;">
            Contactez-nous
        </h1>
        <p class="text-sm mb-10" style="color: #60916a;">
            Une question sur nos soins, nos produits ou une commande ? N'hésitez pas à nous écrire.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 320px; gap: 4rem; align-items: start;">

            {{-- Formulaire --}}
            <div>
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg text-sm font-medium" style="background-color: #f0fdf4; color: #276e44; border: 1px solid #bbf7d0;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}" class="space-y-5" data-turbo="false">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea name="message" id="message" rows="6" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent resize-y">{{ old('message') }}</textarea>
                        @error('message') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="inline-flex items-center gap-2 font-semibold px-6 py-3 rounded-lg text-sm text-white transition hover:opacity-90"
                            style="background-color: #276e44;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Envoyer le message
                    </button>
                </form>
            </div>

            {{-- Infos de contact --}}
            <div class="rounded-xl p-5" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #276e44;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold" style="color: #276e44;">Adresse</p>
                        <p class="text-sm mt-1" style="color: #60916a;">
                            Institut Corps à Coeur<br>
                            22 avenue Jean Jaurès<br>
                            14270 Mézidon Vallée d'Auge
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #276e44;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold" style="color: #276e44;">Téléphone</p>
                        <p class="text-sm mt-1" style="color: #60916a;">
                            <a href="tel:0231201045" class="hover:underline" style="color: #60916a;">02 31 20 10 45</a>
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 mb-5">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #276e44;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold" style="color: #276e44;">Horaires</p>
                        <p class="text-sm mt-1" style="color: #60916a;">
                            Lun – Ven : 9h – 18h<br>
                            Sam : sur rendez-vous
                        </p>
                    </div>
                </div>

                <a href="https://www.planity.com/institut-corps-a-coeur-14270-mezidon-vallee-dauge" target="_blank" rel="noopener"
                   class="block text-center font-semibold py-3 rounded-lg text-sm text-white transition hover:opacity-90"
                   style="background-color: #276e44;">
                    Prendre rendez-vous →
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
