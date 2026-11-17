import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { initReveal, cleanupReveal } from './motion/reveal.js';
import { initNav } from './motion/nav.js';
import { terbilang, formatRupiah, formatRibu, loadNota, saveNota, buildReserveUrl } from './lib/formatters.js';
import { flyToNota } from './lib/nota-fly.js';

gsap.registerPlugin(ScrollTrigger);

const scrollLocks = new Set();

function applyScrollLockState() {
    const locked = scrollLocks.size > 0;
    document.documentElement.classList.toggle('overflow-hidden', locked);
    document.documentElement.style.overflow = locked ? 'hidden' : '';
    if (document.body) {
        document.body.classList.toggle('overflow-hidden', locked);
        document.body.style.overflowY = locked ? 'hidden' : '';
    }
    if (lenisInstance) {
        if (locked) {
            lenisInstance.stop();
        } else {
            lenisInstance.start();
        }
    }
}

window.umcoScrollLock = (key, locked) => {
    const lockKey = String(key || 'default');
    if (locked) {
        scrollLocks.add(lockKey);
    } else {
        scrollLocks.delete(lockKey);
    }
    applyScrollLockState();
};

function clearAllScrollLocks() {
    scrollLocks.clear();
    applyScrollLockState();
}

window.umcoFormatters = { terbilang, formatRupiah, formatRibu, loadNota, saveNota, buildReserveUrl };
window.umcoFlyToNota = flyToNota;

const modules = {
    'hero': () => import('./sections/hero.js'),
    'marquee-collage': () => import('./sections/marquee-collage.js'),
    'signature-reel': () => import('./sections/signature-reel.js'),
    'gallery-diagonal': () => import('./sections/gallery-diagonal.js'),
    'testimonials-parallax': () => import('./sections/testimonials-parallax.js'),
    'gallery-mosaic': () => import('./sections/gallery-mosaic.js'),
    'reservasi-tools': () => import('./sections/reservasi-tools.js'),
    'location-map': () => import('./sections/location-map.js'),
};

let io = null;
let loopIo = null;
let resizeObs = null;
let resizeTimer = null;
let resizeRaf = 0;
let cleanups = [];
let lenisInstance = null;
let rafCallback = null;

function initFooter() {
    const footer = document.getElementById('public-footer');
    if (!footer) return;

    const wordmark = footer.querySelector('[data-footer-wordmark]');
    const cutout = footer.querySelector('[data-footer-cutout]');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) {
        if (wordmark) gsap.set(wordmark, { scaleX: 1, letterSpacing: '-0.02em', y: 0, opacity: 1 });
        return;
    }

    if (wordmark) {
        gsap.set(wordmark, { scaleX: 0.72, letterSpacing: '-0.04em', y: 36, opacity: 0.3, transformOrigin: '50% 80%' });
        gsap.to(wordmark, {
            scaleX: 1.0,
            letterSpacing: '0.005em',
            y: 0,
            opacity: 1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: footer,
                start: 'top 95%',
                end: 'bottom bottom',
                scrub: 1.4,
            },
        });
    }

    if (cutout) {
        gsap.set(cutout, { y: 28, opacity: 0.8 });
        gsap.to(cutout, {
            y: 0,
            opacity: 1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: footer,
                start: 'top 92%',
                end: 'bottom 96%',
                scrub: 1.1,
            },
        });
    }
}

