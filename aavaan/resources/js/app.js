import Alpine from 'alpinejs';
import { initAnimations } from '@animations/index.js';
import Swiper from 'swiper';
import { Autoplay, Pagination, Navigation, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
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
            pagination: { el: '.hero-swiper .swiper-pagination', clickable: true },
            speed: 800,
        });
    }

    // کروسل هنرمندان صفحه اصلی (سلکتورها مستقل از Hero هستند تا تداخل نکنند)
    if (document.querySelector('.featured-swiper')) {
        new Swiper('.featured-swiper', {
            modules: [Autoplay, Pagination, Navigation],
            loop: true,
            autoplay: { delay: 3500, disableOnInteraction: false },
            pagination: { el: '.featured-swiper .swiper-pagination', clickable: true },
            navigation: {
                nextEl: '.featured-swiper .swiper-button-next',
                prevEl: '.featured-swiper .swiper-button-prev',
            },
            spaceBetween: 20,
            slidesPerView: 1.2,
            breakpoints: {
                640:  { slidesPerView: 2 },
                900:  { slidesPerView: 3 },
                1200: { slidesPerView: 4 },
            },
        });
    }
});

// ری‌ست انیمیشن کارت‌ها بعد از دریافت نتایج جدید (مثلاً fetch در Alpine.js)
window.refreshCardAnimations = function () {
    import('@animations/scenes/artist-card.js').then((m) => m.animateArtistCards());
};
