export function init(el) {
    const coffeeInput = el.querySelector('[data-brew-coffee]');
    const tempInput = el.querySelector('[data-brew-temp]');
    const timeInput = el.querySelector('[data-brew-time]');

    const coffeeVal = el.querySelector('[data-brew-coffee-val]');
    const tempVal = el.querySelector('[data-brew-temp-val]');
    const timeVal = el.querySelector('[data-brew-time-val]');
    const verdictEl = el.querySelector('[data-brew-verdict]');
    const ratioEl = el.querySelector('[data-brew-ratio]');
    const liquidRect = el.querySelector('[data-brew-liquid]');
    const toneEl = el.querySelector('[data-brew-tone]');

    const verdicts = (() => {
        try {
            return JSON.parse(el.dataset.verdicts || '{}');
        } catch {
            return {};
        }
    })();

    const defaults = {
        under: verdicts.under || 'Kemudaan. Kayak air cucian gelas.',
        balanced: verdicts.balanced || 'Pas. Ini yang biasanya kami keluarkan di bar.',
        over: verdicts.over || 'Pahit sekali. Kamu lagi kejar deadline ya?',
    };

    const render = () => {
        const g = Number(coffeeInput?.value || 15);
        const t = Number(tempInput?.value || 92);
        const m = Number(timeInput?.value || 3);

        if (coffeeVal) coffeeVal.textContent = `${g} gram`;
        if (tempVal) tempVal.textContent = `${t} C`;
        if (timeVal) timeVal.textContent = `${m} menit`;

        const waterMl = 225;
        const ratio = (waterMl / g).toFixed(1).replace('.', ',');
        if (ratioEl) ratioEl.textContent = `1 : ${ratio}`;

        // Skor sederhana: target ideal sekitar 14-18g, 90-94C, 2.5-3.5 menit
        const score = (g - 10) / 15 * 0.45 + (t - 80) / 20 * 0.25 + (m - 1) / 4 * 0.30;

        let statusText = defaults.balanced;
        let fillColor = '#6B4226';
        let fillOpacity = 0.88;

        if (score < 0.34) {
            statusText = defaults.under;
            fillColor = '#B6895B';
            fillOpacity = 0.65;
        } else if (score > 0.72) {
            statusText = defaults.over;
            fillColor = '#22263A';
            fillOpacity = 0.96;
        }

        if (verdictEl) verdictEl.textContent = statusText;
        if (toneEl) {
            toneEl.style.backgroundColor = fillColor;
            toneEl.style.opacity = String(fillOpacity);
        }
        if (liquidRect) {
            liquidRect.setAttribute('fill', fillColor);
            liquidRect.setAttribute('fill-opacity', String(fillOpacity));
        }
    };

    const inputs = [coffeeInput, tempInput, timeInput].filter(Boolean);
    inputs.forEach((inp) => inp.addEventListener('input', render));
    render();

    return () => {
        inputs.forEach((inp) => inp.removeEventListener('input', render));
    };
}
