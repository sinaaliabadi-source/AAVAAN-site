import Alpine from 'alpinejs';
import { initAnimations } from '@animations/index.js';

// راه‌اندازی Alpine.js
window.Alpine = Alpine;
Alpine.start();

// راه‌اندازی انیمیشن‌ها بعد از load صفحه
document.addEventListener('DOMContentLoaded', () => {
    initAnimations();
});
