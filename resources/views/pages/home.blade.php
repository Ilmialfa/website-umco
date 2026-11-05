<x-layouts.public>
    <!-- 1. HERO SECTION (SECTION 4.2) -->
    <section class="relative min-h-[92vh] flex items-center pt-28 pb-16 lg:pt-36 lg:pb-24 overflow-hidden" style="background: linear-gradient(180deg, #0F2A1D 0%, #153827 55%, #F1ECDF 100%);">
        <!-- Ambient Background Glow & Geometric lines -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-crema/30 blur-3xl"></div>
            <div class="absolute top-1/3 -right-20 w-80 h-80 rounded-full bg-forest-deep blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                <!-- Left Column: Headline, Narrative & Dual CTA -->
                <div class="lg:col-span-7 space-y-6 lg:space-y-8 hero-fade-in">
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs text-crema font-semibold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-crema"></span>
                        <span>Di Dalam Kampus Universitas Muhammadiyah Riau (UMRI)</span>
                    </div>

                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold text-alabaster leading-[1.08] tracking-tight">
                        {{ \App\Support\Settings::get('brand.hero_headline', 'Ruang tenang untuk nugas, tempat ramai untuk bercerita.') }}
                    </h1>

                    <p class="text-base sm:text-lg text-alabaster/85 max-w-xl leading-relaxed font-normal">
                        {{ \App\Support\Settings::get('brand.hero_description', 'Coffee shop modern berarsitektur huruf H di jantung Kampus UMRI Pekanbaru. Pilihan indoor full AC untuk nugas dan kerja, berpadu outdoor street food yang hidup sejak sore hingga malam.') }}
                    </p>

                    <!-- Dual CTA -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a
                            href="/reservasi"
                            wire:navigate
                            class="inline-flex items-center justify-center px-7 py-3.5 rounded-full bg-crema text-forest font-bold text-sm tracking-wide hover:bg-crema-dark hover:text-alabaster transition duration-200 shadow-md group"
                        >
                            <span>Reservasi Meja</span>
                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>

                        <a
                            href="/menu"
                            wire:navigate
                            class="inline-flex items-center justify-center px-7 py-3.5 rounded-full border border-alabaster/30 text-alabaster font-semibold text-sm tracking-wide hover:bg-white/10 hover:border-alabaster/60 transition duration-200"
                        >
                            Lihat Katalog Menu
                        </a>
                    </div>

                    <!-- Meta Badges on Hero -->
                    <div class="pt-6 flex flex-wrap items-center gap-6 text-xs text-alabaster/70 border-t border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-alabaster">Dine-in &amp; Takeaway</span>
                        </div>
                        <span class="text-white/20">•</span>
                        <div class="flex items-center gap-2">
                            <span>Harga Rp 25.000 – Rp 50.000</span>
                        </div>
                        <span class="text-white/20">•</span>
                        <div class="flex items-center gap-2">
                            <span>Buka Sampai 21.00 WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Masked with H-Architecture Motif & Rotating Badge -->
                <div class="lg:col-span-5 relative hero-fade-in flex justify-center">
                    <!-- Rotating Circular Badge: "4.9 Google Maps" (Section 4.2) -->
                    <div class="absolute -top-6 -right-2 sm:right-6 z-20 pointer-events-none">
                        <div class="relative w-28 h-28 flex items-center justify-center">
                            <!-- Circular rotating text container -->
                            <div class="absolute inset-0 animate-[spin_18s_linear_infinite]">
                                <svg viewBox="0 0 100 100" class="w-full h-full fill-current text-crema">
                                    <path id="circlePath" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0" fill="none"/>
                                    <text font-size="9" font-weight="700" letter-spacing="2.5">
                                        <textPath href="#circlePath" startOffset="0%">
                                            ★ 4.9 GOOGLE MAPS • 86+ ULASAN •
                                        </textPath>
                                    </text>
                                </svg>
                            </div>
                            <!-- Center Rating Number -->
                            <div class="w-12 h-12 rounded-full bg-forest border border-crema/40 flex flex-col items-center justify-center shadow-lg text-alabaster">
                                <span class="font-serif text-sm font-bold text-crema">4.9</span>
                                <span class="text-[9px] text-sage">★</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Visual Frame with Architectural Shadow -->
                    <div class="w-full max-w-md rounded-3xl overflow-hidden shadow-2xl border border-white/15 bg-forest-deep">
                        <img
                            src="{{ asset('/images/hero-art.svg') }}"
                            alt="UM.CO House Architectural Composition"
                            class="w-full h-auto object-cover transform hover:scale-102 transition duration-700"
                            fetchpriority="high"
                        />
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. DYNAMIC MULTI-PURPOSE BANNERS (SECTION 6.2) -->
    @if($banners->count() > 0)
    <section class="py-8 bg-oat border-b border-moss/10" x-data="{ current: 0, total: {{ $banners->count() }} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-2xl bg-alabaster border border-moss/15 shadow-sm p-6 sm:p-8">
                @foreach($banners as $index => $banner)
                    <div
                        x-show="current === {{ $index }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="flex flex-col md:flex-row md:items-center justify-between gap-6"
                        @if($index > 0) style="display: none;" @endif
                    >
                        <div class="space-y-2 max-w-2xl">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-moss/10 text-moss">
                                    Info UMCO
                                </span>
                                <h3 class="font-serif text-xl sm:text-2xl font-bold text-forest">
                                    {{ $banner->title }}
                                </h3>
                            </div>
                            <p class="text-sm text-charcoal-muted leading-relaxed">
                                {{ $banner->description }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4 shrink-0">
                            @if($banner->cta_url)
                                <a
                                    href="{{ $banner->cta_url }}"
                                    wire:navigate
                                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-forest text-alabaster text-xs font-semibold hover:bg-espresso transition duration-200"
                                >
                                    <span>{{ $banner->cta_label }}</span>
                                    <svg class="w-3.5 h-3.5 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif

                            @if($banners->count() > 1)
                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        @click="current = (current === 0) ? (total - 1) : (current - 1)"
                                        class="p-2 rounded-full border border-gray-300 hover:bg-gray-100 text-charcoal transition"
                                        aria-label="Banner Sebelumnya"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        @click="current = (current === total - 1) ? 0 : (current + 1)"
                                        class="p-2 rounded-full border border-gray-300 hover:bg-gray-100 text-charcoal transition"
                                        aria-label="Banner Selanjutnya"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 3. CUPLIKAN CERITA: GEDUNG HURUF H (SECTION 6.2) -->
    <section class="py-20 lg:py-28 bg-oat">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                <!-- Left Column: Story Text -->
                <div class="lg:col-span-6 space-y-6">
                    <p class="text-xs uppercase tracking-widest text-moss font-bold">Cerita Arsitektur Kami</p>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest leading-tight">
                        Arsitektur Huruf H: Dua Karakter di Jantung Kampus UMRI
                    </h2>
                    <p class="text-charcoal leading-relaxed text-base">
                        UM.CO House tidak dibangun seperti kafe ruko pada umumnya. Mengambil bentuk denah huruf H di tengah Kampus Universitas Muhammadiyah Riau, tata ruang kami memisahkan dua kebutuhan mahasiswa dan pengunjung secara harmonis:
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-5 rounded-2xl bg-alabaster border border-moss/15 shadow-sm space-y-2">
                            <span class="w-3 h-3 rounded-full bg-forest inline-block"></span>
                            <h4 class="font-serif font-bold text-forest text-lg">Sayap Indoor (Full AC)</h4>
                            <p class="text-xs text-charcoal-muted leading-relaxed">
                                Suasana sejuk, tenang, dan tertata rapi. Didesain khusus untuk laptopan, menyelesaikan skripsi, atau diskusi terarah tanpa distraksi.
                            </p>
                        </div>
                        <div class="p-5 rounded-2xl bg-alabaster border border-moss/15 shadow-sm space-y-2">
                            <span class="w-3 h-3 rounded-full bg-crema-dark inline-block"></span>
                            <h4 class="font-serif font-bold text-forest text-lg">Sayap Outdoor (Street Food)</h4>
                            <p class="text-xs text-charcoal-muted leading-relaxed">
                                Area terbuka yang adem dan hidup sejak sore. Dikelilingi aneka stan jajanan street food, pas untuk melepas lelah kuliah bersama kawan.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a
                            href="/about"
                            wire:navigate
                            class="inline-flex items-center text-sm font-bold text-forest hover:text-moss transition group"
                        >
                            <span>Pelajari Selengkapnya Tentang Cerita Kami</span>
                            <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Visual Architecture Graphic -->
                <div class="lg:col-span-6">
                    <div class="relative p-8 rounded-3xl bg-forest text-alabaster overflow-hidden shadow-xl">
                        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-moss/30 blur-2xl"></div>

                        <p class="text-xs uppercase tracking-widest text-crema font-bold mb-4">Denah Konseptual Huruf H</p>
                        <!-- Visual Floorplan Diagram -->
                        <div class="relative aspect-[4/3] rounded-2xl bg-forest-deep/60 border border-white/10 p-6 flex flex-col justify-between">
                            <div class="flex justify-between items-start text-xs text-sage">
                                <span>Jl. Tuanku Tambusai (Gerbang Utama UMRI)</span>
                                <span>Area Parkir Kampus</span>
                            </div>

                            <!-- Simplified H Architecture Blueprint Grid -->
                            <div class="flex items-center justify-center my-auto w-full">
                                <div class="grid grid-cols-5 gap-2 w-full max-w-sm h-48 items-center text-center">
                                    <!-- Left wing: Indoor -->
                                    <div class="col-span-2 h-full rounded-xl bg-forest/80 border-2 border-sage/40 flex flex-col items-center justify-center p-3 text-alabaster">
                                        <span class="text-xs uppercase font-bold text-crema">Sayap Kiri</span>
                                        <span class="font-serif font-bold text-sm mt-1">Indoor AC</span>
                                        <span class="text-[10px] text-sage mt-1">Colokan &amp; Nugas</span>
                                    </div>
                                    <!-- Center link: Barista -->
                                    <div class="col-span-1 h-16 rounded-lg bg-crema/20 border-2 border-crema/60 flex flex-col items-center justify-center p-1 text-[10px] text-crema font-semibold">
                                        Brew Bar
                                    </div>
                                    <!-- Right wing: Outdoor -->
                                    <div class="col-span-2 h-full rounded-xl bg-forest/80 border-2 border-crema/40 flex flex-col items-center justify-center p-3 text-alabaster">
                                        <span class="text-xs uppercase font-bold text-crema">Sayap Kanan</span>
                                        <span class="font-serif font-bold text-sm mt-1">Outdoor</span>
                                        <span class="text-[10px] text-sage mt-1">Street Food Stands</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-end text-xs text-sage/80 pt-2 border-t border-white/10">
                                <span>Kapasitas 40 Kursi per Slot</span>
                                <span>Buka Setiap Hari 08.00 - 21.00 WIB</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 4. SOROTAN MENU FAVORIT & UNGGULAN (SECTION 6.2) -->
    <section class="py-20 bg-alabaster border-y border-moss/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-12 border-b border-moss/15">
                <div>
                    <p class="text-xs uppercase tracking-widest text-moss font-bold">Kurasi Pilihan</p>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest mt-1">
                        Menu Jagoan &amp; Barista's Pick
                    </h2>
                </div>
                <a
                    href="/menu"
                    wire:navigate
                    class="inline-flex items-center text-sm font-bold text-forest hover:text-moss transition group shrink-0"
                >
                    <span>Lihat Seluruh 20+ Menu</span>
                    <svg class="w-4 h-4 ml-1.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <!-- Asymmetric Grid of Featured Menu Items -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pt-10">
                @foreach($favoriteMenuItems as $item)
                    <div class="p-6 rounded-2xl bg-oat/50 border border-moss/15 hover:border-moss/40 hover:bg-white transition-all duration-300 flex flex-col justify-between group">
                        <div class="space-y-4">
                            <!-- Category & Badge -->
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-charcoal-muted">
                                    {{ $item->category->name ?? 'Menu' }}
                                </span>
                                @if($item->is_favorite)
                                    <span class="px-2.5 py-0.5 rounded-full bg-crema/40 text-forest text-[11px] font-bold">
                                        Best Seller
                                    </span>
                                @elseif($item->is_new)
                                    <span class="px-2.5 py-0.5 rounded-full bg-moss/20 text-moss text-[11px] font-bold">
                                        Baru
                                    </span>
                                @endif
                            </div>

                            <!-- Name & Price with Classical Dot Leader -->
                            <div class="space-y-1">
                                <div class="flex items-baseline justify-between gap-3">
                                    <h3 class="font-serif text-xl font-bold text-forest group-hover:text-espresso transition">
                                        {{ $item->name }}
                                    </h3>
                                    <span class="font-mono tabular-nums text-base font-bold text-forest shrink-0">
                                        {{ $item->formatted_price }}
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-charcoal-muted leading-relaxed line-clamp-2">
                                    {{ $item->description }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Link to Reservation -->
                        <div class="pt-6 border-t border-moss/10 flex items-center justify-between mt-4">
                            <span class="text-xs text-charcoal-muted">Sedia: Dine-in &amp; Takeaway</span>
                            <a
                                href="/reservasi?menu={{ $item->slug }}"
                                wire:navigate
                                class="text-xs font-bold text-forest hover:text-moss transition inline-flex items-center gap-1"
                            >
                                <span>Pesan via Reservasi</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. SEKSI SUASANA & PEMILIH SUASANA CLIENT-SIDE (IDE 5.3) -->
    <section class="py-20 lg:py-28 bg-oat" x-data="{
        selectedMood: 'nugas',
        moods: {
            nugas: {
                title: 'Sesi Nugas &amp; Kerja Fokus',
                area: 'Sayap Indoor (Full AC)',
                desc: 'Meja luas, colokan listrik di setiap sudut, koneksi Wi-Fi stabil, dan suasana hening yang mendukung konsentrasi penuh.',
                recommendations: ['Cafe Latte Double Shot', 'Kopi Susu Aren UMCO', 'Donat Brulee Cinnamon']
            },
            sore: {
                title: 'Santai Sore &amp; Melepas Penat',
                area: 'Sayap Outdoor / Lorong Tengah',
                desc: 'Angin sepoi sore kampus dengan obrolan ringan ditemani minuman segar dingin dan kudapan renyah.',
                recommendations: ['Lychee Sparkling Yakult', 'Tahu Walik Sambal Kecap', 'Cireng Salju Bumbu Rujak']
            },
            malam: {
                title: 'Ngobrol Malam &amp; Street Food',
                area: 'Sayap Outdoor Street Food Stands',
                desc: 'Lampu gantung temaram, aroma panggangan kuliner malam, dan kehangatan temu kawan setelah seharian sibuk.',
                recommendations: ['Rice Bowl Ayam Sambal Matah', 'Spanish Latte UMRI', 'Crispy Chicken Popcorn BBQ']
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto space-y-4 pb-12">
                <p class="text-xs uppercase tracking-widest text-moss font-bold">Eksplorasi Suasana</p>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest">
                    Mau Suasana Seperti Apa Hari Ini?
                </h2>
                <p class="text-sm text-charcoal-muted">
                    Pilih tujuan kunjunganmu, kami siapkan rekomendasi sudut meja dan menu yang paling pas.
                </p>

                <!-- 3 Mood Buttons -->
                <div class="inline-flex p-1.5 rounded-full bg-alabaster border border-moss/20 shadow-sm gap-1 mt-4">
                    <button
                        type="button"
                        @click="selectedMood = 'nugas'"
                        :class="selectedMood === 'nugas' ? 'bg-forest text-alabaster font-bold shadow-sm' : 'text-charcoal hover:text-forest'"
                        class="px-5 py-2 rounded-full text-xs transition duration-200"
                    >
                        Mau Nugas Fokus
                    </button>
                    <button
                        type="button"
                        @click="selectedMood = 'sore'"
                        :class="selectedMood === 'sore' ? 'bg-forest text-alabaster font-bold shadow-sm' : 'text-charcoal hover:text-forest'"
                        class="px-5 py-2 rounded-full text-xs transition duration-200"
                    >
                        Santai Sore
                    </button>
                    <button
                        type="button"
                        @click="selectedMood = 'malam'"
                        :class="selectedMood === 'malam' ? 'bg-forest text-alabaster font-bold shadow-sm' : 'text-charcoal hover:text-forest'"
                        class="px-5 py-2 rounded-full text-xs transition duration-200"
                    >
                        Ngobrol Malam
                    </button>
                </div>
            </div>

            <!-- Mood Showcase Card -->
            <div class="max-w-4xl mx-auto p-8 rounded-3xl bg-alabaster border border-moss/20 shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    <div class="md:col-span-7 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-moss/10 text-moss text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-moss"></span>
                            <span x-text="moods[selectedMood].area"></span>
                        </div>
                        <h3 class="font-serif text-2xl sm:text-3xl font-bold text-forest" x-html="moods[selectedMood].title"></h3>
                        <p class="text-sm text-charcoal-muted leading-relaxed" x-text="moods[selectedMood].desc"></p>

                        <div class="pt-2">
                            <p class="text-xs font-bold uppercase tracking-wider text-charcoal mb-2">Rekomendasi Menu:</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="item in moods[selectedMood].recommendations" :key="item">
                                    <span class="px-3 py-1 rounded-lg bg-oat text-forest text-xs font-medium border border-moss/15" x-text="item"></span>
                                </template>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a
                                href="/reservasi"
                                wire:navigate
                                class="inline-flex items-center px-5 py-2.5 rounded-full bg-forest text-alabaster text-xs font-bold hover:bg-espresso transition"
                            >
                                Reservasi Meja Suasana Ini &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Hourly Estimation Simple Bar Chart (Ide 5.3 poin 4) -->
                    <div class="md:col-span-5 p-6 rounded-2xl bg-oat/60 border border-moss/15 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-forest">Estimasi Jam Ramai</span>
                            <span class="text-[10px] text-charcoal-muted">(Prakiraan)</span>
                        </div>
                        <!-- Mini bar graph -->
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-14 text-charcoal-muted">08 - 11</span>
                                <div class="flex-grow bg-white rounded-full h-3 overflow-hidden">
                                    <div class="bg-sage h-full rounded-full" style="width: 45%;"></div>
                                </div>
                                <span class="w-12 text-right text-charcoal-muted">Tenang</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-14 text-charcoal-muted">11 - 15</span>
                                <div class="flex-grow bg-white rounded-full h-3 overflow-hidden">
                                    <div class="bg-moss h-full rounded-full" style="width: 75%;"></div>
                                </div>
                                <span class="w-12 text-right text-charcoal-muted">Ramai</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-14 text-charcoal-muted">15 - 18</span>
                                <div class="flex-grow bg-white rounded-full h-3 overflow-hidden">
                                    <div class="bg-moss h-full rounded-full" style="width: 85%;"></div>
                                </div>
                                <span class="w-12 text-right text-charcoal-muted">Puncak</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-14 text-charcoal-muted">18 - 21</span>
                                <div class="flex-grow bg-white rounded-full h-3 overflow-hidden">
                                    <div class="bg-forest h-full rounded-full" style="width: 90%;"></div>
                                </div>
                                <span class="w-12 text-right font-bold text-forest">Sangat Ramai</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-charcoal-muted italic">
                            Disarankan reservasi meja jika datang pada sore atau malam hari.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. ULASAN GOOGLE MAPS 4.9★ (SECTION 6.2) -->
    <section class="py-20 bg-alabaster border-b border-moss/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-12 border-b border-moss/15">
                <div>
                    <div class="inline-flex items-center gap-2 text-amber-600 font-bold text-sm mb-1">
                        <span>★★★★★</span>
                        <span class="text-charcoal font-semibold text-xs">Rating {{ \App\Support\Settings::get('rating.google_rating', '4.9') }} / 5.0 (86+ Ulasan Google)</span>
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest">
                        Kata Mereka Tentang UM.CO House
                    </h2>
                </div>
                <a
                    href="{{ \App\Support\Settings::get('contact.google_maps_url', '#') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center text-sm font-bold text-forest hover:text-moss transition group shrink-0"
                >
                    <span>Baca di Google Maps</span>
                    <svg class="w-4 h-4 ml-1.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            <!-- Review Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-10">
                @foreach($testimonials as $t)
                    <div class="p-6 rounded-2xl bg-oat/40 border border-moss/15 flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-amber-500 text-sm tracking-wider">★★★★★</span>
                                @if($t->is_sample)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-200 text-gray-600">
                                        Contoh
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs sm:text-sm text-charcoal leading-relaxed">
                                "{{ $t->body }}"
                            </p>
                        </div>
                        <div class="pt-4 border-t border-moss/10">
                            <p class="font-bold text-sm text-forest">{{ $t->name }}</p>
                            <p class="text-[11px] text-charcoal-muted">{{ $t->role }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 7. AJAK KOLABORASI KAMPUS (COLLAB WITH UM.CO - SECTION 6.2) -->
    <section class="py-16 bg-forest text-alabaster">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-8 sm:p-12 rounded-3xl bg-forest-deep border border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="space-y-3 max-w-2xl">
                    <span class="px-3 py-1 rounded-full bg-crema/20 text-crema text-xs font-semibold">
                        Kolaborasi &amp; Acara Kampus
                    </span>
                    <h3 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-alabaster">
                        Bikin Acara Komunitas atau Diskusi di UM.CO House?
                    </h3>
                    <p class="text-sm text-sage leading-relaxed">
                        Kami terbuka berkolaborasi dengan BEM, Himpunan Mahasiswa UMRI, musisi akustik, komunitas kreatif Pekanbaru, dan pegiat literasi. Diskusikan rencana acaramu langsung bersama kami.
                    </p>
                </div>

                @php
                    $collabTemplate = \App\Support\Settings::get('contact.collab_template', 'Halo UM.CO House, saya [nama] dari [himpunan/komunitas]. Kami ingin berkolaborasi / mengadakan acara di UM.CO House. Boleh minta info dan syarat ketentuannya?');
                    $collabWa = \App\Support\Settings::get('contact.phone_wa', '082248001571');
                    $collabLink = \App\Support\PhoneNumber::waLink($collabWa, $collabTemplate);
                @endphp

                <div class="shrink-0">
                    <a
                        href="{{ $collabLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center px-7 py-3.5 rounded-full bg-crema text-forest font-bold text-sm tracking-wide hover:bg-crema-dark hover:text-white transition shadow-lg"
                    >
                        <span>Chat WhatsApp Kolaborasi</span>
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. TEASER RESERVASI CEPAT (SECTION 6.2 ITEM 8) -->
    <section class="py-20 bg-oat" x-data="{
        date: '{{ now()->toDateString() }}',
        time: '14:00',
        pax: 2,
        goToReservation() {
            window.location.href = `/reservasi?date=${this.date}&time=${this.time}&pax=${this.pax}`;
        }
    }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-8 sm:p-12 rounded-3xl bg-alabaster border border-moss/20 shadow-lg text-center space-y-6">
                <div class="max-w-xl mx-auto space-y-2">
                    <p class="text-xs uppercase tracking-widest text-moss font-bold">Cek Ketersediaan Meja</p>
                    <h2 class="font-serif text-3xl sm:text-4xl font-bold text-forest">
                        Amankan Kursimu Sebelum Datang
                    </h2>
                    <p class="text-sm text-charcoal-muted">
                        Pilih tanggal, perkiraan jam tiba, dan jumlah orang untuk memastikan meja siap menyambutmu.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mx-auto text-left">
                    <div>
                        <label class="block text-xs font-semibold text-charcoal mb-1">Tanggal</label>
                        <input type="date" x-model="date" min="{{ now()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm font-medium focus:ring-2 focus:ring-forest focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-charcoal mb-1">Jam Kedatangan</label>
                        <select x-model="time" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm font-medium focus:ring-2 focus:ring-forest focus:outline-none bg-white">
                            <option value="09:00">09:00 WIB (Pagi)</option>
                            <option value="11:00">11:00 WIB (Siang)</option>
                            <option value="14:00">14:00 WIB (Siang)</option>
                            <option value="16:00">16:00 WIB (Sore)</option>
                            <option value="18:30">18:30 WIB (Malam)</option>
                            <option value="20:00">20:00 WIB (Malam)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-charcoal mb-1">Jumlah Orang</label>
                        <select x-model="pax" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm font-medium focus:ring-2 focus:ring-forest focus:outline-none bg-white">
                            <option value="1">1 Orang (Nugas)</option>
                            <option value="2">2 Orang</option>
                            <option value="4">4 Orang (Meja Standar)</option>
                            <option value="6">6 Orang</option>
                            <option value="8">8 Orang (Grup)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <button
                        type="button"
                        @click="goToReservation()"
                        class="inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-forest text-alabaster font-bold text-sm tracking-wide hover:bg-espresso transition shadow-md"
                    >
                        Lanjut ke Formulir Reservasi &rarr;
                    </button>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
