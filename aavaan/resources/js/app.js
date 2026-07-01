import Alpine from 'alpinejs';
import { initAnimations } from '@animations/index.js';
import Swiper from 'swiper';
import { Autoplay, Pagination, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

// راه‌اندازی Alpine.js
window.Alpine = Alpine;
Alpine.start();

// راه‌اندازی انیمیشن‌ها بعد از load صفحه
document.addEventListener('DOMContentLoaded', () => {
    initAnimations();

    // اسلایدر Hero صفحه اصلی
    if (document.querySelector('.hero-swiper')) {
        new Swiper('.hero-swiper', {
            modules: [Autoplay, Pagination, EffectFade],
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            effect: 'fade',
            fadeEffect: { crossFade: true },
            pagination: { el: '.swiper-pagination', clickable: true },
            speed: 800,
        });
    }
});

// ری‌ست انیمیشن کارت‌ها بعد از دریافت نتایج جدید (مثلاً fetch در Alpine.js)
window.refreshCardAnimations = function () {
    import('@animations/scenes/artist-card.js').then((m) => m.animateArtistCards());
};
