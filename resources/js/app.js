import './bootstrap';

// Turbo Hotwire
import * as Turbo from '@hotwired/turbo';
Turbo.start();

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Auto-open mini cart when a product is added via Turbo Stream
document.addEventListener('turbo:before-stream-render', (event) => {
    const stream = event.target;
    if (stream.getAttribute('target') === 'cart-count' && stream.getAttribute('action') === 'update') {
        requestAnimationFrame(() => window.dispatchEvent(new CustomEvent('cart-added')));
    }
});
