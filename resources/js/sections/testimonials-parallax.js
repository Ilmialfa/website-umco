import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const cleanups = [];
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced || window.innerWidth < 768) return () => {};

    const cols = el.querySelectorAll('[data-parallax-col]');
    cols.forEach((col, idx) => {
        // Kiri & kanan bergerak ke atas (-), tengah bergerak ke bawah (+) saat digulir
        const shift = idx === 1 ? 42 : -42;
        const tw = gsap.fromTo(
            col,
            { y: -shift * 0.45 },
            {
                y: shift * 0.65,
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

    return () => cleanups.forEach((fn) => fn());
}
