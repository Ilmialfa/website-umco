import gsap from 'gsap';

export function init(el) {
    const viewport = el.querySelector('[data-infinite-viewport]');
    const strip = el.querySelector('[data-infinite-strip]');
    const dialog = el.querySelector('[data-gallery-dialog]');
    const dialogImg = el.querySelector('[data-gallery-dialog-img]');
    const dialogCaption = el.querySelector('[data-gallery-dialog-caption]');
    const dialogClose = el.querySelector('[data-gallery-dialog-close]');
    const btnPrev = el.querySelector('[data-infinite-prev]');
    const btnNext = el.querySelector('[data-infinite-next]');

    const cleanups = [];

    const openLightbox = (src, caption) => {
        if (!dialog || !dialogImg) return;
        dialogImg.src = src;
        dialogImg.alt = caption || 'Foto galeri UMCO';
        if (dialogCaption) dialogCaption.textContent = caption || '';
        if (typeof dialog.showModal === 'function') dialog.showModal();
    };

    const triggers = el.querySelectorAll('[data-gallery-trigger]');
    triggers.forEach((btn) => {
        const handler = () => {
            if (strip?.dataset.dragging === '1') return;
            openLightbox(btn.dataset.src, btn.dataset.caption);
        };
        btn.addEventListener('click', handler);
        cleanups.push(() => btn.removeEventListener('click', handler));
    });

    if (dialogClose && dialog) {
        const closeHandler = () => dialog.close();
        const clickBackdrop = (e) => {
            if (e.target === dialog) dialog.close();
        };
        dialogClose.addEventListener('click', closeHandler);
        dialog.addEventListener('click', clickBackdrop);
        cleanups.push(() => {
            dialogClose.removeEventListener('click', closeHandler);
            dialog.removeEventListener('click', clickBackdrop);
        });
    }

    if (!viewport || !strip) {
        return () => cleanups.forEach((fn) => fn());
    }

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduced) {
        let halfWidth = strip.scrollWidth / 2;
        let offsetX = 0;
        let paused = false;
        let speedPxPerSec = 42;
        let dragStartX = 0;
        let dragStartOffset = 0;
        let pointerDown = false;

        const wrapOffset = (val) => {
            if (halfWidth <= 0) return 0;
            let v = val % halfWidth;
            if (v > 0) v -= halfWidth;
            return v;
        };

        const tick = (_time, deltaMs) => {
            if (!paused && !pointerDown && halfWidth > 0) {
                offsetX = wrapOffset(offsetX - (speedPxPerSec * deltaMs) / 1000);
                strip.style.transform = `translate3d(${offsetX}px, 0, 0)`;
            }
        };

        gsap.ticker.add(tick);
        cleanups.push(() => gsap.ticker.remove(tick));

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
            dragStartX = e.clientX;
            dragStartOffset = offsetX;
            strip.dataset.dragging = '0';
        };
        const onPointerMove = (e) => {
            if (!pointerDown) return;
            const dx = e.clientX - dragStartX;
            if (Math.abs(dx) > 6) strip.dataset.dragging = '1';
            offsetX = wrapOffset(dragStartOffset + dx);
            strip.style.transform = `translate3d(${offsetX}px, 0, 0)`;
        };
        const onPointerUp = () => {
            if (!pointerDown) return;
            pointerDown = false;
            setTimeout(() => { if (strip) strip.dataset.dragging = '0'; }, 60);
        };

        viewport.addEventListener('pointerdown', onPointerDown);
        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', onPointerUp);
        cleanups.push(() => {
            viewport.removeEventListener('pointerdown', onPointerDown);
            window.removeEventListener('pointermove', onPointerMove);
            window.removeEventListener('pointerup', onPointerUp);
        });

        const nudge = (delta) => {
            offsetX = wrapOffset(offsetX + delta);
            gsap.to(strip, {
                duration: 0.45,
                ease: 'power2.out',
                onUpdate: () => { strip.style.transform = `translate3d(${offsetX}px, 0, 0)`; },
            });
        };

        if (btnPrev) {
            const hPrev = () => nudge(320);
            btnPrev.addEventListener('click', hPrev);
            cleanups.push(() => btnPrev.removeEventListener('click', hPrev));
        }
        if (btnNext) {
            const hNext = () => nudge(-320);
            btnNext.addEventListener('click', hNext);
            cleanups.push(() => btnNext.removeEventListener('click', hNext));
        }
    }

    return () => cleanups.forEach((fn) => fn());
}
