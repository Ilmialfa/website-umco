export default function initScrollMeter(el) {
    const label = el.querySelector('[data-scroll-meter-val]') || el;
    let ticking = false;

    const update = () => {
        const cm = Math.round(window.scrollY / 38);
        label.textContent = `${cm} cm`;
        ticking = false;
    };

    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    };

    update();
    window.addEventListener('scroll', onScroll, { passive: true });

    return () => {
        window.removeEventListener('scroll', onScroll);
    };
}
