import { gsap, ScrollTrigger } from '../core/gsap-init.js';

export function animateHero() {
    const hero = document.querySelector('[data-animate="hero"]');
    if (!hero) return;

    // timeline اصلی Hero
    const tl = gsap.timeline({
        defaults: { ease: 'avan-ease', duration: 0.9 }
    });

    // عنوان را حرف‌به‌حرف وارد کن
    tl.from('[data-hero-title]', {
        y: 60,
        opacity: 0,
        duration: 1.1,
    })
    .from('[data-hero-subtitle]', {
        y: 40,
        opacity: 0,
        duration: 0.8,
    }, '-=0.5')
    .from('[data-hero-cta]', {
        y: 30,
        opacity: 0,
        stagger: 0.15,
        duration: 0.7,
    }, '-=0.4')
    .from('[data-hero-badge]', {
        scale: 0.8,
        opacity: 0,
        duration: 0.5,
    }, '-=0.5');

    // پارالاکس هنگام scroll
    ScrollTrigger.create({
        trigger: hero,
        start: 'top top',
        end: 'bottom top',
        scrub: 1,
        onUpdate: (self) => {
            gsap.to('[data-hero-bg]', {
                y: self.progress * 80,
                duration: 0,
            });
        },
    });
}
