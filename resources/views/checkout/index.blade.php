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
              relayPoints: [],
              relayLoading: false,
              relaySearched: false,

              async searchRelayPoints(zipCode, city) {
                  if (!zipCode || !city) return;
                  this.relayLoading = true;
                  this.relaySearched = false;
                  try {
                      const res = await fetch('{{ route('boxtal.parcel-points') }}', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                          },
                          body: JSON.stringify({ zipCode: zipCode, city: city, country: 'FR' })
                      });
                      const data = await res.json();
                      this.relayPoints = data.points || [];
                  } catch (e) {
                      this.relayPoints = [];
                  }
                  this.relayLoading = false;
                  this.relaySearched = true;
                  if (this.relayPoints.length > 0) {
                      this.$nextTick(() => window.__relayMap.render(this));
                  }
              },

              selectRelayPoint(point) {
                  this.relayPointCode = point.code;
                  this.relayPointName = point.name;
                  this.relayPointAddress = [point.street, point.zipCode, point.city].filter(Boolean).join(', ');
                  window.__relayMap.highlight(point);
              },

              resetRelay() {
                  this.relayPoints = [];
                  this.relayPointCode = '';
                  this.relayPointName = '';
                  this.relayPointAddress = '';
                  this.relaySearched = false;
                  window.__relayMap.destroy();
              }
          }"
          x-effect="if (shippingMethod !== 'boxtal') { resetRelay() }">
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

                {{-- Points relais Boxtal --}}
                <section x-show="shippingMethod === 'boxtal'" x-transition>
                    <h2 class="text-base font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">
                        Choisissez votre point relais
                    </h2>
                    @error('relay_point_code')<p class="text-xs text-red-500 mb-3">{{ $message }}</p>@enderror

                    <div class="mb-4 flex gap-3">
                        <input type="text" x-ref="relayZip" placeholder="Code postal"
                               :value="$el.value || document.querySelector('[name=billing_postcode]')?.value || ''"
                               class="w-32 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <input type="text" x-ref="relayCity" placeholder="Ville"
                               :value="$el.value || document.querySelector('[name=billing_city]')?.value || ''"
                               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <button type="button"
                                @click="searchRelayPoints($refs.relayZip.value, $refs.relayCity.value)"
                                :disabled="relayLoading"
                                class="bg-green-700 text-white px-4 py-2 rounded text-sm font-medium hover:bg-green-800 transition disabled:opacity-50">
                            <span x-show="!relayLoading">Rechercher</span>
                            <span x-show="relayLoading" class="flex items-center gap-1">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Recherche…
                            </span>
                        </button>
                    </div>

                    {{-- Carte + Liste côte à côte --}}
                    <div x-show="relayPoints.length > 0" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Liste des points relais --}}
                        <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100 order-2 sm:order-1">
                            <template x-for="(point, idx) in relayPoints" :key="point.code">
                                <label class="flex items-start gap-3 p-3 cursor-pointer transition border-l-4"
                                       :class="relayPointCode === point.code
                                           ? (point.network === 'CHRP_NETWORK' ? 'bg-blue-50 border-l-blue-600' : 'bg-green-50 border-l-green-600')
                                           : 'hover:bg-gray-50 border-l-transparent'"
                                       :id="'relay-item-' + point.code">
                                    <input type="radio" name="_relay_select"
                                           :value="point.code"
                                           :checked="relayPointCode === point.code"
                                           @change="selectRelayPoint(point)"
                                           class="mt-1 text-green-600 focus:ring-green-500">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-white text-xs font-bold shrink-0"
                                                  :class="point.network === 'CHRP_NETWORK' ? 'bg-blue-600' : 'bg-green-700'"
                                                  x-text="idx + 1"></span>
                                            <span class="text-sm font-medium text-gray-900" x-text="point.name"></span>
                                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full leading-none"
                                                  :class="point.network === 'CHRP_NETWORK' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'"
                                                  x-text="point.networkLabel"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 ml-7" x-text="[point.street, point.zipCode + ' ' + point.city].filter(Boolean).join(', ')"></p>
                                    </div>
                                </label>
                            </template>
                        </div>

                        {{-- Carte MapLibre --}}
                        <div class="order-1 sm:order-2">
                            <div id="relay-map" class="w-full rounded-lg border border-gray-200 overflow-hidden" style="height: 384px;"></div>
                        </div>
                    </div>

                    {{-- Aucun résultat --}}
                    <div x-show="relaySearched && relayPoints.length === 0 && !relayLoading" x-transition
                         class="p-4 text-sm text-gray-500 text-center border border-gray-200 rounded-lg">
                        Aucun point relais trouvé pour cette recherche. Essayez avec une autre ville ou code postal.
                    </div>

                    {{-- Point sélectionné --}}
                    <div x-show="relayPointName" x-transition
                         class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-800">
                            <span class="font-semibold">Point relais sélectionné :</span>
                            <span x-text="relayPointName"></span>
                        </p>
                        <p class="text-xs text-green-700 mt-1" x-text="relayPointAddress"></p>
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

