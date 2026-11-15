function parseRgbString(str) {
    if (!str || str === 'transparent') return null;
    const match = str.match(/rgba?\(\s*([\d.]+)[,\s]+([\d.]+)[,\s]+([\d.]+)(?:[,\s/]+([\d.]+))?\s*\)/i);
    if (!match) return null;
    const a = match[4] !== undefined ? parseFloat(match[4]) : 1;
    if (a <= 0.05) return null;
    return [Math.round(parseFloat(match[1])), Math.round(parseFloat(match[2])), Math.round(parseFloat(match[3]))];
}

function parseHex(hex) {
    const clean = hex.trim().replace('#', '');
    if (clean.length === 3) {
        return clean.split('').map((c) => parseInt(c + c, 16));
    }
    if (clean.length === 6) {
        return [
            parseInt(clean.slice(0, 2), 16),
            parseInt(clean.slice(2, 4), 16),
            parseInt(clean.slice(4, 6), 16),
        ];
    }
    return [246, 244, 237];
}

function parseStops(spec) {
    return spec
        .split(',')
        .map((part) => {
            const [posStr, hex] = part.trim().split(':');
            return { pos: Math.max(0, Math.min(1, parseFloat(posStr))), rgb: parseHex(hex) };
        })
        .filter((s) => !Number.isNaN(s.pos))
        .sort((a, b) => a.pos - b.pos);
}

function lerpRgb(stops, progress) {
    const t = Math.max(0, Math.min(1, progress));
    if (t <= stops[0].pos) return stops[0].rgb;
    if (t >= stops[stops.length - 1].pos) return stops[stops.length - 1].rgb;

    for (let i = 0; i < stops.length - 1; i++) {
        const a = stops[i];
        const b = stops[i + 1];
        if (t >= a.pos && t <= b.pos) {
            const span = Math.max(0.0001, b.pos - a.pos);
            const localT = (t - a.pos) / span;
            return [
                Math.round(a.rgb[0] + (b.rgb[0] - a.rgb[0]) * localT),
                Math.round(a.rgb[1] + (b.rgb[1] - a.rgb[1]) * localT),
                Math.round(a.rgb[2] + (b.rgb[2] - a.rgb[2]) * localT),
            ];
        }
    }
    return stops[0].rgb;
}

function resolveSectionColor(section, sampleY) {
    const stopSpec = section.dataset.navStops;
    if (stopSpec) {
        const rect = section.getBoundingClientRect();
        const stops = parseStops(stopSpec);
        if (stops.length >= 2 && rect.height > 0) {
            const progress = (sampleY - rect.top) / rect.height;
            return lerpRgb(stops, progress);
        }
    }

    if (section.dataset.navBg) {
        return parseHex(section.dataset.navBg);
    }

    const computed = parseRgbString(getComputedStyle(section).backgroundColor);
    if (computed) return computed;

    const bodyBg = parseRgbString(getComputedStyle(document.body).backgroundColor);
    return bodyBg || [246, 244, 237];
}

export function initNav() {
    const header = document.getElementById('umco-navbar');
    if (!header) return () => {};

    let ticking = false;
    const initialTheme = header.dataset.initialTheme || 'light';

    const collectZones = () => {
        const list = Array.from(document.querySelectorAll('main section, main [data-nav-theme], main [data-nav-bg], #public-footer'));
        return list.filter((el, idx, arr) => el.offsetHeight > 20 && arr.indexOf(el) === idx);
    };

    const applyState = () => {
        ticking = false;
        const y = window.scrollY;
        const scrolled = y > 24;
        header.dataset.scrolled = scrolled ? '1' : '0';
        header.dataset.hidden = '0';

        const headerHeight = header.getBoundingClientRect().height || 64;
        const sampleY = Math.min(34, Math.max(18, headerHeight * 0.5));
        const zones = collectZones();

        let activeZone = null;
        for (let i = zones.length - 1; i >= 0; i--) {
            const r = zones[i].getBoundingClientRect();
            if (r.top <= sampleY + 4 && r.bottom > sampleY) {
                activeZone = zones[i];
                break;
            }
        }

        if (!activeZone && zones.length) {
            activeZone = zones[0];
        }

        if (!activeZone) {
            const fallbackRgb = initialTheme === 'dark' ? [13, 53, 38] : [246, 244, 237];
            header.style.setProperty('--nav-bg', `rgb(${fallbackRgb[0]}, ${fallbackRgb[1]}, ${fallbackRgb[2]})`);
            header.dataset.theme = initialTheme;
            return;
        }

        const rgb = resolveSectionColor(activeZone, sampleY);
        const lum = (0.299 * rgb[0] + 0.587 * rgb[1] + 0.114 * rgb[2]) / 255;
        const nextTheme = lum < 0.58 ? 'dark' : 'light';

        header.style.setProperty('--nav-bg', `rgb(${rgb[0]}, ${rgb[1]}, ${rgb[2]})`);
        header.dataset.theme = nextTheme;
    };

    const onScrollOrResize = () => {
        if (!ticking) {
            window.requestAnimationFrame(applyState);
            ticking = true;
        }
    };

    applyState();
    window.addEventListener('scroll', onScrollOrResize, { passive: true });
    window.addEventListener('resize', onScrollOrResize, { passive: true });

    return () => {
        window.removeEventListener('scroll', onScrollOrResize);
        window.removeEventListener('resize', onScrollOrResize);
    };
}
