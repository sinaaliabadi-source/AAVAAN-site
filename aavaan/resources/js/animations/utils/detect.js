// ابزارهای تشخیص محیط
export const isMobile = () => window.innerWidth < 768;
export const isReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;
export const isTouch = () =>
    'ontouchstart' in window || navigator.maxTouchPoints > 0;

// اگر کاربر reduced motion خواسته، انیمیشن نشان نده
export function shouldAnimate() {
    return !isReducedMotion();
}
