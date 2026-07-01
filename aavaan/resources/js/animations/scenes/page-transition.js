import { gsap } from '../core/gsap-init.js';

// تغییر صفحه نرم — fade کوتاه هنگام ورود و خروج از صفحه
export function initPageTransition() {
    if (typeof document === 'undefined') return;

    // ورود نرم صفحه
    gsap.from('main', {
        opacity: 0,
        duration: 0.5,
        ease: 'avan-ease',
    });

    // خروج نرم هنگام کلیک روی لینک‌های داخلی
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href]');
        if (!link) return;

        const url = link.getAttribute('href');
        // فقط لینک‌های داخلی (نه دانلود، نه tab جدید، نه anchor)
        if (
            !url ||
            url.startsWith('#') ||
            url.startsWith('mailto:') ||
            url.startsWith('tel:') ||
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.hostname !== window.location.hostname
        ) {
            return;
        }

        e.preventDefault();
        gsap.to('main', {
            opacity: 0,
            duration: 0.3,
            ease: 'avan-ease',
            onComplete: () => {
                window.location.href = url;
            },
        });
    });
}
