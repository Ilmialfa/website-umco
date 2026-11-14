import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const track = el.querySelector('[data-reel-track]');
    const cards = Array.from(el.querySelectorAll('[data-reel-card]'));
    const bar = el.querySelector('[data-reel-progress]');
    if (!track || !cards.length) return () => {};

    const desktopMq = window.matchMedia('(min-width: 1024px)');
    const reducedMq = window.matchMedia('(prefers-reduced-motion: reduce)');
    let st = null;

    const highlightIndex = (activeIdx) => {
        cards.forEach((card, idx) => {
            card.style.opacity = idx === activeIdx ? '1' : '0.52';
        });
    };

    const onScrollNative = () => {
        const maxScroll = Math.max(1, track.scrollWidth - track.clientWidth);
        const ratio = Math.min(1, Math.max(0, track.scrollLeft / maxScroll));
        if (bar) {
            bar.style.transform = `scaleX(${Math.max(0.15, ratio)})`;
        }
        const activeIdx = Math.min(cards.length - 1, Math.round(ratio * (cards.length - 1)));
        highlightIndex(activeIdx);
    };

    if (desktopMq.matches && !reducedMq.matches) {
        const getScrollAmount = () => Math.max(0, track.scrollWidth - el.clientWidth + 64);

        if (getScrollAmount() > 40) {
            const tween = gsap.to(track, {
                x: () => -getScrollAmount(),
                ease: 'none',
                scrollTrigger: {
                    trigger: el,
                    pin: true,
                    scrub: 0.8,
                    start: 'top top',
                    end: () => `+=${getScrollAmount()}`,
                    invalidateOnRefresh: true,
                    onUpdate: (self) => {
                        if (bar) {
                            bar.style.transform = `scaleX(${Math.max(0.12, self.progress)})`;
                        }
                        const activeIdx = Math.min(cards.length - 1, Math.round(self.progress * (cards.length - 1)));
                        highlightIndex(activeIdx);
                    },
                },
            });
            st = tween.scrollTrigger;
            highlightIndex(0);
            return () => {
                st?.kill();
                gsap.set(track, { clearProps: 'transform' });
                cards.forEach((c) => { c.style.opacity = ''; });
            };
        }
    }

    // Mobile & tablet scroll-snap handler
    track.addEventListener('scroll', onScrollNative, { passive: true });
    onScrollNative();

    return () => {
        track.removeEventListener('scroll', onScrollNative);
        st?.kill();
    };
}
