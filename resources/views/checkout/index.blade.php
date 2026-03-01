<x-layouts.app title="Finaliser la commande">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Finaliser la commande</h1>

    <form action="{{ route('checkout.store') }}" method="POST"
          x-data="{
              shippingSame: true,
              shippingMethod: '{{ old('shipping_method', 'colissimo') }}',
              shippingPrices: @js(collect($shippingMethods)->mapWithKeys(fn($m, $k) => [$k => $m['price']])),
              subtotal: {{ $subtotal }},
              discountAmount: {{ $discount['amount'] }},
              get shippingCost() { return this.shippingPrices[this.shippingMethod] ?? 0 },
              get total() { return Math.max(0, this.subtotal - this.discountAmount + this.shippingCost) },
              formatPrice(v) { return v.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' \u20ac' },

              // Boxtal relay point
              relayPointCode: '{{ old('relay_point_code') }}',
              relayPointName: '{{ old('relay_point_name') }}',
              relayPointAddress: '{{ old('relay_point_address') }}',
              boxtalMap: null,
              boxtalMapReady: false,

              initBoxtalMap() {
                  if (this.boxtalMap) return;
                  this.$nextTick(() => {
                      const container = document.getElementById('boxtal-map-container');
                      if (!container) return;
                      this.boxtalMap = new BoxtalParcelPointMap.BoxtalParcelPointMap({
                          domToLoadMap: '#boxtal-map-container',
                          accessToken: '{{ config('shipping.boxtal.access_token') }}',
                          config: {
                              locale: 'fr',
                              parcelPointNetworks: @js(collect(config('shipping.boxtal.networks'))->map(fn($n) => ['code' => $n, 'markerTemplate' => ['anchor' => 'bottom', 'color' => '#15803d']])),
                              options: {
                                  autoSelectNearestParcelPoint: true,
                                  primaryColor: '#15803d'
                              }
                          },
                          onMapLoaded: () => {
                              this.boxtalMapReady = true;
                              this.searchRelayPoints();
                          }
                      });
                  });
              },

              searchRelayPoints() {
                  if (!this.boxtalMap || !this.boxtalMapReady) return;
                  const postcode = document.querySelector('[name=billing_postcode]')?.value;
                  const city = document.querySelector('[name=billing_city]')?.value;
                  if (!postcode || !city) return;

                  this.boxtalMap.searchParcelPoints(
                      { country: 'FR', zipCode: postcode, city: city },
                      (point) => {
                          this.relayPointCode = point.code;
                          this.relayPointName = point.name;
                          const loc = point.location;
                          this.relayPointAddress = [loc.street, loc.zipCode, loc.city].filter(Boolean).join(', ');
                      }
                  );
              },

              destroyBoxtalMap() {
                  this.boxtalMap = null;
                  this.boxtalMapReady = false;
                  this.relayPointCode = '';
                  this.relayPointName = '';
                  this.relayPointAddress = '';
              }
          }"
          x-effect="if (shippingMethod === 'boxtal') { initBoxtalMap() } else { destroyBoxtalMap() }">
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
                <section x-show="shippingMethod !== 'pickup'" x-transition>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                        <h2 class="text-base font-semibold text-gray-900">Adresse de livraison</h2>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer"
                               x-show="shippingMethod !== 'boxtal'">
                            <input type="checkbox" name="shipping_same" value="1"
                                   x-model="shippingSame"
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            Identique à la facturation
                        </label>
                    </div>

                    <div x-show="!shippingSame && shippingMethod !== 'boxtal'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                {{-- Mode de livraison --}}
                <section>
                    <h2 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                        Mode de livraison
                    </h2>
                    @error('shipping_method')<p class="text-xs text-red-500 mb-3">{{ $message }}</p>@enderror

                    <div class="space-y-3">
                        @foreach($shippingMethods as $key => $method)
                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition"
                                   :class="shippingMethod === '{{ $key }}'
                                       ? 'border-green-600 bg-green-50 ring-1 ring-green-600'
                                       : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="shipping_method" value="{{ $key }}"
                                           x-model="shippingMethod"
                                           class="text-green-600 focus:ring-green-500"
                                           {{ old('shipping_method', 'colissimo') === $key ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-gray-900">{{ $method['label'] }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">
                                    {{ $method['price'] > 0 ? number_format($method['price'], 2, ',', ' ') . ' €' : 'Gratuit' }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- Carte Boxtal point relais --}}
                <section x-show="shippingMethod === 'boxtal'" x-transition>
                    <h2 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                        Choisissez votre point relais
                    </h2>
                    @error('relay_point_code')<p class="text-xs text-red-500 mb-3">{{ $message }}</p>@enderror

                    <div class="mb-4 flex gap-3">
                        <input type="text" id="relay-search-postcode" placeholder="Code postal"
                               :value="document.querySelector('[name=billing_postcode]')?.value"
                               class="w-32 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <input type="text" id="relay-search-city" placeholder="Ville"
                               :value="document.querySelector('[name=billing_city]')?.value"
                               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <button type="button" @click="
                            const pc = document.getElementById('relay-search-postcode').value;
                            const ct = document.getElementById('relay-search-city').value;
                            if (pc && ct && boxtalMap && boxtalMapReady) {
                                boxtalMap.searchParcelPoints(
                                    { country: 'FR', zipCode: pc, city: ct },
                                    (point) => {
                                        relayPointCode = point.code;
                                        relayPointName = point.name;
                                        const loc = point.location;
                                        relayPointAddress = [loc.street, loc.zipCode, loc.city].filter(Boolean).join(', ');
                                    }
                                );
                            }
                        " class="bg-green-700 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-800 transition">
                            Rechercher
                        </button>
                    </div>

                    <div id="boxtal-map-container" class="w-full rounded-lg border border-gray-200 overflow-hidden" style="height: 450px;"></div>

                    {{-- Selected relay point info --}}
                    <div x-show="relayPointName" x-transition
                         class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm font-semibold text-green-800" x-text="relayPointName"></p>
                        <p class="text-sm text-green-700" x-text="relayPointAddress"></p>
                    </div>

                    <input type="hidden" name="relay_point_code" :value="relayPointCode">
                    <input type="hidden" name="relay_point_name" :value="relayPointName">
                    <input type="hidden" name="relay_point_address" :value="relayPointAddress">
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

                        <div class="flex justify-between text-gray-600">
                            <span>Livraison</span>
                            <span x-text="shippingCost > 0 ? formatPrice(shippingCost) : 'Gratuit'"></span>
                        </div>

                        <div class="flex justify-between font-semibold text-gray-900 pt-2 border-t border-gray-200 text-base">
                            <span>Total</span>
                            <span x-text="formatPrice(total)"></span>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-6 w-full bg-green-700 text-white py-3 px-6 rounded font-medium hover:bg-green-800 transition text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="shippingMethod === 'boxtal' && !relayPointCode">
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

{{-- Boxtal Map JS (loaded only when needed) --}}
<script src="https://cdn.jsdelivr.net/npm/@boxtal/parcel-point-map@0.0.9/dist/index.global.js"></script>
</x-layouts.app>
