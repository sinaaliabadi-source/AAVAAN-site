import Alpine from 'alpinejs';
import { initAnimations } from '@animations/index.js';

// راه‌اندازی Alpine.js
window.Alpine = Alpine;
Alpine.start();

// راه‌اندازی انیمیشن‌ها بعد از load صفحه
document.addEventListener('DOMContentLoaded', () => {
    initAnimations();
});

// ری‌ست انیمیشن کارت‌ها بعد از دریافت نتایج جدید (مثلاً fetch در Alpine.js)
window.refreshCardAnimations = function () {
    import('@animations/scenes/artist-card.js').then((m) => m.animateArtistCards());
};
