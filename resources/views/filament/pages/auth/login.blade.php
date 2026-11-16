<x-filament-panels::page.simple>
    <div class="umco-login-shell">
        {{-- Panel Kiri: Visual Editorial Suasana UMCO & Kopi --}}
        <aside class="umco-login-visual" aria-label="Suasana UMCO House">
            <div class="umco-login-visual-bg" aria-hidden="true"></div>

            <div class="umco-login-visual-top">
                <span class="umco-login-badge">
                    <span class="umco-login-badge-dot" aria-hidden="true"></span>
                    UMCO HOUSE · KAMPUS 2 UMRI
                </span>
                <span class="umco-login-hours">08.00 – 22.00 WIB</span>
            </div>

            <div class="umco-login-collage">
                <figure class="umco-login-photo-main">
                    <img
                        src="{{ asset('images/bistro-patio-table.webp') }}"
                        alt="Suasana meja luar ruangan UMCO House di Kampus 2 UMRI"
                        width="1200"
                        height="800"
                        decoding="async"
                    >
                </figure>

                <figure class="umco-login-photo-inset">
                    <img
                        src="{{ asset('images/drinks-trio-banner.webp') }}"
                        alt="Tiga racikan minuman andalan UMCO House"
                        width="900"
                        height="600"
                        decoding="async"
                    >
                </figure>
            </div>

            <div class="umco-login-visual-copy">
                <p class="umco-login-hand">~ ruang kendali harian cafe</p>
                <h2 class="umco-login-headline">
                    MEJA SIAP. RACIKAN TERJAGA.
                </h2>
                <p class="umco-login-subcopy">
                    Verifikasi bukti bayar reservasi tamu, kelola stok menu harian, dan perbarui konten editorial UMCO House dari satu meja kerja.
                </p>

                <div class="umco-login-pillars">
                    <div class="umco-login-pillar">
                        <span class="umco-login-pillar-val">Real-Time</span>
                        <span class="umco-login-pillar-lbl">Slot &amp; Status Karcis</span>
                    </div>
                    <div class="umco-login-pillar">
                        <span class="umco-login-pillar-val">RBAC Staf</span>
                        <span class="umco-login-pillar-lbl">Owner · Admin</span>
                    </div>
                    <div class="umco-login-pillar">
                        <span class="umco-login-pillar-val">Diaudit</span>
                        <span class="umco-login-pillar-lbl">Log Aktivitas Penuh</span>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Panel Kanan: Form Autentikasi Staf UMCO --}}
        <section class="umco-login-form-pane" aria-label="Formulir masuk staf">
            <div class="umco-login-form-inner">
                <header class="umco-login-brand">
                    <div class="umco-login-mark-row">
                        <span class="inline-flex items-center gap-2.5">
                            <img src="/images/umco-logo.webp?v=umco2" alt="Logo UMCO" width="32" height="32" class="h-8 w-8 rounded-full object-contain">
                            <span class="umco-login-wordmark">UM.CO</span>
                        </span>
                        <span class="umco-login-role-pill">Portal Staf</span>
                    </div>
                    <h1 class="umco-login-title">MASUK BACKOFFICE</h1>
                    <p class="umco-login-subtitle">
                        Gunakan akun staf resmi yang diberikan oleh Owner cafe.
                    </p>
                </header>

                <div class="umco-login-form-body">
                    {{ $this->content }}
                </div>

                <footer class="umco-login-footer">
                    <div class="umco-login-security-note">
                        <svg class="umco-login-shield" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 2.5L4 5v4.6c0 4 2.55 7.55 6 8.9 3.45-1.35 6-4.9 6-8.9V5l-6-2.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M7.5 10.2l1.7 1.7 3.5-3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Sesi terenkripsi · Pembatasan percobaan masuk &amp; audit IP aktif</span>
                    </div>

                    <a href="{{ url('/') }}" class="umco-login-back">
                        <span aria-hidden="true">&larr;</span>
                        <span>Kembali ke website publik</span>
                    </a>
                </footer>
            </div>
        </section>
    </div>
</x-filament-panels::page.simple>
