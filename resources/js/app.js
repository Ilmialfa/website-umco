import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { initReveal, cleanupReveal } from './motion/reveal.js';
import { initNav } from './motion/nav.js';
import { terbilang, formatRupiah, formatRibu, loadNota, saveNota } from './lib/formatters.js';

gsap.registerPlugin(ScrollTrigger);

window.umcoFormatters = { terbilang, formatRupiah, formatRibu, loadNota, saveNota };

const modules = {
    'scroll-meter': () => import('./sections/scroll-meter.js'),
    'hero': () => import('./sections/hero.js'),
    'led-board': () => import('./sections/led-board.js'),
    'signature-reel': () => import('./sections/signature-reel.js'),
    'story-line': () => import('./sections/story-line.js'),
    'brew-toy': () => import('./sections/brew-toy.js'),
    'crowd-chart': () => import('./sections/crowd-chart.js'),
    'testimonials-slider': () => import('./sections/testimonials-slider.js'),
    'gallery-mosaic': () => import('./sections/gallery-mosaic.js'),
    'altimeter': () => import('./sections/altimeter.js'),
};

let io = null;
let cleanups = [];
let lenisInstance = null;
let rafCallback = null;

function initFooter() {
    const footer = document.getElementById('public-footer');
    if (!footer) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) return;

    const wordmark = footer.querySelector('[data-footer-wordmark]');
    if (wordmark) {
        gsap.fromTo(
            wordmark,
            { fontVariationSettings: '"wdth" 60, "wght" 800' },
            {
                fontVariationSettings: '"wdth" 100, "wght" 800',
                ease: 'none',
                scrollTrigger: {
                    trigger: footer,
                    start: 'top bottom',
                    end: 'bottom bottom',
                    scrub: 0.6,
                },
            }
        );
    }

    const cutout = footer.querySelector('[data-footer-cutout]');
    if (cutout && window.innerWidth >= 1024) {
        gsap.fromTo(
            cutout,
            { y: 18 },
            {
                y: -16,
                ease: 'none',
                scrollTrigger: {
                    trigger: footer,
                    start: 'top bottom',
                    end: 'bottom bottom',
                    scrub: 0.8,
                },
            }
        );
    }
}

async function initLenisIfEnabled() {
    const enabled = document.documentElement.dataset.smoothScroll === '1';
    const desktopFinePointer = window.matchMedia('(min-width: 1024px) and (pointer: fine)').matches;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!enabled || !desktopFinePointer || reduced) return;

    const { default: Lenis } = await import('lenis');
    lenisInstance = new Lenis({ lerp: 0.1 });
    lenisInstance.on('scroll', ScrollTrigger.update);
    rafCallback = (time) => lenisInstance?.raf(time * 1000);
    gsap.ticker.add(rafCallback);
    gsap.ticker.lagSmoothing(0);
}

function initSectionModules() {
    const targets = document.querySelectorAll('[data-module]');
    if (!targets.length) return;

    io = new IntersectionObserver(
        (entries) => {
            entries.forEach(async (entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                io?.unobserve(el);

                const key = el.dataset.module;
                const loader = modules[key];
                if (!loader) return;

                try {
                    const mod = await loader();
                    const destroy = mod?.default?.(el);
                    if (typeof destroy === 'function') {
                        cleanups.push(destroy);
                    }
                } catch {}
            });
        },
        { rootMargin: '400px 0px' }
    );

    targets.forEach((el) => io.observe(el));
}

function bootPage() {
    const navDestroy = initNav();
    if (typeof navDestroy === 'function') cleanups.push(navDestroy);

    initReveal();
    initFooter();
    initSectionModules();
    initLenisIfEnabled();
}

function teardownPage() {
    if (io) {
        io.disconnect();
        io = null;
    }

    cleanups.forEach((fn) => {
        try {
            fn();
        } catch {}
    });
    cleanups = [];

    if (lenisInstance) {
        if (rafCallback) gsap.ticker.remove(rafCallback);
        lenisInstance.destroy();
        lenisInstance = null;
        rafCallback = null;
    }

    cleanupReveal();
    ScrollTrigger.getAll().forEach((t) => t.kill());
}

document.addEventListener('livewire:navigating', () => {
    const skel = document.getElementById('umco-nav-skeleton');
    if (skel) skel.hidden = false;
    teardownPage();
});

document.addEventListener('livewire:navigated', () => {
    const skel = document.getElementById('umco-nav-skeleton');
    if (skel) skel.hidden = true;
    bootPage();
    ScrollTrigger.refresh();
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootPage);
} else {
    bootPage();
}
