import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const cleanups = [];
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isDesktop = window.innerWidth >= 1024;

    if (!reduced && isDesktop) {
        const cards = el.querySelectorAll('[data-collage-card]');
        cards.forEach((card, i) => {
            const shift = i % 2 === 0 ? -24 : 24;
            const tw = gsap.fromTo(
                card,
                { y: -shift * 0.45 },
                {
                    y: shift,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top bottom',
                        end: 'bottom top',
                        scrub: 0.85,
                    },
                }
            );
            cleanups.push(() => tw.scrollTrigger?.kill());
        });

        const marqueeBand = el.querySelector('[data-marquee-parallax]');
        if (marqueeBand) {
            const tw = gsap.fromTo(
                marqueeBand,
                { xPercent: 0 },
                {
                    xPercent: -6,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: marqueeBand,
                        start: 'top bottom',
                        end: 'bottom top',
                        scrub: 0.7,
                    },
                }
            );
            cleanups.push(() => tw.scrollTrigger?.kill());
        }
    }

    return () => cleanups.forEach((fn) => fn());
}