async function initLenisIfEnabled() {
    const enabled = (document.body?.dataset.smoothScroll || document.documentElement.dataset.smoothScroll) !== '0';
    const desktopFinePointer = window.matchMedia('(min-width: 1024px) and (pointer: fine)').matches;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!enabled || !desktopFinePointer || reduced) return;

    const { default: Lenis } = await import('lenis');
    lenisInstance = new Lenis({
        lerp: 0.085,
        smoothWheel: true,
        wheelMultiplier: 0.96,
        touchMultiplier: 1.35,
        autoResize: true,
    });
    if (scrollLocks.size > 0) {
        lenisInstance.stop();
    }
    lenisInstance.on('scroll', ScrollTrigger.update);
    rafCallback = (time) => lenisInstance?.raf(time * 1000);
    gsap.ticker.add(rafCallback);
    gsap.ticker.lagSmoothing(0);

    // Sinkronkan batas gulir Lenis secara otomatis saat tinggi dokumen berubah (misal pindah step /reservasi)
    if (typeof ResizeObserver !== 'undefined' && document.body) {
        resizeObs = new ResizeObserver(() => {
            if (resizeRaf) cancelAnimationFrame(resizeRaf);
            resizeRaf = requestAnimationFrame(() => {
                resizeRaf = 0;
                lenisInstance?.resize();
            });

            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(() => {
                resizeTimer = null;
                ScrollTrigger.refresh();
            }, 180);
        });
        resizeObs.observe(document.body);
    }
}

function initOffscreenMotionPause() {
    const animated = document.querySelectorAll('.umco-marquee-track, .umco-col-loop-up, .umco-col-loop-down, .umco-vapor-wisp');
    if (!animated.length || typeof IntersectionObserver === 'undefined') return;

    loopIo = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                entry.target.dataset.loopPaused = entry.isIntersecting ? '0' : '1';
            });
        },
        { rootMargin: '120px 0px' }
    );
    animated.forEach((el) => loopIo.observe(el));
}

function initSectionModules() {
    const targets = document.querySelectorAll('[data-module]');
    if (!targets.length) return;

    const loadModuleEl = async (el) => {
        const key = el.dataset.module;
        const loader = modules[key];
        if (!loader || el.dataset.moduleLoaded === '1') return;
        el.dataset.moduleLoaded = '1';

        try {
            const mod = await loader();
            const initFn = mod?.default || mod?.init;
            const destroy = typeof initFn === 'function' ? initFn(el) : undefined;
            if (typeof destroy === 'function') {
                cleanups.push(destroy);
            }
        } catch {}
    };

    const lazyTargets = [];
    targets.forEach((el) => {
        if (el.dataset.eager === '1') {
            loadModuleEl(el);
        } else {
            lazyTargets.push(el);
        }
    });

    if (!lazyTargets.length) return;

    io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                io?.unobserve(el);
                loadModuleEl(el);
            });
        },
        { rootMargin: '400px 0px' }
    );

    lazyTargets.forEach((el) => io.observe(el));
}

function scheduleReservasiWarmPrefetch() {
    if (document.body?.dataset.page === 'reservasi') return;

    const triggerPrefetch = () => {
        if (document.visibilityState === 'hidden') return;
        const link = document.querySelector('a.nav-reservasi-pill[wire\\:navigate\\.hover], a[href$="/reservasi"][wire\\:navigate\\.hover]');
        if (!link) return;
        link.dispatchEvent(new MouseEvent('mouseenter', { bubbles: false }));
        window.setTimeout(() => {
            link.dispatchEvent(new MouseEvent('mouseleave', { bubbles: false }));
        }, 80);
    };

    const startTimer = window.setTimeout(triggerPrefetch, 220);
    const keepWarmTimer = window.setInterval(triggerPrefetch, 24000);
    cleanups.push(() => {
        clearTimeout(startTimer);
        clearInterval(keepWarmTimer);
    });
}

function bootPage() {
    const navDestroy = initNav();
    if (typeof navDestroy === 'function') cleanups.push(navDestroy);

    initReveal();
    initFooter();
    initSectionModules();
    initOffscreenMotionPause();
    initLenisIfEnabled();
    scheduleReservasiWarmPrefetch();
}

function teardownPage() {
    if (io) {
        io.disconnect();
        io = null;
    }

    if (loopIo) {
        loopIo.disconnect();
        loopIo = null;
    }

    if (resizeRaf) {
        cancelAnimationFrame(resizeRaf);
        resizeRaf = 0;
    }

    if (resizeTimer) {
        clearTimeout(resizeTimer);
        resizeTimer = null;
    }

    if (resizeObs) {
        resizeObs.disconnect();
        resizeObs = null;
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

    clearAllScrollLocks();
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
