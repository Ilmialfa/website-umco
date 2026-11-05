<x-layouts.public>
    <!-- HERO CONTAINER (MELLOW & TARAQ STYLE: ROUNDED FRAME WITH CINEMATIC PHOTOGRAPHY) -->
    <div class="px-3 sm:px-6 pt-4 pb-12">
        <section class="relative min-h-[88vh] lg:min-h-[92vh] rounded-[2rem] sm:rounded-[2.5rem] lg:rounded-[3rem] overflow-hidden bg-[#07241C] text-white flex items-center">
            
            <!-- Background Photographic Layer with Dramatic Warm Grading -->
            <div class="absolute inset-0 z-0">
                <img
                    src="{{ asset('/images/hero-photo.jpg') }}"
                    alt="UM.CO House Coffee & Patio"
                    class="w-full h-full object-cover object-center opacity-35 scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#07241C] via-[#07241C]/80 to-transparent"></div>
            </div>

            <!-- Ambient Glow -->
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#F5B82E]/15 blur-3xl pointer-events-none"></div>

            <div class="max-w-6xl mx-auto px-6 sm:px-10 lg:px-12 py-28 sm:py-36 relative z-10 w-full">
                <div class="max-w-2xl space-y-6">

                    <!-- Top Tag (Mellow style) -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-xs font-bold tracking-widest uppercase">
                        <span class="w-2 h-2 rounded-full bg-[#F5B82E]"></span>
                        <span class="text-white">Kampus UMRI Pekanbaru</span>
                    </div>

                    <!-- Giant Bold Sans-Serif Display Headline (NO SERIFS) -->
                    <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight text-white leading-[1.02]">
                        A Better Place<br/>
                        <span class="text-[#F5B82E]">For Coffee.</span>
                    </h1>

                    <!-- Clean 2-Sentence Punchy Subtitle (Zero AI Clutter) -->
                    <p class="text-base sm:text-lg text-[#9AB4A6] leading-relaxed max-w-lg font-medium">
                        Ruang tenang ber-AC untuk nugas di siang hari, dan courtyard terbuka hijau dengan aneka street food malam hari.
                    </p>

                    <!-- Big Pill Action Buttons -->
                    <div class="pt-4 flex flex-wrap items-center gap-4">
                        <a
                            href="/reservasi"
                            wire:navigate
                            class="px-8 py-4 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 text-center shadow-lg"
                        >
                            Reservasi Meja &rarr;
                        </a>
                        <a
                            href="/menu"
                            wire:navigate
                            class="px-8 py-4 rounded-full text-xs sm:text-sm font-bold tracking-wide bg-white/10 hover:bg-white/20 text-white backdrop-blur-md transition text-center"
                        >
                            Lihat Menu
                        </a>
                    </div>

                    <!-- Quick Rating Sticker (Mochanf style) -->
                    <div class="pt-8 flex items-center gap-6 text-xs text-white/70">
                        <div class="flex items-center gap-2">
                            <span class="font-display font-black text-lg text-white">4.9 ★</span>
                            <span class="text-[#9AB4A6]">Google Review</span>
                        </div>
                        <span class="text-white/20">|</span>
                        <div>
                            <span class="font-display font-black text-lg text-white">08.00 - 21.00</span>
                            <span class="text-[#9AB4A6] block">Buka Setiap Hari</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <!-- 2. PRODUCT SHOWCASE: "GOOD COFFEE, MADE SIMPLY" (TARAQ & BREWHAUS STYLE) -->
    <section class="py-16 lg:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Menu Favorit Kampus</span>
                    <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-[#0D3B2E] mt-1">
                        Good Coffee, Made Simply.
                    </h2>
                </div>
                <a
                    href="/menu"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#0D3B2E] hover:text-[#D99B16] transition"
                >
                    <span>Buka Katalog Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Big Photographic Product Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($favoriteMenuItems->take(4) as $item)
                    <div class="bg-white rounded-3xl p-4 flex flex-col justify-between hover:bg-[#F4EEDF]/40 transition group">
                        <!-- Dominant Image -->
                        <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-[#FAF7EE] mb-4">
                            <img
                                src="{{ $item->image_url }}"
                                alt="{{ $item->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                loading="lazy"
                            />
                            <span class="absolute top-2.5 left-2.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C]">
                                Best Seller
                            </span>
                        </div>

                        <!-- Card Meta: Short, Punchy, No Text Clutter -->
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#145341]">
                                {{ $item->category?->name ?? 'Minuman' }}
                            </span>
                            <h3 class="font-display text-lg font-bold text-[#0D3B2E] truncate">
                                {{ $item->name }}
                            </h3>
                        </div>

                        <!-- Bottom Price & Order Action -->
                        <div class="pt-4 mt-3 border-t border-[#EAE2D0] flex items-center justify-between">
                            <span class="font-display font-black text-base text-[#0D3B2E]">
                                {{ $item->formatted_price }}
                            </span>
                            <a
                                href="/reservasi?menu={{ $item->slug }}"
                                wire:navigate
                                class="w-8 h-8 rounded-full bg-[#0D3B2E] text-white hover:bg-[#F5B82E] hover:text-[#07241C] transition flex items-center justify-center font-bold text-sm"
                                title="Pesan"
                            >
                                +
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. FULL-BLEED PHOTOGRAPHIC ATMOSPHERE SECTION (TARAQ "MEET IN COMFORT" STYLE) -->
    <div class="px-3 sm:px-6 py-6">
        <section class="relative min-h-[60vh] rounded-[2rem] sm:rounded-[2.5rem] overflow-hidden bg-[#0D3B2E] text-white flex items-end">
            <!-- Full Photographic Background -->
            <div class="absolute inset-0 z-0">
                <img
                    src="{{ asset('/images/hero-photo.jpg') }}"
                    alt="Suasana Cafe UMCO"
                    class="w-full h-full object-cover object-center opacity-40 scale-105"
                    loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-[#07241C] via-[#07241C]/60 to-transparent"></div>
            </div>

            <div class="max-w-6xl mx-auto px-6 sm:px-10 lg:px-12 py-16 sm:py-20 relative z-10 w-full flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div class="max-w-xl space-y-3">
                    <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">Arsitektur Huruf H</span>
                    <h2 class="font-display text-3xl sm:text-5xl font-black uppercase tracking-tight leading-none text-white">
                        Meet In Comfort.
                    </h2>
                    <p class="text-sm sm:text-base text-[#9AB4A6] leading-relaxed">
                        Satu gedung, dua zona berbeda: Wing Barat ber-AC untuk laptopan tanpa gangguan suara, dan Wing Timur outdoor dengan aneka jajanan street food sore hari.
                    </p>
                </div>

                <a
                    href="/about"
                    wire:navigate
                    class="px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white text-[#07241C] hover:bg-[#F5B82E] transition flex-shrink-0 text-center"
                >
                    Lihat Denah &amp; Lokasi &rarr;
                </a>
            </div>
        </section>
    </div>

    <!-- 4. FROM BEAN TO CUP 3-STEP VISUAL (TARAQ EDITORIAL STYLE) -->
    <section class="py-16 lg:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-16 space-y-2">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Standar Kualitas</span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-[#0D3B2E]">
                    From Bean To Cup.
                </h2>
                <p class="text-sm text-[#122018]/60">Tiga komitmen di balik setiap tegukan kopi yang kami sajikan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="bg-white rounded-3xl p-8 space-y-3">
                    <span class="font-display text-5xl font-black text-[#145341]/20 block">01</span>
                    <h3 class="font-display text-xl font-bold text-[#0D3B2E] uppercase">Biji Pilihan Sumatera</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed">
                        Kurasi biji Arabica Mandheling dan Gayo grade 1 dengan profil rasa cokelat, rempah, dan aroma karamel yang tebal.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-3xl p-8 space-y-3">
                    <span class="font-display text-5xl font-black text-[#145341]/20 block">02</span>
                    <h3 class="font-display text-xl font-bold text-[#0D3B2E] uppercase">Kalibrasi Presisi Harian</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed">
                        Setiap pagi pukul 07.30 WIB, barista menguji rasio ekstraksi dan suhu air agar rasa cangkir pertamamu konsisten hingga malam hari.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-3xl p-8 space-y-3">
                    <span class="font-display text-5xl font-black text-[#145341]/20 block">03</span>
                    <h3 class="font-display text-xl font-bold text-[#0D3B2E] uppercase">Sajian Hangat Kampus</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed">
                        Dihidangkan segar dalam gelas kaca untuk dine-in santai, atau take-away cepat menuju ruang kuliah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. REAL REVIEWS: "WHY PEOPLE COME BACK" (TARAQ STYLE) -->
    <section class="py-16 lg:py-24 bg-[#F4EEDF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Ulasan Pengunjung</span>
                <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-[#0D3B2E] mt-1">
                    Why People Come Back.
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($testimonials->take(3) as $review)
                    <div class="bg-white rounded-3xl p-7 flex flex-col justify-between space-y-6">
                        <div class="space-y-3">
                            <div class="flex items-center gap-1 text-[#F5B82E]">
                                ★★★★★
                            </div>
                            <p class="text-sm text-[#122018]/80 leading-relaxed">
                                &ldquo;{{ $review->body }}&rdquo;
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#EAE2D0]">
                            <span class="font-display font-bold text-sm text-[#0D3B2E] block">{{ $review->author_name }}</span>
                            <span class="text-xs text-[#122018]/50">{{ $review->author_role ?? 'Pengunjung Kafe' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. FINAL BOTTOM CTA: "COME IN. TAKE A BREATH" (TARAQ STYLE) -->
    <div class="px-3 sm:px-6 py-8">
        <section class="rounded-[2rem] sm:rounded-[2.5rem] bg-[#07241C] text-white py-20 px-6 sm:px-12 text-center relative overflow-hidden">
            <div class="max-w-2xl mx-auto space-y-4 relative z-10">
                <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">Reservasi Meja</span>
                <h2 class="font-display text-3xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none text-white">
                    Come In.<br/>
                    Take A Breath.
                </h2>
                <p class="text-sm sm:text-base text-[#9AB4A6] leading-relaxed pt-2">
                    Amankan meja belajarmu sekarang. Tanpa biaya muka, menu bisa dipesan saat tiba.
                </p>
                <div class="pt-6">
                    <a
                        href="/reservasi"
                        wire:navigate
                        class="px-8 py-4 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition shadow-lg inline-block"
                    >
                        Mulai Reservasi &rarr;
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.public>
