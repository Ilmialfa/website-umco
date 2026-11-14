const SATUAN = [
    '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
    'sepuluh', 'sebelas',
];

export function terbilang(n) {
    const num = Math.floor(Number(n) || 0);
    if (num <= 0) return 'nol rupiah';
    return `${terbilangInt(num).replace(/\s+/g, ' ').trim()} rupiah`;
}

function terbilangInt(n) {
    if (n < 12) return SATUAN[n];
    if (n < 20) return `${terbilangInt(n - 10)} belas`;
    if (n < 100) return `${terbilangInt(Math.floor(n / 10))} puluh ${terbilangInt(n % 10)}`.trim();
    if (n < 200) return `seratus ${terbilangInt(n - 100)}`.trim();
    if (n < 1000) return `${terbilangInt(Math.floor(n / 100))} ratus ${terbilangInt(n % 100)}`.trim();
    if (n < 2000) return `seribu ${terbilangInt(n - 1000)}`.trim();
    if (n < 1000000) return `${terbilangInt(Math.floor(n / 1000))} ribu ${terbilangInt(n % 1000)}`.trim();
    if (n < 1000000000) return `${terbilangInt(Math.floor(n / 1000000))} juta ${terbilangInt(n % 1000000)}`.trim();
    return String(n);
}

export function formatRupiah(n) {
    const val = Math.round(Number(n) || 0);
    return 'Rp ' + val.toLocaleString('id-ID');
}

export function formatRibu(n) {
    const val = Math.round(Number(n) || 0);
    if (val >= 1000) {
        const ribu = val / 1000;
        const formatted = Number.isInteger(ribu) ? String(ribu) : String(ribu).replace('.', ',');
        return `${formatted} ribu`;
    }
    return String(val);
}

const NOTA_STORAGE_KEY = 'umco_nota_v1';

export function loadNota() {
    try {
        const raw = window.localStorage.getItem(NOTA_STORAGE_KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}

export function saveNota(items) {
    try {
        window.localStorage.setItem(NOTA_STORAGE_KEY, JSON.stringify(items || []));
    } catch {
        // abaikan bila private mode memblokir storage
    }
}
