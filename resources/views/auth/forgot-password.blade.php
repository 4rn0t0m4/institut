<x-layouts.guest title="Mot de passe oublié">
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <h1 class="text-xl font-semibold text-gray-900 mb-2 text-center">Mot de passe oublié</h1>
    <p class="text-sm text-gray-500 text-center mb-6">
        Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.
    </p>

    @if(session('status'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded px-4 py-3 mb-5">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded px-4 py-3 mb-5">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4" data-turbo="false">
        @csrf

        <div>
            <label class="block text-sm text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                   required autofocus>
        </div>

        <button type="submit"
                class="w-full text-white py-2.5 rounded font-medium transition text-sm"
                style="background-color: #276e44;">
            Envoyer le lien
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="hover:underline" style="color: #276e44;">Retour à la connexion</a>
    </p>
</div>
</x-layouts.guest>
