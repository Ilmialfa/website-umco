import gsap from 'gsap';

/**
 * Sorotan Menu andalan berpola strip portrait bergulir mulus (kiri-kanan),
 * mendukung klik ke /menu/{slug}, seret horizontal, serta tombol panah.
 */
export function init(el) {
    const viewport = el.querySelector('[data-infinite-viewport]');
    const strip = el.querySelector('[data-infinite-strip]');
    const btnPrev = el.querySelector('[data-infinite-prev]');
    const btnNext = el.querySelector('[data-infinite-next]');
    if (!viewport || !strip) return () => {};

    const cleanups = [];
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let halfWidth = strip.scrollWidth / 2;
    let offsetX = 0;
    let paused = false;
    let speedPxPerSec = 36;
    let dragStartX = 0;
    let dragStartOffset = 0;
    let pointerDown = false;
    let wasDragged = false;

    const wrapOffset = (val) => {
        if (halfWidth <= 0) return 0;
        let v = val % halfWidth;
        if (v > 0) v -= halfWidth;
        return v;
    };

    if (!reduced) {
        const tick = (_time, deltaMs) => {
            if (!paused && !pointerDown && halfWidth > 0) {
                offsetX = wrapOffset(offsetX - (speedPxPerSec * deltaMs) / 1000);
                strip.style.transform = `translate3d(${offsetX}px, 0, 0)`;
            }
        };
        gsap.ticker.add(tick);
        cleanups.push(() => gsap.ticker.remove(tick));
    }

    const onResize = () => {
        halfWidth = strip.scrollWidth / 2;
        offsetX = wrapOffset(offsetX);
    };
    window.addEventListener('resize', onResize, { passive: true });
    cleanups.push(() => window.removeEventListener('resize', onResize));

    const onEnter = () => { paused = true; };
    const onLeave = () => { paused = false; };
    viewport.addEventListener('mouseenter', onEnter);
    viewport.addEventListener('mouseleave', onLeave);
    cleanups.push(() => {
        viewport.removeEventListener('mouseenter', onEnter);
        viewport.removeEventListener('mouseleave', onLeave);
    });

    const onPointerDown = (e) => {
        pointerDown = true;
        wasDragged = false;
        dragStartX = e.clientX;
        dragStartOffset = offsetX;
        strip.dataset.dragging = '0';
    };
    const onPointerMove = (e) => {
        if (!pointerDown) return;
        const delta = e.clientX - dragStartX;
        if (Math.abs(delta) > 6) {
            wasDragged = true;
            strip.dataset.dragging = '1';
        }
        offsetX = wrapOffset(dragStartOffset + delta);
        strip.style.transform = `translate3d(${offsetX}px, 0, 0)`;
    };
    const onPointerUp = () => {
        if (!pointerDown) return;
        pointerDown = false;
        setTimeout(() => { strip.dataset.dragging = '0'; }, 80);
    };

    // Guard anchor navigation if user actually dragged
    const links = strip.querySelectorAll('a[data-menu-card-link]');
    links.forEach((a) => {
        const guard = (e) => {
            if (wasDragged || strip.dataset.dragging === '1') {
                e.preventDefault();
            }
        };
        a.addEventListener('click', guard);
        cleanups.push(() => a.removeEventListener('click', guard));
    });

    viewport.addEventListener('pointerdown', onPointerDown);
    window.addEventListener('pointermove', onPointerMove, { passive: true });
    window.addEventListener('pointerup', onPointerUp);
    window.addEventListener('pointercancel', onPointerUp);
    cleanups.push(() => {
        viewport.removeEventListener('pointerdown', onPointerDown);
        window.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('pointerup', onPointerUp);
        window.removeEventListener('pointercancel', onPointerUp);
    });

    const stepBy = (dir) => {
        const stepSize = Math.min(360, viewport.clientWidth * 0.72);
        const proxy = { x: offsetX };
        paused = true;
        gsap.to(proxy, {
            x: offsetX + dir * stepSize,
            duration: 0.65,
            ease: 'power3.out',
            onUpdate: () => {
                offsetX = wrapOffset(proxy.x);
                strip.style.transform = `translate3d(${offsetX}px, 0, 0)`;
            },
            onComplete: () => { paused = false; },
        });
    };

    const onPrev = () => stepBy(1);
    const onNext = () => stepBy(-1);
    btnPrev?.addEventListener('click', onPrev);
    btnNext?.addEventListener('click', onNext);
    cleanups.push(() => {
        btnPrev?.removeEventListener('click', onPrev);
        btnNext?.removeEventListener('click', onNext);
    });

    return () => cleanups.forEach((fn) => fn());
}
