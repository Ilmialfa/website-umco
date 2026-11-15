import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const cleanups = [];
    const reducedMq = window.matchMedia('(prefers-reduced-motion: reduce)');
    const desktopMq = window.matchMedia('(min-width: 1024px)');
    const cutout = el.querySelector('[data-hero-cutout]');

    if (!reducedMq.matches && cutout) {
        gsap.fromTo(
            cutout,
            { y: 24, opacity: 0, scale: 0.96 },
            { y: 0, opacity: 1, scale: 1, duration: 0.75, ease: 'power3.out', delay: 0.1 }
        );

        if (desktopMq.matches) {
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
    }

    return () => cleanups.forEach((fn) => fn());
}
