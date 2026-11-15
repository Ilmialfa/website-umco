import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

let splits = [];
let ctx = null;

function isNearTopViewport(el) {
    const rect = el.getBoundingClientRect();
    return window.scrollY < 80 && rect.top < window.innerHeight * 0.94;
}

export function initReveal() {
    cleanupReveal();

    const els = Array.from(document.querySelectorAll('[data-reveal]'));
    const autoMedia = Array.from(document.querySelectorAll('main [data-reveal-media]'));
    if (!els.length && !autoMedia.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = window.innerWidth < 640;

    if (reduced) {
        [...els, ...autoMedia].forEach((el) => gsap.set(el, { opacity: 1, y: 0, scale: 1, autoAlpha: 1 }));
        return;
    }

    ctx = gsap.context(() => {
        document.querySelectorAll('[data-reveal-stagger]').forEach((parent) => {
            const children = Array.from(parent.querySelectorAll(':scope > [data-reveal]'));
            if (!children.length) return;

            children.forEach((c) => {
                c.dataset.revealHandled = '1';
            });

            const yIn = isMobile ? 16 : 26;
            const heroVisible = isNearTopViewport(parent);

            if (heroVisible) {
                gsap.set(children, { opacity: 1, y: 0, scale: 1 });
                return;
            }
            gsap.set(children, { opacity: 0, y: yIn, scale: 0.98 });

            ScrollTrigger.create({
                trigger: parent,
                start: 'top 92%',
                end: 'bottom -4%',
                onEnter: () => gsap.to(children, { opacity: 1, y: 0, scale: 1, duration: 0.78, stagger: 0.08, ease: 'power3.out', overwrite: 'auto' }),
                onEnterBack: () => gsap.to(children, { opacity: 1, y: 0, scale: 1, duration: 0.65, stagger: 0.06, ease: 'power3.out', overwrite: 'auto' }),
                onLeaveBack: () => gsap.to(children, { opacity: 0, y: yIn, scale: 0.98, duration: 0.35, ease: 'power2.out', overwrite: 'auto' }),
            });
        });

        // Koreografi halus untuk semua gambar & bingkai media
        autoMedia.forEach((el) => {
            const img = el.querySelector('img');
            const yIn = isMobile ? 18 : 28;
            const heroVisible = isNearTopViewport(el);
            if (heroVisible) {
                gsap.set(el, { opacity: 1, y: 0 });
                return;
            }
            gsap.set(el, { opacity: 0, y: yIn });
            if (img) gsap.set(img, { scale: 1.05 });

            ScrollTrigger.create({
                trigger: el,
                start: 'top 93%',
                end: 'bottom -4%',
                onEnter: () => {
                    gsap.to(el, { opacity: 1, y: 0, duration: 0.85, ease: 'power3.out', overwrite: 'auto' });
                    if (img) gsap.to(img, { scale: 1, duration: 1.05, ease: 'power3.out', overwrite: 'auto' });
                },
                onEnterBack: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out', overwrite: 'auto' }),
                onLeaveBack: () => {
                    gsap.to(el, { opacity: 0, y: yIn, duration: 0.35, ease: 'power2.out', overwrite: 'auto' });
                    if (img) gsap.to(img, { scale: 1.05, duration: 0.35, overwrite: 'auto' });
                },
            });
        });

        els.forEach((el) => {
            if (el.dataset.revealHandled === '1') return;

            const type = el.dataset.reveal;
            const heroVisible = isNearTopViewport(el);
            const stBounds = {
                trigger: el,
                start: 'top 92%',
                end: 'bottom -4%',
            };

            if (type === 'fade') {
                if (heroVisible) {
                    gsap.set(el, { opacity: 1, y: 0 });
                    return;
                }
                const yIn = isMobile ? 10 : 14;
                gsap.set(el, { opacity: 0, y: yIn });
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.75, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.6, ease: 'power3.out', overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, y: yIn, duration: 0.35, overwrite: 'auto' }),
                });
            } else if (type === 'rise') {
                if (heroVisible) {
                    gsap.set(el, { opacity: 1, y: 0 });
                    return;
                }
                const yIn = isMobile ? 16 : 26;
                gsap.set(el, { opacity: 0, y: yIn });
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.78, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out', overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, y: yIn, duration: 0.35, ease: 'power2.out', overwrite: 'auto' }),
                });
            } else if (type === 'lines') {
                if (heroVisible) {
                    gsap.set(el, { opacity: 1, y: 0 });
                    return;
                }
                const split = SplitText.create(el, { type: 'lines', aria: 'none' });
                splits.push(split);
                gsap.set(split.lines, { y: 24, opacity: 0 });
                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => gsap.to(split.lines, { y: 0, opacity: 1, duration: 0.82, stagger: 0.08, ease: 'power3.out', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(split.lines, { y: 0, opacity: 1, duration: 0.65, stagger: 0.06, ease: 'power3.out', overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(split.lines, { opacity: 0, y: 24, duration: 0.35, overwrite: 'auto' }),
                });
            } else if (type === 'mask') {
                if (heroVisible) {
                    gsap.set(el, { opacity: 1, y: 0 });
                    return;
                }
                const img = el.querySelector('img');
                const yIn = isMobile ? 16 : 24;
                gsap.set(el, { opacity: 0, y: yIn });
                if (img) gsap.set(img, { scale: 1.05 });

                ScrollTrigger.create({
                    ...stBounds,
                    onEnter: () => {
                        gsap.to(el, { opacity: 1, y: 0, duration: 0.85, ease: 'power3.out', overwrite: 'auto' });
                        if (img) gsap.to(img, { scale: 1, duration: 1.05, ease: 'power3.out', overwrite: 'auto' });
                    },
                    onEnterBack: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out', overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, y: yIn, duration: 0.35, overwrite: 'auto' }),
                });
            } else if (type === 'stamp') {
                gsap.fromTo(
                    el,
                    { scale: 1.18, opacity: 0 },
                    {
                        scale: 1,
                        opacity: 1,
                        duration: 0.5,
                        ease: 'back.out(1.7)',
                        scrollTrigger: {
                            trigger: el,
                            start: 'top 90%',
                            once: true,
                        },
                    }
                );
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
        try {
            s.revert();
        } catch {}
    });
    splits = [];
}
