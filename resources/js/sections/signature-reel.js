export function init(el) {
    const track = el.querySelector('[data-reel-track]');
    const cards = Array.from(el.querySelectorAll('[data-reel-card]'));
    const bar = el.querySelector('[data-reel-progress]');
    const prevBtn = el.querySelector('[data-reel-prev]');
    const nextBtn = el.querySelector('[data-reel-next]');
    if (!track || !cards.length) return () => {};

    let isDragging = false;
    let startX = 0;
    let scrollStart = 0;

    const syncProgress = () => {
        const maxScroll = Math.max(1, track.scrollWidth - track.clientWidth);
        const ratio = Math.min(1, Math.max(0, track.scrollLeft / maxScroll));
        if (bar) {
            bar.style.transform = `scaleX(${Math.max(0.18, ratio)})`;
        }
        if (prevBtn) prevBtn.disabled = track.scrollLeft <= 8;
        if (nextBtn) nextBtn.disabled = track.scrollLeft >= maxScroll - 8;
    };

    const stepBy = (dir) => {
        const cardW = cards[0]?.getBoundingClientRect().width || 340;
        track.scrollBy({ left: dir * (cardW + 24), behavior: 'smooth' });
    };

    const onPrev = () => stepBy(-1);
    const onNext = () => stepBy(1);

    const onPointerDown = (e) => {
        if (e.button !== 0 || e.target.closest('a')) return;
        isDragging = true;
        startX = e.pageX - track.offsetLeft;
        scrollStart = track.scrollLeft;
        track.classList.add('cursor-grabbing');
    };
    const onPointerMove = (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        track.scrollLeft = scrollStart - (x - startX) * 1.15;
    };
    const onPointerUp = () => {
        isDragging = false;
        track.classList.remove('cursor-grabbing');
    };

    track.addEventListener('scroll', syncProgress, { passive: true });
    prevBtn?.addEventListener('click', onPrev);
    nextBtn?.addEventListener('click', onNext);
    track.addEventListener('mousedown', onPointerDown);
    window.addEventListener('mousemove', onPointerMove);
    window.addEventListener('mouseup', onPointerUp);
    syncProgress();

    return () => {
        track.removeEventListener('scroll', syncProgress);
        prevBtn?.removeEventListener('click', onPrev);
        nextBtn?.removeEventListener('click', onNext);
        track.removeEventListener('mousedown', onPointerDown);
        window.removeEventListener('mousemove', onPointerMove);
        window.removeEventListener('mouseup', onPointerUp);
    };
}
