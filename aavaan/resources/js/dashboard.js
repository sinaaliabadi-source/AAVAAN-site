// نقطه‌ی ورود انیمیشن برای لایوت داشبورد.
// این لایوت Alpine.js را از CDN بارگذاری می‌کند، پس اینجا Alpine را import نمی‌کنیم
// تا از دوباره start شدن Alpine جلوگیری شود؛ فقط انیمیشن‌های امن داشبورد اجرا می‌شوند.
import { shouldAnimate } from '@animations/utils/detect.js';
import { initAOS } from '@animations/core/aos-init.js';
import { animateArtistCards } from '@animations/scenes/artist-card.js';
import { initMagneticButtons } from '@animations/utils/magnetic.js';

function boot() {
    if (!shouldAnimate()) return; // احترام به prefers-reduced-motion

    initAOS();
    animateArtistCards();
    initMagneticButtons();
}

// ری‌ست انیمیشن کارت‌ها بعد از دریافت نتایج جدید (fetch در Alpine.js)
window.refreshCardAnimations = function () {
    import('@animations/scenes/artist-card.js').then((m) => m.animateArtistCards());
};

document.addEventListener('DOMContentLoaded', boot);
