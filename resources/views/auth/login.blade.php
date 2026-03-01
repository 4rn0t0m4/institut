<x-layouts.guest title="Connexion">
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <h1 class="text-xl font-semibold text-gray-900 mb-6 text-center">Connexion</h1>

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

    <form action="{{ route('login.post') }}" method="POST" class="space-y-4" data-turbo="false">
        @csrf

        <div>
            <label class="block text-sm text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                   required autofocus>
        </div>

        <div>
            <label class="block text-sm text-gray-700 mb-1">Mot de passe</label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                   required>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600">
                Se souvenir de moi
            </label>
            <a href="{{ route('password.request') }}" class="text-sm hover:underline" style="color: #276e44;">
                Mot de passe oublié ?
            </a>
        </div>

        <button type="submit"
                class="w-full bg-green-700 text-white py-2.5 rounded font-medium hover:bg-green-800 transition text-sm">
            Se connecter
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="text-green-700 hover:underline">Créer un compte</a>
    </p>
</div>
</x-layouts.guest>
