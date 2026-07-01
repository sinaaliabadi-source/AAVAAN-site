// smooth scroll با Lenis — جایگزین بهتر از scroll-behavior: smooth
import Lenis from '@studio-freight/lenis';

let lenis;

export function initLenis() {
    // فقط روی desktop (روی موبایل native scroll بهتر است)
    if (window.innerWidth < 768) return;

    lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        direction: 'vertical',
        gestureDirection: 'vertical',
        smooth: true,
        smoothTouch: false,
        touchMultiplier: 2,
    });

    // اتصال به GSAP ticker برای هماهنگی
    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    return lenis;
}

export function getLenis() {
    return lenis;
}
