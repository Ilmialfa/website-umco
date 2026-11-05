<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Design Specimen — UM.CO House</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-oat text-charcoal font-sans antialiased min-h-screen p-8 lg:p-16">
    <div class="max-w-5xl mx-auto space-y-16">
        <!-- Header -->
        <header class="border-b border-moss/20 pb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-widest text-moss font-semibold">Identitas & Design Tokens</p>
                <h1 class="font-serif text-4xl lg:text-5xl font-bold text-forest mt-1">UM.CO House (UMRI)</h1>
                <p class="text-charcoal-muted mt-2 text-sm max-w-xl">Halaman uji spesimen tipografi, palet warna, dan ritme spasi sebelum perakitan komponen utama.</p>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest text-alabaster text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-crema animate-pulse"></span>
                <span>Fase 1: Verified</span>
            </div>
        </header>

        <!-- Color Palette -->
        <section class="space-y-6">
            <h2 class="font-serif text-2xl font-bold text-forest">Palet Warna Utama</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
                <div class="p-4 rounded-xl bg-forest text-alabaster shadow-sm">
                    <div class="h-16 rounded-lg bg-forest border border-white/10 mb-3"></div>
                    <p class="font-bold text-sm">Forest Deep</p>
                    <p class="text-xs text-sage font-mono mt-0.5">#0F2A1D</p>
                    <p class="text-[11px] text-white/60 mt-1">Latar gelap & footer</p>
                </div>
                <div class="p-4 rounded-xl bg-alabaster border border-moss/20 text-charcoal shadow-sm">
                    <div class="h-16 rounded-lg bg-espresso mb-3"></div>
                    <p class="font-bold text-sm text-espresso">Espresso</p>
                    <p class="text-xs text-charcoal-muted font-mono mt-0.5">#1D3B2A</p>
                    <p class="text-[11px] text-charcoal-muted mt-1">Tombol utama</p>
                </div>
                <div class="p-4 rounded-xl bg-alabaster border border-moss/20 text-charcoal shadow-sm">
                    <div class="h-16 rounded-lg bg-moss mb-3"></div>
                    <p class="font-bold text-sm text-moss">Moss</p>
                    <p class="text-xs text-charcoal-muted font-mono mt-0.5">#3E5C45</p>
                    <p class="text-[11px] text-charcoal-muted mt-1">Garis & ikon</p>
                </div>
                <div class="p-4 rounded-xl bg-alabaster border border-moss/20 text-charcoal shadow-sm">
                    <div class="h-16 rounded-lg bg-sage mb-3"></div>
                    <p class="font-bold text-sm text-forest">Muted Sage</p>
                    <p class="text-xs text-charcoal-muted font-mono mt-0.5">#A8BBA2</p>
                    <p class="text-[11px] text-charcoal-muted mt-1">Latar lembut</p>
                </div>
                <div class="p-4 rounded-xl bg-alabaster border border-moss/20 text-charcoal shadow-sm">
                    <div class="h-16 rounded-lg bg-oat border border-moss/10 mb-3"></div>
                    <p class="font-bold text-sm text-forest">Oat Cream</p>
                    <p class="text-xs text-charcoal-muted font-mono mt-0.5">#F1ECDF</p>
                    <p class="text-[11px] text-charcoal-muted mt-1">Latar utama</p>
                </div>
                <div class="p-4 rounded-xl bg-white border border-moss/20 text-charcoal shadow-sm">
                    <div class="h-16 rounded-lg bg-alabaster border border-moss/10 mb-3"></div>
                    <p class="font-bold text-sm text-forest">Alabaster</p>
                    <p class="text-xs text-charcoal-muted font-mono mt-0.5">#FAF7F0</p>
                    <p class="text-[11px] text-charcoal-muted mt-1">Permukaan kartu</p>
                </div>
                <div class="p-4 rounded-xl bg-alabaster border border-moss/20 text-charcoal shadow-sm">
                    <div class="h-16 rounded-lg bg-crema mb-3"></div>
                    <p class="font-bold text-sm text-forest">Crema Accent</p>
                    <p class="text-xs text-charcoal-muted font-mono mt-0.5">#D3B98C</p>
                    <p class="text-[11px] text-charcoal-muted mt-1">Aksen badge</p>
                </div>
            </div>
        </section>

        <!-- Typography Scale -->
        <section class="space-y-6">
            <h2 class="font-serif text-2xl font-bold text-forest">Tipografi: Fraunces (Serif) & Instrument Sans</h2>
            <div class="p-8 rounded-2xl bg-alabaster border border-moss/20 space-y-8">
                <div>
                    <p class="text-xs text-moss font-semibold uppercase tracking-wider mb-1">Display Hero — Fraunces 600</p>
                    <h2 class="font-serif text-4xl lg:text-6xl text-forest font-semibold leading-[1.1] max-w-2xl">
                        Ruang tenang untuk nugas, tempat ramai untuk bercerita.
                    </h2>
                </div>

                <div>
                    <p class="text-xs text-moss font-semibold uppercase tracking-wider mb-1">Heading Seksi — Fraunces 700</p>
                    <h3 class="font-serif text-2xl lg:text-3xl text-forest font-bold">
                        Denah Gedung H: Dua Suasana Berbeda di Jantung Kampus UMRI
                    </h3>
                </div>

                <div>
                    <p class="text-xs text-moss font-semibold uppercase tracking-wider mb-1">Body Text — Instrument Sans 400</p>
                    <p class="text-charcoal leading-relaxed max-w-2xl">
                        UM.CO House dirancang untuk menjawab ritme mahasiswa dan sivitas akademika: pagi hingga sore yang membutuhkan fokus tinggi dengan koneksi listrik andal dan AC sejuk di area indoor, serta malam yang santai di area outdoor terbuka bersama aneka jajanan street food pilihan.
                    </p>
                </div>

                <div>
                    <p class="text-xs text-moss font-semibold uppercase tracking-wider mb-2">Angka Harga Tabular (tabular-nums)</p>
                    <div class="space-y-2 max-w-md text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Cafe Latte (Double Shot)</span>
                            <span class="font-mono tabular-nums font-semibold text-forest">Rp 28.000</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Artisan Donut Glazed Cinnamon</span>
                            <span class="font-mono tabular-nums font-semibold text-forest">Rp 18.000</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium">Rice Bowl Ayam Sambal Matah</span>
                            <span class="font-mono tabular-nums font-semibold text-forest">Rp 35.000</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Components & Buttons -->
        <section class="space-y-6">
            <h2 class="font-serif text-2xl font-bold text-forest">Spesimen Komponen UI</h2>
            <div class="p-8 rounded-2xl bg-alabaster border border-moss/20 flex flex-wrap items-center gap-4">
                <a href="#" class="px-6 py-3 rounded-full bg-forest text-alabaster font-medium text-sm hover:bg-espresso transition duration-200">
                    Reservasi Meja
                </a>
                <a href="#" class="px-6 py-3 rounded-full border border-forest text-forest font-medium text-sm hover:bg-forest/5 transition duration-200">
                    Lihat Menu Lengkap
                </a>
                <span class="px-3 py-1 rounded-full bg-crema/40 text-forest text-xs font-semibold">
                    Best Seller
                </span>
                <span class="px-3 py-1 rounded-full bg-moss/20 text-moss text-xs font-semibold">
                    Baru
                </span>
                <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-500 text-xs font-semibold">
                    Habis Hari Ini
                </span>
            </div>
        </section>
    </div>
</body>
</html>
