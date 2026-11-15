import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

let splits = [];
let ctx = null;

function isNearTopViewport(el) {
    const rect = el.getBoundingClientRect();
    return window.scrollY < 48 && rect.top < window.innerHeight * 0.92;
}

export function initReveal() {
    cleanupReveal();

    const els = Array.from(document.querySelectorAll('[data-reveal]'));
    if (!els.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = window.innerWidth < 640;

    if (reduced) {
        els.forEach((el) => gsap.set(el, { opacity: 1, y: 0, autoAlpha: 1 }));
        return;
    }

    ctx = gsap.context(() => {
        document.querySelectorAll('[data-reveal-stagger]').forEach((parent) => {
            const children = Array.from(parent.querySelectorAll(':scope > [data-reveal]'));
            if (!children.length) return;

            children.forEach((c) => {
                c.dataset.revealHandled = '1';
            });

            const yIn = isMobile ? 12 : 20;
            const heroVisible = isNearTopViewport(parent);

            if (heroVisible) {
                gsap.fromTo(children, { opacity: 0, y: yIn }, { opacity: 1, y: 0, duration: 0.6, stagger: 0.06, ease: 'power3.out' });
            } else {
                gsap.set(children, { opacity: 0, y: yIn });
            }

            ScrollTrigger.create({
                trigger: parent,
                start: 'top 90%',
                end: 'bottom -6%',
                onEnter: () => gsap.to(children, { opacity: 1, y: 0, duration: 0.55, stagger: 0.06, ease: 'power3.out', overwrite: 'auto' }),
                onEnterBack: () => gsap.to(children, { opacity: 1, y: 0, duration: 0.45, stagger: 0.04, ease: 'power3.out', overwrite: 'auto' }),
                onLeave: () => gsap.to(children, { opacity: 0, y: isMobile ? -6 : -10, duration: 0.25, ease: 'power2.out', overwrite: 'auto' }),
                onLeaveBack: () => gsap.to(children, { opacity: 0, y: yIn, duration: 0.25, ease: 'power2.out', overwrite: 'auto' }),
            });
        });

        els.forEach((el) => {
            if (el.dataset.revealHandled === '1') return;

            const type = el.dataset.reveal;
            const heroVisible = isNearTopViewport(el);
            const stBounds = {
                trigger: el,
                start: 'top 90%',
                end: 'bottom -6%',
            };

            if (type === 'fade') {
                if (heroVisible) {
                    gsap.fromTo(el, { opacity: 0 }, { opacity: 1, duration: 0.6, ease: 'power3.out', delay: 0.12 });
                } else {
                    gsap.set(el, { opacity: 0 });
                }
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(el, { opacity: 1, duration: 0.55, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, duration: 0.45, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(el, { opacity: 0, duration: 0.25, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, duration: 0.25, overwrite: 'auto' }),
                });
            } else if (type === 'rise') {
                const yIn = isMobile ? 12 : 20;
                if (heroVisible) {
                    gsap.fromTo(el, { opacity: 0, y: yIn }, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out' });
                } else {
                    gsap.set(el, { opacity: 0, y: yIn });
                }
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.45, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(el, { opacity: 0, y: isMobile ? -6 : -10, duration: 0.25, ease: 'power2.out', overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, y: yIn, duration: 0.25, ease: 'power2.out', overwrite: 'auto' }),
                });
            } else if (type === 'lines') {
                const split = SplitText.create(el, { type: 'lines', aria: 'none' });
                splits.push(split);
                if (heroVisible) {
                    gsap.fromTo(split.lines, { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.7, stagger: 0.07, ease: 'power3.out' });
                } else {
                    gsap.set(split.lines, { y: 20, opacity: 0 });
                }
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(split.lines, { y: 0, opacity: 1, duration: 0.65, stagger: 0.07, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(split.lines, { y: 0, opacity: 1, duration: 0.5, stagger: 0.05, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(split.lines, { opacity: 0, y: -8, duration: 0.25, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(split.lines, { opacity: 0, y: 20, duration: 0.25, overwrite: 'auto' }),
                });
            } else if (type === 'mask') {
                const img = el.querySelector('img');
                if (heroVisible) {
                    gsap.fromTo(el, { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out' });
                } else {
                    gsap.set(el, { opacity: 0, y: 16 });
                    if (img) gsap.set(img, { scale: 1.04 });
                }

                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => {
                        gsap.to(el, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out', overwrite: 'auto' });
                        if (img) gsap.to(img, { scale: 1, duration: 0.9, ease: 'power3.out', overwrite: 'auto' });
                    },
                    onEnterBack: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.45, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(el, { opacity: 0, duration: 0.25, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, y: 16, duration: 0.25, overwrite: 'auto' }),
                });
            }
        });
    });

    if (document.fonts?.ready) {
        document.fonts.ready.then(() => ScrollTrigger.refresh());
    }
}

export function cleanupReveal() {
    if (ctx) {
        ctx.revert();
        ctx = null;
    }
    splits.forEach((s) => {
        try { s.revert(); } catch {}
    });
    splits = [];
}
