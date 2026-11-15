import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function init(el) {
    const cleanups = [];
    const dialog = el.querySelector('[data-gallery-dialog]');
    const dialogImg = el.querySelector('[data-gallery-dialog-img]');
    const dialogCaption = el.querySelector('[data-gallery-dialog-caption]');
    const dialogClose = el.querySelector('[data-gallery-dialog-close]');

    el.querySelectorAll('[data-gallery-trigger]').forEach((btn) => {
        const handler = () => {
            if (!dialog || !dialogImg) return;
            dialogImg.src = btn.dataset.src;
            dialogImg.alt = btn.dataset.caption || 'Foto galeri UMCO';
            if (dialogCaption) dialogCaption.textContent = btn.dataset.caption || '';
            if (typeof dialog.showModal === 'function') dialog.showModal();
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

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduced) {
        el.querySelectorAll('[data-diag-row]').forEach((row, idx) => {
            const dir = idx % 2 === 0 ? -1 : 1;
            const tw = gsap.fromTo(
                row,
                { xPercent: dir * -4, y: dir * -16 },
                {
                    xPercent: dir * 6,
                    y: dir * 24,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top bottom',
                        end: 'bottom top',
                        scrub: 0.9,
                    },
                }
            );
            cleanups.push(() => tw.scrollTrigger?.kill());
        });
    }

    return () => cleanups.forEach((fn) => fn());
}
