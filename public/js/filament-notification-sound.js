(function () {
    let lastCount = null;

    const checkBell = () => {
        if (document.visibilityState !== 'visible') {
            return;
        }

        const badge = document.querySelector(
            '[aria-label*="Notifikasi"], .fi-topbar-database-notifications-btn .fi-badge'
        );
        const text = badge ? badge.textContent.trim() : '';
        const count = text ? parseInt(text, 10) || 0 : 0;

        if (lastCount !== null && count > lastCount) {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.08, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.25);
            } catch (_) {
                // Abaikan jika browser memblokir AudioContext sebelum interaksi pengguna
            }
        }

        lastCount = count;
    };

    setInterval(checkBell, 30000);
})();
