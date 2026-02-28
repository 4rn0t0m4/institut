<x-layouts.app title="Finaliser la commande">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Finaliser la commande</h1>

    <form action="{{ route('checkout.store') }}" method="POST"
          x-data="{ shippingSame: true }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Colonne gauche : formulaire --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Adresse de facturation --}}
                <section>
                    <h2 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                        Adresse de facturation
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Prénom *</label>
                            <input type="text" name="billing_first_name"
                                   value="{{ old('billing_first_name') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('billing_first_name') border-red-400 @enderror"
                                   required>
                            @error('billing_first_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Nom *</label>
                            <input type="text" name="billing_last_name"
                                   value="{{ old('billing_last_name') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('billing_last_name') border-red-400 @enderror"
                                   required>
                            @error('billing_last_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">E-mail *</label>
                            <input type="email" name="billing_email"
                                   value="{{ old('billing_email', auth()->user()?->email) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('billing_email') border-red-400 @enderror"
                                   required>
                            @error('billing_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Téléphone</label>
                            <input type="tel" name="billing_phone"
                                   value="{{ old('billing_phone') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">Adresse *</label>
                            <input type="text" name="billing_address_1"
                                   value="{{ old('billing_address_1') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('billing_address_1') border-red-400 @enderror"
                                   required>
                            @error('billing_address_1')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">Complément d'adresse</label>
                            <input type="text" name="billing_address_2"
                                   value="{{ old('billing_address_2') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Code postal *</label>
                            <input type="text" name="billing_postcode"
                                   value="{{ old('billing_postcode') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('billing_postcode') border-red-400 @enderror"
                                   required>
                            @error('billing_postcode')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Ville *</label>
                            <input type="text" name="billing_city"
                                   value="{{ old('billing_city') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500 @error('billing_city') border-red-400 @enderror"
                                   required>
                            @error('billing_city')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Pays *</label>
                            <select name="billing_country"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                                    required>
                                <option value="FR" {{ old('billing_country', 'FR') === 'FR' ? 'selected' : '' }}>France</option>
                                <option value="BE" {{ old('billing_country') === 'BE' ? 'selected' : '' }}>Belgique</option>
                                <option value="CH" {{ old('billing_country') === 'CH' ? 'selected' : '' }}>Suisse</option>
                                <option value="LU" {{ old('billing_country') === 'LU' ? 'selected' : '' }}>Luxembourg</option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- Adresse de livraison --}}
                <section>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-900">Adresse de livraison</h2>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="shipping_same" value="1"
                                   x-model="shippingSame"
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            Identique à la facturation
                        </label>
                    </div>

                    <div x-show="!shippingSame" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Prénom</label>
                            <input type="text" name="shipping_first_name"
                                   value="{{ old('shipping_first_name') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Nom</label>
                            <input type="text" name="shipping_last_name"
                                   value="{{ old('shipping_last_name') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">Adresse</label>
                            <input type="text" name="shipping_address_1"
                                   value="{{ old('shipping_address_1') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm text-gray-700 mb-1">Complément</label>
                            <input type="text" name="shipping_address_2"
                                   value="{{ old('shipping_address_2') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Code postal</label>
                            <input type="text" name="shipping_postcode"
                                   value="{{ old('shipping_postcode') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Ville</label>
                            <input type="text" name="shipping_city"
                                   value="{{ old('shipping_city') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Pays</label>
                            <select name="shipping_country"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="FR" {{ old('shipping_country', 'FR') === 'FR' ? 'selected' : '' }}>France</option>
                                <option value="BE" {{ old('shipping_country') === 'BE' ? 'selected' : '' }}>Belgique</option>
                                <option value="CH" {{ old('shipping_country') === 'CH' ? 'selected' : '' }}>Suisse</option>
                                <option value="LU" {{ old('shipping_country') === 'LU' ? 'selected' : '' }}>Luxembourg</option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- Note de commande --}}
                <section>
                    <h2 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                        Note de commande (optionnel)
                    </h2>
                    <textarea name="customer_note" rows="3"
                              placeholder="Instructions particulières pour votre commande..."
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">{{ old('customer_note') }}</textarea>
                </section>
            </div>

            {{-- Colonne droite : récapitulatif --}}
            <aside>
                <div class="bg-gray-50 rounded-xl p-6 sticky top-24">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Récapitulatif</h2>

                    <ul class="space-y-3 mb-4">
                        @foreach($items as $item)
                            <li class="flex justify-between text-sm text-gray-700">
                                <span>
                                    {{ $item['name'] }}
                                    <span class="text-gray-400">× {{ $item['quantity'] }}</span>
                                </span>
                                <span>
                                    {{ number_format(($item['price'] + $item['addon_price']) * $item['quantity'], 2, ',', ' ') }} €
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="border-t border-gray-200 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Sous-total</span>
                            <span>{{ number_format($subtotal, 2, ',', ' ') }} €</span>
                        </div>

                        @if($discount['amount'] > 0)
                            <div class="flex justify-between text-green-700">
                                <span>Remise</span>
                                <span>−{{ number_format($discount['amount'], 2, ',', ' ') }} €</span>
                            </div>
                        @endif

                        <div class="flex justify-between font-semibold text-gray-900 pt-2 border-t border-gray-200 text-base">
                            <span>Total</span>
                            <span>{{ number_format($total, 2, ',', ' ') }} €</span>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-6 w-full bg-green-700 text-white py-3 px-6 rounded font-medium hover:bg-green-800 transition text-sm">
                        Payer avec Stripe →
                    </button>

                    <p class="mt-3 text-xs text-gray-400 text-center">
                        Paiement sécurisé via Stripe
                    </p>
                </div>
            </aside>
        </div>
    </form>
</div>
</x-layouts.app>
