import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const numEl = el.querySelector('[data-altimeter-num]');
    const items = Array.from(el.querySelectorAll('[data-origin-altitude]'));
    if (!numEl || !items.length) return () => {};

    const maxAlt = Math.max(...items.map((i) => Number(i.dataset.originAltitude || 0)), 1650);
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const desktopMq = window.matchMedia('(min-width: 640px)');

    if (reduced || !desktopMq.matches) {
        numEl.textContent = maxAlt.toLocaleString('id-ID');
        return () => {};
    }

    const state = { val: 0 };
    const st = gsap.to(state, {
        val: maxAlt,
        ease: 'none',
        scrollTrigger: {
            trigger: el,
            start: 'top 65%',
            end: 'bottom 55%',
            scrub: 0.4,
        },
        onUpdate: () => {
            const current = Math.round(state.val);
            numEl.textContent = current.toLocaleString('id-ID');
            items.forEach((row) => {
                const target = Number(row.dataset.originAltitude || 0);
                row.style.opacity = current >= target - 180 ? '1' : '0.35';
            });
        },
    });

    return () => {
        st.scrollTrigger?.kill();
    };
}
