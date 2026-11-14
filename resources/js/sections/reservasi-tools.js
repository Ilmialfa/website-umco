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

    // Latar kertas luar
    ctx.fillStyle = '#F5F2EA';
    ctx.fillRect(0, 0, W, H);

    const padX = 48;
    const padY = 40;
    const cardW = W - padX * 2;
    const cardH = H - padY * 2;

    // Karcis susu-stroberi dengan gerigi atas & bawah
    ctx.fillStyle = '#F0D2C9';
    ctx.beginPath();
    const teeth = 26;
    const step = cardW / teeth;
    ctx.moveTo(padX, padY + 8);
    for (let i = 0; i < teeth; i++) {
        ctx.lineTo(padX + i * step + step * 0.5, padY);
        ctx.lineTo(padX + (i + 1) * step, padY + 8);
    }
    ctx.lineTo(padX + cardW, padY + cardH - 8);
    for (let i = teeth; i > 0; i--) {
        ctx.lineTo(padX + i * step - step * 0.5, padY + cardH);
        ctx.lineTo(padX + (i - 1) * step, padY + cardH - 8);
    }
    ctx.closePath();
    ctx.fill();

    ctx.fillStyle = '#22263A';
    ctx.font = '800 22px Anybody, Arial, sans-serif';
    ctx.fillText('UMCO HOUSE', padX + 36, padY + 62);

    ctx.fillStyle = '#C4472E';
    ctx.font = '600 28px "Reenie Beanie", cursive';
    ctx.textAlign = 'right';
    ctx.fillText(data.code || 'No. ......', padX + cardW - 36, padY + 62);
    ctx.textAlign = 'left';

    // Judul karcis
    ctx.fillStyle = '#22263A';
    ctx.font = '800 44px Anybody, Arial, sans-serif';
    ctx.fillText('Karcis meja', padX + 36, padY + 116);

    // Stempel kanan atas
    if (data.stamp) {
        ctx.save();
        ctx.translate(padX + cardW - 140, padY + 108);
        ctx.rotate(-0.11);
        ctx.strokeStyle = data.stampHex || '#C4472E';
        ctx.lineWidth = 3;
        ctx.strokeRect(-72, -18, 144, 34);
        ctx.fillStyle = data.stampHex || '#C4472E';
        ctx.font = '800 13px Anybody, Arial, sans-serif';
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
        ctx.fillStyle = 'rgba(34,38,58,0.65)';
        ctx.font = '600 15px "Familjen Grotesk", sans-serif';
        ctx.fillText(label, padX + 36, y);

        ctx.fillStyle = '#22263A';
        ctx.font = '600 22px "Familjen Grotesk", sans-serif';
        const text = String(val).slice(0, 36);
        ctx.fillText(text, padX + 132, y);

        ctx.strokeStyle = 'rgba(34,38,58,0.22)';
        ctx.setLineDash([4, 4]);
        ctx.beginPath();
        ctx.moveTo(padX + 36, y + 14);
        ctx.lineTo(padX + cardW - 36, y + 14);
        ctx.stroke();
        ctx.setLineDash([]);

        y += 60;
    });

    ctx.fillStyle = 'rgba(34,38,58,0.8)';
    ctx.font = '500 16px "Familjen Grotesk", sans-serif';
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
