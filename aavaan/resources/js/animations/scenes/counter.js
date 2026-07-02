import { gsap, ScrollTrigger } from '../core/gsap-init.js';

// انیمیشن شمارنده‌ی عددی (مثلاً "۲۲ رشته هنری"، "۱۰۰۰+ هنرمند")
export function animateCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;

    counters.forEach((counter) => {
        const target = parseInt(counter.dataset.counter, 10);
        const obj = { value: 0 };

        ScrollTrigger.create({
            trigger: counter,
            start: 'top 85%',
            once: true,
            onEnter: () => {
                gsap.to(obj, {
                    value: target,
                    duration: 2,
                    ease: 'power2.out',
                    onUpdate: () => {
                        // تبدیل به اعداد فارسی
                        counter.textContent = Math.ceil(obj.value)
                            .toLocaleString('fa-IR');
                    },
                });
            },
        });
    });
}
