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
        { opacity: 0.2 },
        {
            opacity: 1,
            stagger: 0.04,
            ease: 'none',
            scrollTrigger: {
                trigger: el,
                start: 'top 78%',
                end: 'center 42%',
                scrub: 0.5,
            },
        }
    );

    return () => {
        anim.scrollTrigger?.kill();
    };
}
