import { gsap } from '../core/gsap-init.js';

// افکت مغناطیسی روی دکمه‌های CTA
export function initMagneticButtons() {
    const buttons = document.querySelectorAll('[data-magnetic]');
    if (!buttons.length) return;

    buttons.forEach((btn) => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            gsap.to(btn, {
                x: x * 0.3,
                y: y * 0.3,
                duration: 0.4,
                ease: 'power2.out',
            });
        });

        btn.addEventListener('mouseleave', () => {
            gsap.to(btn, {
                x: 0,
                y: 0,
                duration: 0.6,
                ease: 'elastic.out(1, 0.5)',
            });
        });
    });
}