<script>
window.__relayMap = (function() {
    var map = null;
    var markers = [];

    function loadMapLibre(cb) {
        if (window.maplibregl) { cb(); return; }
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css';
        document.head.appendChild(link);
        var script = document.createElement('script');
        script.src = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js';
        script.onload = cb;
        document.head.appendChild(script);
    }

    function initMap(alpine) {
        var container = document.getElementById('relay-map');
        if (!container) return;
        if (map) { map.remove(); map = null; }
        markers = [];

        var points = alpine.relayPoints;
        var bounds = new maplibregl.LngLatBounds();
        points.forEach(function(p) {
            if (p.lat && p.lng) bounds.extend([p.lng, p.lat]);
        });

        map = new maplibregl.Map({
            container: container,
            style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
            bounds: bounds,
            fitBoundsOptions: { padding: 40, maxZoom: 14 }
        });
        map.addControl(new maplibregl.NavigationControl(), 'top-right');

        points.forEach(function(point, idx) {
            if (!point.lat || !point.lng) return;

            var isChrono = point.network === 'CHRP_NETWORK';
            var el = document.createElement('div');
            el.className = 'relay-marker' + (isChrono ? ' relay-marker--chrono' : '');
            el.textContent = idx + 1;
            el.addEventListener('click', function() {
                alpine.selectRelayPoint(point);
                var item = document.getElementById('relay-item-' + point.code);
                if (item) item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });

            var badge = isChrono ? 'Chronopost' : 'Mondial Relay';
            var popupHtml = '<span class="relay-popup-badge' + (isChrono ? ' relay-popup-badge--chrono' : '') + '">' + badge + '</span>' +
                '<strong class="relay-popup-name">' +
                point.name + '</strong><br><span class="relay-popup-addr">' +
                point.street + '<br>' + point.zipCode + ' ' + point.city + '</span>';

            var marker = new maplibregl.Marker({ element: el })
                .setLngLat([point.lng, point.lat])
                .setPopup(new maplibregl.Popup({ offset: 20 }).setHTML(popupHtml))
                .addTo(map);

            markers.push({ code: point.code, el: el, marker: marker });
        });
    }

    return {
        render: function(alpine) {
            loadMapLibre(function() { initMap(alpine); });
        },
        highlight: function(point) {
            if (map && point.lat && point.lng) {
                map.flyTo({ center: [point.lng, point.lat], zoom: 15 });
            }
            markers.forEach(function(m) {
                m.el.style.opacity = m.code === point.code ? '1' : '0.5';
                m.el.style.transform = m.code === point.code ? 'scale(1.3)' : 'scale(1)';
            });
        },
        destroy: function() {
            if (map) { map.remove(); map = null; }
            markers = [];
        }
    };
})();
</script>
<style>
.relay-marker {
    width: 28px; height: 28px; border-radius: 50%;
    background: #15803d; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,.3);
    transition: transform .2s, opacity .2s;
}
.relay-marker--chrono { background: #2563eb; }
.relay-popup-badge {
    display: inline-block; font-size: 10px; font-weight: 600;
    padding: 1px 6px; border-radius: 9px; margin-bottom: 4px;
    background: #dcfce7; color: #15803d;
}
.relay-popup-badge--chrono { background: #dbeafe; color: #2563eb; }
.relay-popup-name { font-size: 13px; display: block; }
.relay-popup-addr { font-size: 12px; color: #666; }
</style>
</x-layouts.app>
