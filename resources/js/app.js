import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

let mm = null;

function initAnimations() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    if (mm) {
        mm.revert();
    }
    mm = gsap.matchMedia();

    mm.add('(min-width: 768px)', () => {
        // Footer Parallax on transparent photographic cutout
        const signatureDrink = document.querySelector('.footer-signature-drink');
        if (signatureDrink) {
            gsap.to(signatureDrink, {
                y: -25,
                ease: 'none',
                scrollTrigger: {
                    trigger: '#public-footer',
                    start: 'top bottom',
                    end: 'bottom bottom',
                    scrub: 1,
                },
            });
        }
    });
}

function cleanupAnimations() {
    ScrollTrigger.getAll().forEach((trigger) => trigger.kill());
    if (mm) {
        mm.revert();
        mm = null;
    }
}

// Livewire Navigation Lifecycle Hooks
document.addEventListener('livewire:navigating', () => {
    cleanupAnimations();
});

document.addEventListener('livewire:navigated', () => {
    initAnimations();
    ScrollTrigger.refresh();
});

document.addEventListener('DOMContentLoaded', () => {
    initAnimations();
});
