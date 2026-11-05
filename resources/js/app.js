import Lenis from 'lenis';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

let lenis = null;
let mm = null;

function initLenis() {
    // Only smooth scroll on desktop when not preferring reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    if (!lenis) {
        lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            orientation: 'vertical',
            gestureOrientation: 'vertical',
            smoothWheel: true,
            syncTouch: false, // DO NOT hijack touch scroll on mobile devices!
            touchMultiplier: 1.5,
        });

        lenis.on('scroll', ScrollTrigger.update);

        gsap.ticker.add((time) => {
            lenis?.raf(time * 1000);
        });

        gsap.ticker.lagSmoothing(0);
    }
}

function initAnimations() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // Clean up previous animations if any
    if (mm) {
        mm.revert();
    }
    mm = gsap.matchMedia();

    // Desktop Animations
    mm.add('(min-width: 768px)', () => {
        // Subtle Parallax on Signature Drink in Footer
        const signatureDrink = document.querySelector('.footer-signature-drink');
        if (signatureDrink) {
            gsap.to(signatureDrink, {
                y: -30,
                ease: 'none',
                scrollTrigger: {
                    trigger: '#public-footer',
                    start: 'top bottom',
                    end: 'bottom bottom',
                    scrub: 1,
                },
            });
        }

        // Hero subtle reveal
        const heroElements = document.querySelectorAll('.hero-fade-in');
        if (heroElements.length > 0) {
            gsap.from(heroElements, {
                opacity: 0,
                y: 20,
                duration: 0.8,
                stagger: 0.12,
                ease: 'power2.out',
            });
        }
    });

    // Mobile specific: light and smooth
    mm.add('(max-width: 767px)', () => {
        const heroElements = document.querySelectorAll('.hero-fade-in');
        if (heroElements.length > 0) {
            gsap.from(heroElements, {
                opacity: 0,
                y: 10,
                duration: 0.6,
                stagger: 0.08,
                ease: 'power2.out',
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
    initLenis();
    initAnimations();
    ScrollTrigger.refresh();
});

// Initial boot
document.addEventListener('DOMContentLoaded', () => {
    initLenis();
    initAnimations();
});
