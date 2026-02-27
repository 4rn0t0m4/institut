import './bootstrap';

// Turbo Hotwire
import * as Turbo from '@hotwired/turbo';
Turbo.start();

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
