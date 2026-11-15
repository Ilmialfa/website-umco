import gsap from 'gsap';

/**
 * Strip menu favorit horizontal otomatis (infinite loop) tanpa tombol panah,
 * dikendalikan langsung oleh posisi kursor kiri/kanan, seret (drag), dan sentuhan.
 */
export function init(el) {
    const viewport = el.querySelector('[data-infinite-viewport]');
    const strip = el.querySelector('[data-infinite-strip]');
    if (!viewport || !strip) return () => {};

    const cleanups = [];
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let halfWidth = strip.scrollWidth / 2;
    let offsetX = 0;
    const baseSpeed = -34; // px/detik ke kiri
    let currentSpeed = baseSpeed;
    let targetSpeed = baseSpeed;
    let pointerDown = false;
    let dragStartX = 0;
    let dragStartOffset = 0;
    let wasDragged = false;

    const wrapOffset = (val) => {
        if (halfWidth <= 0) return 0;
        let v = val % halfWidth;
        if (v > 0) v -= halfWidth;
        return v;
    };

    if (!reduced) {
        let isVisible = true;
        const visObs = new IntersectionObserver(
            (entries) => {
                isVisible = entries[0]?.isIntersecting ?? true;
            },
            { rootMargin: '140px 0px' }
        );
        visObs.observe(el);
        cleanups.push(() => visObs.disconnect());

        const tick = (_time, deltaMs) => {
            if (!isVisible || pointerDown || halfWidth <= 0) return;
            const factor = Math.min(1, (deltaMs / 1000) * 6.5);
            currentSpeed += (targetSpeed - currentSpeed) * factor;
            offsetX = wrapOffset(offsetX + (currentSpeed * deltaMs) / 1000);
            strip.style.transform = `translate3d(${offsetX}px, 0, 0)`;
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

    // Kursor kiri/kanan mengarahkan laju gulir tanpa tombol fisik
    const onMouseMove = (e) => {
        if (pointerDown || e.pointerType === 'touch') return;
        const rect = viewport.getBoundingClientRect();
        if (!rect.width) return;
        const norm = (e.clientX - rect.left) / rect.width; // 0 (kiri) .. 1 (kanan)

        if (norm < 0.30) {
            const strength = (0.30 - norm) / 0.30; // 0..1
            targetSpeed = 140 * strength; // gulir ke kanan (mundur ke item kiri)
        } else if (norm > 0.70) {
            const strength = (norm - 0.70) / 0.30; // 0..1
            targetSpeed = -150 * strength; // percepat ke kiri (item berikutnya)
        } else {
            // Area tengah: melambat halus agar mudah membaca dan mengeklik menu
            targetSpeed = 0;
        }
    };

    const onMouseLeave = () => {
        targetSpeed = baseSpeed;
    };

    viewport.addEventListener('mousemove', onMouseMove, { passive: true });
    viewport.addEventListener('mouseleave', onMouseLeave, { passive: true });
    cleanups.push(() => {
        viewport.removeEventListener('mousemove', onMouseMove);
        viewport.removeEventListener('mouseleave', onMouseLeave);
    });

    // Dukung wheel horizontal trackpad / mouse
    const onWheel = (e) => {
        const delta = Math.abs(e.deltaX) > Math.abs(e.deltaY) ? e.deltaX : (e.shiftKey ? e.deltaY : 0);
        if (Math.abs(delta) > 0) {
            e.preventDefault();
            offsetX = wrapOffset(offsetX - delta);
            strip.style.transform = `translate3d(${offsetX}px, 0, 0)`;
        }
    };
    viewport.addEventListener('wheel', onWheel, { passive: false });
    cleanups.push(() => viewport.removeEventListener('wheel', onWheel));

    // Seret horizontal langsung
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

    return () => {
        cleanups.forEach((fn) => fn());
    };
}
