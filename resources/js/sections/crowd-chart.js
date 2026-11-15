export function init(el) {
    const patterns = (() => {
        try {
            return JSON.parse(el.dataset.patterns || '{}');
        } catch {
            return {};
        }
    })();

    const dayButtons = Array.from(el.querySelectorAll('[data-crowd-day]'));
    const barsWrap = el.querySelector('[data-crowd-bars]');
    const ctaPill = el.querySelector('[data-crowd-cta]');
    const hours = Array.from({ length: 16 }, (_, i) => i + 7); // 07..22

    const utcMs = Date.now() + new Date().getTimezoneOffset() * 60000;
    const wib = new Date(utcMs + 7 * 3600000);
    const todayIso = wib.getDay() === 0 ? 7 : wib.getDay();
    const currentHour = wib.getHours();

    let activeDay = todayIso;

    // Hitung tanggal terdekat untuk hari terpilih
    const targetDateForDay = (isoDay) => {
        const diff = (isoDay - todayIso + 7) % 7;
        const target = new Date(wib.getTime() + diff * 86400000);
        const y = target.getFullYear();
        const m = String(target.getMonth() + 1).padStart(2, '0');
        const d = String(target.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };

    const renderBars = () => {
        if (!barsWrap) return;
        const dayMap = patterns[String(activeDay)] || {};
        const dateStr = targetDateForDay(activeDay);

        let peakHour = 16;
        let peakVal = -1;
        hours.forEach((hr) => {
            const val = Number(dayMap[String(hr)] ?? 0);
            if (val > peakVal) {
                peakVal = val;
                peakHour = hr;
            }
        });

        barsWrap.innerHTML = '';
        const isDesktopOrTab = window.innerWidth >= 640;

        hours.forEach((hr, index) => {
            const level = Math.max(8, Math.min(100, Number(dayMap[String(hr)] ?? 20)));
            const isNow = activeDay === todayIso && hr === currentHour;
            const isPeak = hr === peakHour;
            const jamText = `${String(hr).padStart(2, '0')}.00`;
            const jamQuery = `${String(hr).padStart(2, '0')}:00`;
            const targetUrl = `/reservasi?tanggal=${dateStr}&jam=${jamQuery}`;

            const btn = document.createElement('a');
            btn.href = targetUrl;
            btn.setAttribute('data-crowd-hour', String(hr));
            btn.setAttribute('title', `Pesan meja jam ${jamText}`);

            if (isDesktopOrTab) {
                btn.className = 'group relative flex flex-col items-center justify-end h-56 sm:h-64 flex-1 focus:outline-none';
                btn.innerHTML = `
                    ${isPeak ? '<span class="absolute -top-7 whitespace-nowrap font-hand text-xl text-[var(--mentega-tua)]">biasanya penuh</span>' : ''}
                    <span class="opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute -top-9 z-10 px-2.5 py-1 rounded-full bg-[var(--tinta)] text-white text-[11px] font-medium whitespace-nowrap pointer-events-none">
                        Pesan ${jamText}
                    </span>
                    <span
                        class="w-full max-w-[30px] rounded-t-md transition-[height,background-color] duration-300 group-hover:bg-[var(--mentega-tua)]"
                        style="height: ${level}%; background-color: ${isNow ? 'var(--mentega)' : 'rgba(23, 28, 25, 0.24)'}; transition-delay: ${index * 20}ms"
                    ></span>
                    <span class="mt-2.5 text-xs tabular-nums ${isNow ? 'font-bold text-[var(--mentega)]' : 'text-[var(--tinta-70)]'}">
                        ${String(hr).padStart(2, '0')}
                    </span>
                `;
            } else {
                btn.className = 'flex items-center gap-3 py-1.5';
                btn.innerHTML = `
                    <span class="w-12 shrink-0 text-xs tabular-nums font-medium ${isNow ? 'font-bold text-[var(--mentega)]' : 'text-[var(--tinta-70)]'}">${jamText}</span>
                    <span class="flex-1 h-4 bg-[var(--garis)] rounded-r-md overflow-hidden">
                        <span class="block h-full rounded-r-md" style="width: ${level}%; background-color: ${isNow ? 'var(--mentega)' : 'rgba(23, 28, 25, 0.28)'};"></span>
                    </span>
                    <span class="w-20 shrink-0 text-right font-hand text-base text-[var(--mentega-tua)]">${isPeak ? 'penuh' : ''}</span>
                `;
            }

            btn.addEventListener('mouseenter', () => {
                if (ctaPill) {
                    ctaPill.href = targetUrl;
                    ctaPill.textContent = `Pesan meja jam ${jamText} →`;
                }
            });

            barsWrap.appendChild(btn);
        });

        dayButtons.forEach((btn) => {
            const isActive = Number(btn.dataset.crowdDay) === activeDay;
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            btn.className = isActive
                ? 'px-4 h-10 rounded-full bg-[var(--mentega)] text-white text-sm font-semibold whitespace-nowrap cursor-pointer'
                : 'px-4 h-10 rounded-full bg-transparent border border-[var(--garis)] text-[var(--tinta)] text-sm font-medium whitespace-nowrap cursor-pointer hover:border-[var(--mentega)]';
        });
    };

    const onDayClick = (e) => {
        activeDay = Number(e.currentTarget.dataset.crowdDay);
        renderBars();
    };

    const onResize = () => renderBars();

    dayButtons.forEach((btn) => btn.addEventListener('click', onDayClick));
    window.addEventListener('resize', onResize, { passive: true });
    renderBars();

    return () => {
        dayButtons.forEach((btn) => btn.removeEventListener('click', onDayClick));
        window.removeEventListener('resize', onResize);
    };
}
