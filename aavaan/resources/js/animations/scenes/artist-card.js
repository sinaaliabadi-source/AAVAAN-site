import { gsap, ScrollTrigger } from '../core/gsap-init.js';

export function animateArtistCards() {
    const cards = document.querySelectorAll('[data-animate="artist-card"]');
    if (!cards.length) return;

    // ورود کارت‌ها هنگام scroll
    gsap.from(cards, {
        y: 50,
        opacity: 0,
        duration: 0.7,
        stagger: 0.12,
        ease: 'avan-ease',
        scrollTrigger: {
            trigger: cards[0].parentElement,
            start: 'top 80%',
            toggleActions: 'play none none none',
        },
    });

    // hover effect برای هر کارت
    cards.forEach((card) => {
        const img = card.querySelector('[data-card-img]');
        const overlay = card.querySelector('[data-card-overlay]');

        card.addEventListener('mouseenter', () => {
            gsap.to(img, { scale: 1.05, duration: 0.4, ease: 'power2.out' });
            gsap.to(overlay, { opacity: 1, duration: 0.3 });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(img, { scale: 1, duration: 0.4, ease: 'power2.out' });
            gsap.to(overlay, { opacity: 0, duration: 0.3 });
        });
    });
}
