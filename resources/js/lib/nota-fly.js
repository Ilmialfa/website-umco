export function flyToNota(sourceEl, imgUrl = null) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.dispatchEvent(new CustomEvent('umco:nota-landed'));
        return;
    }

    const startRect = sourceEl?.getBoundingClientRect?.();
    const sx = startRect ? startRect.left + startRect.width / 2 : window.innerWidth * 0.5;
    const sy = startRect ? startRect.top + startRect.height / 2 : window.innerHeight * 0.5;

    window.requestAnimationFrame(() => {
        const pillTarget = document.querySelector('[data-nota-target]');
        const targetRect = pillTarget?.getBoundingClientRect?.();
        const hasVisibleTarget = targetRect && targetRect.width > 0 && targetRect.height > 0;

        const tx = hasVisibleTarget ? targetRect.left + targetRect.width / 2 : Math.max(window.innerWidth - 160, 80);
        const ty = hasVisibleTarget ? targetRect.top + targetRect.height / 2 : window.innerHeight - 40;

        const token = document.createElement('div');
        token.setAttribute('aria-hidden', 'true');
        token.style.cssText = [
            'position:fixed',
            'left:0',
            'top:0',
            'width:44px',
            'height:44px',
            'margin-left:-22px',
            'margin-top:-22px',
            'border-radius:9999px',
            'overflow:hidden',
            'pointer-events:none',
            'z-index:90',
            'display:flex',
            'align-items:center',
            'justify-content:center',
            'background-color:#1B5E42',
            'color:#FFFFFF',
            'border:2.5px solid #FFFFFF',
            'will-change:transform,opacity',
            `transform:translate3d(${sx}px,${sy}px,0) scale(1)`,
        ].join(';');

        if (imgUrl) {
            const img = document.createElement('img');
            img.src = imgUrl;
            img.alt = '';
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
            token.appendChild(img);
        } else {
            token.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M8 7h8"/><path d="M8 11h8"/><path d="M8 15h5"/></svg>';
        }

        document.body.appendChild(token);

        const cx = sx + (tx - sx) * 0.42;
        const cy = Math.min(sy, ty) - Math.max(96, Math.abs(tx - sx) * 0.24);
        const duration = 560;
        const startTime = performance.now();

        const step = (now) => {
            const raw = Math.min(1, (now - startTime) / duration);
            const t = raw < 0.5 ? 4 * raw * raw * raw : 1 - Math.pow(-2 * raw + 2, 3) / 2;
            const inv = 1 - t;

            const x = inv * inv * sx + 2 * inv * t * cx + t * t * tx;
            const y = inv * inv * sy + 2 * inv * t * cy + t * t * ty;

            const scale = raw < 0.25 ? 1 + (raw / 0.25) * 0.18 : 1.18 - ((raw - 0.25) / 0.75) * 0.84;
            const rotate = Math.sin(raw * Math.PI) * (tx >= sx ? 14 : -14);
            const opacity = raw > 0.84 ? Math.max(0.15, 1 - (raw - 0.84) / 0.16) : 1;

            token.style.transform = `translate3d(${x.toFixed(1)}px,${y.toFixed(1)}px,0) scale(${scale.toFixed(3)}) rotate(${rotate.toFixed(1)}deg)`;
            token.style.opacity = String(opacity);

            if (raw < 1) {
                window.requestAnimationFrame(step);
            } else {
                token.remove();
                window.dispatchEvent(new CustomEvent('umco:nota-landed'));
                const pill = document.querySelector('[data-nota-pill]');
                if (pill && typeof pill.animate === 'function') {
                    pill.animate(
                        [
                            { transform: 'scale(1)' },
                            { transform: 'scale(1.07) translateY(-4px)', offset: 0.38 },
                            { transform: 'scale(0.97) translateY(0)', offset: 0.72 },
                            { transform: 'scale(1)' },
                        ],
                        { duration: 420, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' }
                    );
                }
            }
        };

        window.requestAnimationFrame(step);
    });
}
