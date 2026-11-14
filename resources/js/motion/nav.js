export function initNav() {
    const header = document.getElementById('umco-navbar');
    if (!header) return () => {};

    const isReservationPage = header.dataset.page === 'reservation';
    let lastY = window.scrollY;
    let ticking = false;
    let currentTheme = header.dataset.initialTheme || 'dark';

    const applyState = () => {
        const y = window.scrollY;
        const scrolled = y > 80;
        header.dataset.scrolled = scrolled ? '1' : '0';

        // Sembunyi saat scroll ke bawah di luar posisi atas, tampil saat scroll ke atas
        if (!isReservationPage && y > 160 && y > lastY + 6) {
            header.dataset.hidden = '1';
        } else if (y < lastY - 4 || y <= 160) {
            header.dataset.hidden = '0';
        }

        // Deteksi tema section di bawah navbar jika belum scrolled
        if (!scrolled) {
            const sections = document.querySelectorAll('[data-nav-theme]');
            for (const sec of sections) {
                const rect = sec.getBoundingClientRect();
                if (rect.top <= 48 && rect.bottom >= 48) {
                    currentTheme = sec.dataset.navTheme;
                    break;
                }
            }
            header.dataset.theme = currentTheme;
        } else {
            header.dataset.theme = 'solid';
        }

        lastY = y;
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
