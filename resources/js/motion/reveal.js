import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

let splits = [];
let ctx = null;

function inViewport(el) {
    const rect = el.getBoundingClientRect();
    return rect.bottom > 0 && rect.top < window.innerHeight * 0.94;
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
        // Stagger groups: tampil mulus saat masuk viewport, tetap terlihat sepanjang di layar
        document.querySelectorAll('[data-reveal-stagger]').forEach((parent) => {
            const children = Array.from(parent.querySelectorAll(':scope > [data-reveal]'));
            if (!children.length) return;

            children.forEach((c) => {
                c.dataset.revealHandled = '1';
            });

            const yIn = isMobile ? 12 : 20;
            const visibleNow = inViewport(parent);
            gsap.set(children, visibleNow ? { opacity: 1, y: 0 } : { opacity: 0, y: yIn });

            ScrollTrigger.create({
                trigger: parent,
                start: 'top 93%',
                end: 'bottom -5%',
                onEnter: () => gsap.to(children, { opacity: 1, y: 0, duration: 0.55, stagger: 0.06, ease: 'power3.out', overwrite: 'auto' }),
                onEnterBack: () => gsap.to(children, { opacity: 1, y: 0, duration: 0.45, stagger: 0.04, ease: 'power3.out', overwrite: 'auto' }),
                onLeave: () => gsap.to(children, { opacity: 0, y: isMobile ? -6 : -10, duration: 0.25, ease: 'power2.out', overwrite: 'auto' }),
                onLeaveBack: () => gsap.to(children, { opacity: 0, y: yIn, duration: 0.25, ease: 'power2.out', overwrite: 'auto' }),
            });
        });

        els.forEach((el) => {
            if (el.dataset.revealHandled === '1') return;

            const type = el.dataset.reveal;
            const visibleNow = inViewport(el);
            const stBounds = {
                trigger: el,
                start: 'top 93%',
                end: 'bottom -5%',
            };

            if (type === 'fade') {
                gsap.set(el, { opacity: visibleNow ? 1 : 0 });
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(el, { opacity: 1, duration: 0.55, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, duration: 0.45, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(el, { opacity: 0, duration: 0.25, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, duration: 0.25, overwrite: 'auto' }),
                });
            } else if (type === 'rise') {
                const yIn = isMobile ? 12 : 20;
                gsap.set(el, visibleNow ? { opacity: 1, y: 0 } : { opacity: 0, y: yIn });
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
                gsap.set(split.lines, visibleNow ? { y: 0, opacity: 1 } : { y: 18, opacity: 0 });
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(split.lines, { y: 0, opacity: 1, duration: 0.7, stagger: 0.07, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(split.lines, { y: 0, opacity: 1, duration: 0.5, stagger: 0.05, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(split.lines, { opacity: 0, y: -8, duration: 0.25, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(split.lines, { opacity: 0, y: 18, duration: 0.25, overwrite: 'auto' }),
                });
            } else if (type === 'chars-wdth') {
                const split = SplitText.create(el, { type: 'chars', aria: 'none' });
                splits.push(split);
                gsap.set(split.chars, visibleNow ? { opacity: 1, y: 0 } : { opacity: 0, y: 12 });
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(split.chars, { opacity: 1, y: 0, duration: 0.5, stagger: 0.015, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(split.chars, { opacity: 1, y: 0, duration: 0.4, overwrite: 'auto' }),
                    onLeave: () => gsap.to(split.chars, { opacity: 0, duration: 0.25, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(split.chars, { opacity: 0, duration: 0.25, overwrite: 'auto' }),
                });
            } else if (type === 'mask') {
                const img = el.querySelector('img');
                gsap.set(el, visibleNow ? { opacity: 1, y: 0 } : { opacity: 0, y: 16 });
                if (img && !visibleNow) gsap.set(img, { scale: 1.04 });

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
            } else if (type === 'stamp') {
                gsap.set(el, { autoAlpha: visibleNow ? 1 : 0, scale: visibleNow ? 1 : 1.25, rotate: -3 });
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top 90%',
                    once: true,
                    onEnter: () => gsap.to(el, { autoAlpha: 1, scale: 1, rotate: -3, duration: 0.45, ease: 'back.out(1.8)' }),
                });
            }
        });
    });

    if (document.fonts?.ready) {
        document.fonts.ready.then(() => {
            ScrollTrigger.refresh();
        });
    }
}

export function cleanupReveal() {
    splits.forEach((s) => {
        try {
            s.revert();
        } catch {}
    });
    splits = [];

    if (ctx) {
        ctx.revert();
        ctx = null;
    }

    document.querySelectorAll('[data-reveal]').forEach((el) => {
        delete el.dataset.revealHandled;
    });
}
