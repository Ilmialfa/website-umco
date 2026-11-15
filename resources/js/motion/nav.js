export function initNav() {
    const header = document.getElementById('umco-navbar');
    if (!header) return () => {};

    let ticking = false;
    const initialTheme = header.dataset.initialTheme || 'dark';

    const applyState = () => {
        const y = window.scrollY;
        const scrolled = y > 48;
        header.dataset.scrolled = scrolled ? '1' : '0';
        header.dataset.hidden = '0';

        if (!scrolled) {
            header.dataset.theme = initialTheme;
        } else {
            header.dataset.theme = 'solid';
        }

        ticking = false;
    };

    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(applyState);
            ticking = true;
        }
    };

    applyState();
    window.addEventListener('scroll', onScroll, { passive: true });

    return () => {
        window.removeEventListener('scroll', onScroll);
    };
}
