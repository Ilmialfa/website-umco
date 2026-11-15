import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const words = el.querySelectorAll('[data-story-word]');
    if (!words.length) return () => {};

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) {
        words.forEach((w) => {
            w.style.opacity = '1';
        });
        return () => {};
    }

    const anim = gsap.fromTo(
        words,
        { opacity: 0.34 },
        {
            opacity: 1,
            stagger: 0.03,
            ease: 'none',
            scrollTrigger: {
                trigger: el,
                start: 'top 82%',
                end: 'center 55%',
                scrub: 0.35,
            },
        }
    );

    return () => {
        anim.scrollTrigger?.kill();
    };
}
