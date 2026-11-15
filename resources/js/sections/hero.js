import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const cleanups = [];
    const reducedMq = window.matchMedia('(prefers-reduced-motion: reduce)');
    const desktopMq = window.matchMedia('(min-width: 1024px)');
    const cutout = el.querySelector('[data-hero-cutout]');

    if (!reducedMq.matches && desktopMq.matches && cutout) {
        const tw = gsap.to(cutout, {
            yPercent: 7,
            ease: 'none',
            scrollTrigger: {
                trigger: el,
                start: 'top top',
                end: 'bottom top',
                scrub: 0.9,
            },
        });
        cleanups.push(() => tw.scrollTrigger?.kill());
    }

    return () => cleanups.forEach((fn) => fn());
}
