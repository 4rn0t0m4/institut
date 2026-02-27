<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)"
     x-show="show" x-transition.opacity
     class="bg-green-50 border border-green-200 text-green-800 text-sm rounded px-4 py-2 mb-4">
    ✓ « {{ $product->name }} » ajouté au panier.
</div>
