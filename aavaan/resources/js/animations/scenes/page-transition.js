import { gsap } from '../core/gsap-init.js';

export function initPageTransitions() {
    // Overlay برای transition
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed; inset: 0;
        background: #1F2A44;
        transform: translateY(100%);
        z-index: 9999;
        pointer-events: none;
    `;
    document.body.appendChild(overlay);

    // ورود صفحه
    gsap.to(overlay, {
        translateY: '-100%',
        duration: 0.7,
        ease: 'power3.inOut',
        onComplete: () => overlay.remove(),
    });

    // خروج از صفحه (قبل از navigation)
    document.querySelectorAll('a[href]:not([target="_blank"]):not([data-no-transition])').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript')) return;

            // فقط لینک‌های داخلی همین دامنه
            if (link.hostname && link.hostname !== window.location.hostname) return;
            if (href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (link.hasAttribute('download')) return;

            e.preventDefault();

            const newOverlay = document.createElement('div');
            newOverlay.style.cssText = overlay.style.cssText;
            newOverlay.style.transform = 'translateY(100%)';
            document.body.appendChild(newOverlay);

            gsap.to(newOverlay, {
                translateY: '0%',
                duration: 0.5,
                ease: 'power3.inOut',
                onComplete: () => {
                    window.location.href = href;
                },
            });
        });
    });
}
