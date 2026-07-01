import { shouldAnimate } from './utils/detect.js';
import { initLenis } from './core/lenis-scroll.js';
import { initAOS } from './core/aos-init.js';
import { animateHero } from './scenes/hero.js';
import { animateArtistCards } from './scenes/artist-card.js';
import { animateCounters } from './scenes/counter.js';
import { initMagneticButtons } from './utils/magnetic.js';
import { initPageTransitions } from './scenes/page-transition.js';

export function initAnimations() {
    if (!shouldAnimate()) return; // احترام به prefers-reduced-motion

    initLenis();
    initAOS();
    animateHero();
    animateArtistCards();
    animateCounters();
    initMagneticButtons();
    initPageTransitions();
}

// export جداگانه برای صفحات خاص
export { animateHero, animateArtistCards, animateCounters };
