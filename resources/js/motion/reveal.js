import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

let splits = [];
let ctx = null;

export function initReveal() {
    cleanupReveal();

    const els = Array.from(document.querySelectorAll('[data-reveal]'));
    if (!els.length) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = window.innerWidth < 640;

    if (reduced) {
        ctx = gsap.context(() => {
            els.forEach((el) => {
                gsap.fromTo(
                    el,
                    { autoAlpha: 0 },
                    {
                        autoAlpha: 1,
                        duration: 0.15,
                        ease: 'none',
                        scrollTrigger: {
                            trigger: el,
                            start: 'top 90%',
                            end: 'bottom 10%',
                            toggleActions: 'play reverse play reverse',
                        },
                    }
                );
            });
        });
        return;
    }

    ctx = gsap.context(() => {
        // Stagger groups first
        document.querySelectorAll('[data-reveal-stagger]').forEach((parent) => {
            const children = Array.from(parent.querySelectorAll(':scope > [data-reveal]'));
            if (!children.length) return;

            children.forEach((c) => {
                c.dataset.revealHandled = '1';
            });

            const yIn = isMobile ? 12 : 24;
            gsap.set(children, { opacity: 0, y: yIn });

            ScrollTrigger.create({
                trigger: parent,
                start: 'top 85%',
                end: 'bottom 15%',
                onEnter: () => {
                    gsap.to(children, {
                        opacity: 1,
                        y: 0,
                        duration: 0.42,
                        stagger: 0.06,
                        ease: 'power3.out',
                        overwrite: 'auto',
                    });
                },
                onLeave: () => {
                    gsap.to(children, {
                        opacity: 0,
                        y: isMobile ? -6 : -12,
                        duration: 0.32,
                        stagger: 0.03,
                        ease: 'power2.inOut',
                        overwrite: 'auto',
                    });
                },
                onEnterBack: () => {
                    gsap.to(children, {
                        opacity: 1,
                        y: 0,
                        duration: 0.42,
                        stagger: 0.06,
                        ease: 'power3.out',
                        overwrite: 'auto',
                    });
                },
                onLeaveBack: () => {
                    gsap.to(children, {
                        opacity: 0,
                        y: yIn,
                        duration: 0.32,
                        stagger: 0.03,
                        ease: 'power2.inOut',
                        overwrite: 'auto',
                    });
                },
            });
        });

        els.forEach((el) => {
            if (el.dataset.revealHandled === '1') return;

            const type = el.dataset.reveal;
            const stBase = {
                trigger: el,
                start: 'top 85%',
                end: 'bottom 15%',
            };

            if (type === 'fade') {
                gsap.fromTo(
                    el,
                    { opacity: 0 },
                    {
                        opacity: 1,
                        duration: 0.42,
                        ease: 'power3.out',
                        scrollTrigger: { ...stBase, toggleActions: 'play reverse play reverse' },
                    }
                );
            } else if (type === 'rise') {
                const yIn = isMobile ? 12 : 24;
                const yOut = isMobile ? -6 : -12;
                gsap.set(el, { opacity: 0, y: yIn });
                ScrollTrigger.create({
                    ...stBase,
                    onEnter: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.42, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(el, { opacity: 0, y: yOut, duration: 0.32, ease: 'power2.inOut', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, y: 0, duration: 0.42, ease: 'power3.out', overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, y: yIn, duration: 0.32, ease: 'power2.inOut', overwrite: 'auto' }),
                });
            } else if (type === 'lines') {
                const split = SplitText.create(el, { type: 'lines', mask: 'lines', aria: 'none' });
                splits.push(split);
                gsap.set(split.lines, { yPercent: 100 });
                ScrollTrigger.create({
                    ...stBase,
                    onEnter: () => gsap.to(split.lines, { yPercent: 0, opacity: 1, duration: 0.8, stagger: 0.08, ease: 'power3.out', overwrite: 'auto' }),
                    onLeave: () => gsap.to(el, { opacity: 0, duration: 0.3, ease: 'power2.inOut', overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, duration: 0.4, ease: 'power3.out', overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, duration: 0.3, ease: 'power2.inOut', overwrite: 'auto' }),
                });
            } else if (type === 'chars-wdth') {
                const split = SplitText.create(el, { type: 'chars', aria: 'none' });
                splits.push(split);
                gsap.set(split.chars, { opacity: 0, y: 14 });
                ScrollTrigger.create({
                    ...stBase,
                    onEnter: () =>
                        gsap.to(split.chars, {
                            opacity: 1,
                            y: 0,
                            duration: 0.55,
                            stagger: 0.015,
                            ease: 'power3.out',
                            overwrite: 'auto',
                        }),
                    onLeave: () => gsap.to(el, { opacity: 0, duration: 0.3, overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { opacity: 1, duration: 0.4, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { opacity: 0, duration: 0.3, overwrite: 'auto' }),
                });
            } else if (type === 'mask') {
                const img = el.querySelector('img');
                gsap.set(el, { clipPath: 'inset(100% 0% 0% 0%)', autoAlpha: 0 });
                if (img) gsap.set(img, { scale: 1.08 });

                ScrollTrigger.create({
                    ...stBase,
                    onEnter: () => {
                        gsap.to(el, { clipPath: 'inset(0% 0% 0% 0%)', autoAlpha: 1, duration: 0.8, ease: 'power3.out', overwrite: 'auto' });
                        if (img) gsap.to(img, { scale: 1, duration: 1.0, ease: 'power3.out', overwrite: 'auto' });
                    },
                    onLeave: () => gsap.to(el, { autoAlpha: 0, duration: 0.35, overwrite: 'auto' }),
                    onEnterBack: () => gsap.to(el, { autoAlpha: 1, duration: 0.45, overwrite: 'auto' }),
                    onLeaveBack: () => gsap.to(el, { autoAlpha: 0, duration: 0.35, overwrite: 'auto' }),
                });
            } else if (type === 'stamp') {
                gsap.set(el, { autoAlpha: 0, scale: 1.4, rotate: -8 });
                ScrollTrigger.create({
                    trigger: el,
                    start: 'top 88%',
                    once: true,
                    onEnter: () => {
                        gsap.to(el, {
                            autoAlpha: 1,
                            scale: 1,
                            rotate: -4,
                            duration: 0.45,
                            ease: 'back.out(2)',
                        });
                    },
                });
            }
        });
    });
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
    document.querySelectorAll('[data-reveal-handled]').forEach((el) => {
        delete el.dataset.revealHandled;
    });
}
