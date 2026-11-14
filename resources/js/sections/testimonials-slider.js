export function init(el) {
    const slides = Array.from(el.querySelectorAll('[data-quote-slide]'));
    const prevBtn = el.querySelector('[data-quote-prev]');
    const nextBtn = el.querySelector('[data-quote-next]');
    const indexEl = el.querySelector('[data-quote-index]');
    if (slides.length <= 1) return () => {};

    let current = 0;
    let timer = null;
    let touchStartX = 0;

    const show = (nextIdx) => {
        current = (nextIdx + slides.length) % slides.length;
        slides.forEach((slide, idx) => {
            const active = idx === current;
            slide.classList.toggle('hidden', !active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        if (indexEl) {
            indexEl.textContent = `${String(current + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
        }
    };

    const resetTimer = () => {
        if (timer) window.clearInterval(timer);
        timer = window.setInterval(() => show(current + 1), 6000);
    };

    const onPrev = () => { show(current - 1); resetTimer(); };
    const onNext = () => { show(current + 1); resetTimer(); };
    const onTouchStart = (e) => { touchStartX = e.changedTouches[0]?.clientX || 0; };
    const onTouchEnd = (e) => {
        const dx = (e.changedTouches[0]?.clientX || 0) - touchStartX;
        if (Math.abs(dx) > 40) {
            show(dx < 0 ? current + 1 : current - 1);
            resetTimer();
        }
    };

    prevBtn?.addEventListener('click', onPrev);
    nextBtn?.addEventListener('click', onNext);
    el.addEventListener('touchstart', onTouchStart, { passive: true });
    el.addEventListener('touchend', onTouchEnd, { passive: true });

    show(0);
    resetTimer();

    return () => {
        if (timer) window.clearInterval(timer);
        prevBtn?.removeEventListener('click', onPrev);
        nextBtn?.removeEventListener('click', onNext);
        el.removeEventListener('touchstart', onTouchStart);
        el.removeEventListener('touchend', onTouchEnd);
    };
}
