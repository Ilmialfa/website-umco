import { formatRupiah, loadNota, saveNota } from '../lib/formatters.js';

export function compressProofImage(file, maxDim = 1600, quality = 0.84) {
    return new Promise((resolve) => {
        if (!file || !file.type || !file.type.startsWith('image/')) {
            resolve(file);
            return;
        }
        if (/heic|heif/i.test(file.type) || /\.heic$/i.test(file.name)) {
            resolve(file);
            return;
        }

        const reader = new FileReader();
        reader.onerror = () => resolve(file);
        reader.onload = () => {
            const img = new Image();
            img.onerror = () => resolve(file);
            img.onload = () => {
                const { width, height } = img;
                const scale = Math.min(1, maxDim / Math.max(width, height));
                const w = Math.max(1, Math.round(width * scale));
                const h = Math.max(1, Math.round(height * scale));

                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    resolve(file);
                    return;
                }
                ctx.fillStyle = '#FCFBF7';
                ctx.fillRect(0, 0, w, h);
                ctx.drawImage(img, 0, 0, w, h);

                canvas.toBlob(
                    (blob) => {
                        if (!blob) {
                            resolve(file);
                            return;
                        }
                        const cleanName = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                        resolve(new File([blob], cleanName, { type: 'image/jpeg' }));
                    },
                    'image/jpeg',
                    quality
                );
            };
            img.src = reader.result;
        };
        reader.readAsDataURL(file);
    });
}

export function downloadKarcisPng(data) {
    const W = 720;
    const H = 980;
    const canvas = document.createElement('canvas');
    canvas.width = W;
    canvas.height = H;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Latar kertas luar Warm Alabaster
    ctx.fillStyle = '#F7F5F0';
    ctx.fillRect(0, 0, W, H);

    const padX = 48;
    const padY = 40;
    const cardW = W - padX * 2;
    const cardH = H - padY * 2;

    // Karcis Putih Bersih + Aksen Amber Atas
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(padX, padY, cardW, cardH);
    ctx.fillStyle = '#C4933F';
    ctx.fillRect(padX, padY, cardW, 8);

    ctx.fillStyle = '#161615';
    ctx.font = '600 20px "Plus Jakarta Sans", sans-serif';
    ctx.fillText('UMCO HOUSE', padX + 36, padY + 62);

    ctx.fillStyle = '#A87A2C';
    ctx.font = '600 26px "Reenie Beanie", cursive';
    ctx.textAlign = 'right';
    ctx.fillText(data.code || 'No. ......', padX + cardW - 36, padY + 62);
    ctx.textAlign = 'left';

    // Judul karcis
    ctx.fillStyle = '#161615';
    ctx.font = '600 40px "Plus Jakarta Sans", sans-serif';
    ctx.fillText('Karcis meja', padX + 36, padY + 116);

    // Stempel kanan atas
    if (data.stamp) {
        ctx.save();
        ctx.translate(padX + cardW - 140, padY + 108);
        ctx.rotate(-0.08);
        ctx.strokeStyle = data.stampHex || '#A87A2C';
        ctx.lineWidth = 2.5;
        ctx.strokeRect(-72, -18, 144, 34);
        ctx.fillStyle = data.stampHex || '#A87A2C';
        ctx.font = '600 13px "Plus Jakarta Sans", sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(data.stamp, 0, 4);
        ctx.restore();
    }

    const lines = [
        ['Hari', data.dateLabel || '-'],
        ['Jam', (data.time || '-') + ' WIB'],
        ['Orang', (data.partySize || 2) + ' orang'],
        ['Tempat', data.seatingLabel || 'Bebas di mana saja'],
        ['Nama', data.name || '-'],
        ['WA', data.phoneDisplay || '-'],
        ['Pesanan', data.itemsSummary || '-'],
        ['Total', formatRupiah(Number(data.subtotal || 0))],
        ['Bayar', (data.payLabel || 'DP') + ' · ' + formatRupiah(Number(data.amountDue || 0))],
    ];

    let y = padY + 178;
    lines.forEach(([label, val]) => {
        ctx.fillStyle = '#52514D';
        ctx.font = '600 15px "Instrument Sans", sans-serif';
        ctx.fillText(label, padX + 36, y);

        ctx.fillStyle = '#161615';
        ctx.font = '600 22px "Instrument Sans", sans-serif';
        const text = String(val).slice(0, 36);
        ctx.fillText(text, padX + 132, y);

        ctx.strokeStyle = 'rgba(22,22,21,0.12)';
        ctx.setLineDash([4, 4]);
        ctx.beginPath();
        ctx.moveTo(padX + 36, y + 14);
        ctx.lineTo(padX + cardW - 36, y + 14);
        ctx.stroke();
        ctx.setLineDash([]);

        y += 60;
    });

    ctx.fillStyle = '#52514D';
    ctx.font = '500 15px "Instrument Sans", sans-serif';
    ctx.fillText('Tunjukkan di kasir. Karcis hangus 15 menit setelah jam pesan.', padX + 36, padY + cardH - 52);

    const link = document.createElement('a');
    link.download = `karcis-${data.code || 'umco'}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
}

export function init(el) {
    window.umcoCompressProof = compressProofImage;
    window.umcoDownloadKarcis = downloadKarcisPng;
    window.umcoClearNota = () => saveNota([]);

    const onOpenWa = (e) => {
        const url = e.detail?.url;
        if (url) {
            saveNota([]);
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    };
    window.addEventListener('umco-open-wa', onOpenWa);

    // Hydrate dari localStorage hanya kalau user datang ke /reservasi tanpa ?items=
    if (el && el.dataset.hasQueryItems !== '1' && el.dataset.hydrated !== '1') {
        const nota = loadNota();
        if (Array.isArray(nota) && nota.length > 0 && window.Livewire) {
            el.dataset.hydrated = '1';
            const wireEl = el.closest('[wire\\:id]');
            const comp = wireEl ? window.Livewire.find(wireEl.getAttribute('wire:id')) : null;
            if (comp) {
                comp.call('hydrateNotaItems', nota);
            }
        }
    }

    return () => {
        window.removeEventListener('umco-open-wa', onOpenWa);
    };
}
