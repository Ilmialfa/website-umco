export function init(el) {
    const timeEl = el.querySelector('[data-led-time]');
    const statusEl = el.querySelector('[data-led-status]');
    const crowdEl = el.querySelector('[data-led-crowd]');

    const openStr = el.dataset.openTime || '08:00';
    const closeStr = el.dataset.closeTime || '22:00';
    const patterns = (() => {
        try {
            return JSON.parse(el.dataset.patterns || '{}');
        } catch {
            return {};
        }
    })();

    const toMinutes = (hhmm) => {
        const clean = hhmm.replace('.', ':');
        const [h, m] = clean.split(':').map(Number);
        return (h || 0) * 60 + (m || 0);
    };

    const openMin = toMinutes(openStr);
    const closeMin = toMinutes(closeStr);

    const update = () => {
        const now = new Date();
        // Konversi ke WIB (UTC+7)
        const utcMs = now.getTime() + now.getTimezoneOffset() * 60000;
        const wib = new Date(utcMs + 7 * 3600000);
        const h = wib.getHours();
        const m = wib.getMinutes();
        const curMin = h * 60 + m;

        if (timeEl) {
            timeEl.textContent = `${String(h).padStart(2, '0')}.${String(m).padStart(2, '0')}`;
        }

        const isOpen = curMin >= openMin && curMin < closeMin;
        if (statusEl) {
            statusEl.textContent = isOpen ? 'BUKA' : 'TUTUP';
            statusEl.dataset.open = isOpen ? '1' : '0';
        }

        if (crowdEl) {
            if (!isOpen) {
                crowdEl.textContent = 'TUTUP';
            } else {
                const isoDay = wib.getDay() === 0 ? 7 : wib.getDay();
                const dayMap = patterns[String(isoDay)] || {};
                const level = Number(dayMap[String(h)] ?? 50);
                crowdEl.textContent = level < 40 ? 'SEPI' : level < 75 ? 'SEDANG' : 'RAME';
            }
        }
    };

    update();
    const timer = window.setInterval(update, 30000);

    return () => {
        window.clearInterval(timer);
    };
}
