<x-layouts.public>
    <!-- 1. HERO SECTION (TARAQ & MELLOW STYLE: FULL-BLEED BACKGROUND PHOTOGRAPH WITH TEXT ON TOP) -->
    <div class="px-3 sm:px-6 pt-2 pb-6">
        <section class="relative min-h-[88vh] sm:min-h-[92vh] rounded-[2rem] sm:rounded-[3rem] overflow-hidden bg-[#07241C] text-white flex items-center">
            
            <!-- Full Photographic Layer (Real Commercial Photo: Student with Iced Latte on Sunlit Patio) -->
            <div class="absolute inset-0 z-0">
                <img
                    src="{{ asset('/images/hero-banner-full.jpg') }}"
                    alt="UM.CO House Campus Coffee Patio"
                    class="w-full h-full object-cover object-center scale-100"
                    loading="eager"
                />
                <!-- Subtle Gradient Overlay: Dark on the Left for Ultra-Crisp Typography, Transparent on the Right to Showcase the Photo -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#07241C]/90 via-[#07241C]/65 to-transparent sm:w-3/4"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#07241C]/80 via-transparent to-[#07241C]/40"></div>
            </div>

            <!-- Hero Content Floating Right on Top of the Photograph -->
            <div class="max-w-6xl mx-auto px-6 sm:px-12 lg:px-16 py-28 sm:py-36 relative z-10 w-full">
                <div class="max-w-2xl space-y-6">

                    <!-- Location Pill Tag -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-black/40 backdrop-blur-md text-xs font-bold uppercase tracking-widest text-[#F5B82E] border border-white/10">
                        <span class="w-2 h-2 rounded-full bg-[#F5B82E] animate-ping"></span>
                        <span class="text-white">Kampus UMRI Pekanbaru</span>
                    </div>

                    <!-- Giant Bold Sans-Serif Display Headline (Outfit - Geometric, Spacious, Zero Berdempetan) -->
                    <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-extrabold uppercase tracking-wide text-white leading-[1.08] drop-shadow-md">
                        A Better Place<br/>
                        <span class="text-[#F5B82E]">For Coffee.</span>
                    </h1>

                    <!-- Clean 1-2 Sentence Punchy Subtitle -->
                    <p class="text-base sm:text-lg text-white/90 font-medium leading-relaxed max-w-lg drop-shadow">
                        Ruang tenang ber-AC untuk fokus nugas di siang hari, dan courtyard terbuka hijau dengan aneka street food malam hari.
                    </p>

                    <!-- Big Pill Action Buttons -->
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a
                            href="/reservasi"
                            wire:navigate
                            class="px-8 py-4 rounded-full text-xs sm:text-sm font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 text-center shadow-lg"
                        >
                            Reservasi Meja &rarr;
                        </a>
                        <a
                            href="/menu"
                            wire:navigate
                            class="px-7 py-4 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider bg-white/20 hover:bg-white/30 text-white backdrop-blur-md transition text-center border border-white/20"
                        >
                            Katalog Menu
                        </a>
                    </div>

                    <!-- Social Proof & Operating Hours -->
                    <div class="pt-6 border-t border-white/20 flex items-center gap-6 text-xs text-white/90">
                        <div class="flex items-center gap-2">
                            <span class="font-display font-black text-2xl text-[#F5B82E]">4.9 ★</span>
                            <span class="leading-tight block">Google Review<br/><span class="text-white/60">86 ulasan asli</span></span>
                        </div>
                        <span class="text-white/30">|</span>
                        <div>
                            <span class="font-display font-black text-2xl text-white">08.00 - 21.00</span>
                            <span class="leading-tight block">Buka Setiap Hari<br/><span class="text-white/60">Kampus 2 UMRI</span></span>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <!-- 2. INFINITE RUNNING TICKER RIBBON (TARAQ & MOCHANF STYLE) -->
    <div class="w-full bg-[#F5B82E] text-[#07241C] py-3.5 overflow-hidden select-none">
        <div class="animate-marquee flex items-center gap-8 whitespace-nowrap font-display text-base sm:text-xl font-black uppercase tracking-widest">
            <span>★ UM.CO HOUSE</span>
            <span>★ FRESH BREW COFFEE</span>
            <span>★ KAMPUS UMRI PEKANBARU</span>
            <span>★ ESPRESSO BAR</span>
            <span>★ COLD BREW BOTOLAN</span>
            <span>★ RUANG NUGAS AC INDOOR</span>
            <span>★ COURTYARD STREET FOOD</span>
            <span>★ BUKA 08.00 - 21.00 WIB</span>
            <span>★ 100% ARABICA GRADE 1</span>
            <!-- Duplicate for seamless loop -->
            <span>★ UM.CO HOUSE</span>
            <span>★ FRESH BREW COFFEE</span>
            <span>★ KAMPUS UMRI PEKANBARU</span>
            <span>★ ESPRESSO BAR</span>
            <span>★ COLD BREW BOTOLAN</span>
            <span>★ RUANG NUGAS AC INDOOR</span>
            <span>★ COURTYARD STREET FOOD</span>
            <span>★ BUKA 08.00 - 21.00 WIB</span>
            <span>★ 100% ARABICA GRADE 1</span>
        </div>
    </div>

    <!-- 3. "EVERYTHING YOU NEED FOR A GOOD PAUSE" - EDITORIAL MASONRY PHOTO COLLAGE (TARAQ SECTION 2) -->
    <section class="py-16 sm:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Suasana &amp; Keseharian</span>
                <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-[#0D3B2E] leading-tight">
                    Everything You Need<br/>
                    For A Good Pause.
                </h2>
                <p class="text-sm sm:text-base text-[#122018]/70 max-w-md mx-auto pt-1 font-medium">
                    Ruang singgah santai untuk rehat sejenak di antara padatnya jadwal kuliah.
                </p>
            </div>

            <!-- Asymmetrical Photographic Grid (Taraq Style) -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
                
                <!-- Card 1: Large Vertical Splashing Iced Coffee (Col 5) -->
                <div class="md:col-span-5 relative rounded-3xl overflow-hidden bg-[#FAF7EE] group min-h-[380px] md:min-h-[460px] flex items-center justify-center p-6">
                    <img
                        src="{{ asset('/images/hero-iced-coffee-splash.jpg') }}"
                        alt="Signature Iced Coffee Splash"
                        class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition duration-700"
                        loading="lazy"
                    />
                    <!-- Floating Badge -->
                    <div class="absolute bottom-6 left-6 right-6 p-4 rounded-2xl bg-[#07241C]/80 backdrop-blur-md text-white flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#F5B82E] block">Signature Drink</span>
                            <span class="font-display text-lg font-bold">Es Kopi Susu Aren</span>
                        </div>
                        <span class="font-mono text-sm font-bold text-[#F5B82E]">Rp 22.000</span>
                    </div>
                </div>

                <!-- Card 2 & 3: Stacked Landscape & Detail Photos (Col 7) -->
                <div class="md:col-span-7 flex flex-col gap-5">
                    
                    <!-- Top Wide Photo: Specialty Drinks Banner -->
                    <div class="relative rounded-3xl overflow-hidden aspect-[16/9] bg-[#07241C] group">
                        <img
                            src="{{ asset('/images/drinks-trio-banner.jpg') }}"
                            alt="UM.CO Specialty Iced Drinks"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-700"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between text-white">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#F5B82E]">Pilihan Barista</span>
                                <h3 class="font-display text-xl sm:text-2xl font-black uppercase">Caramel, Matcha &amp; Mocha</h3>
                            </div>
                            <a href="/menu" wire:navigate class="px-4 py-2 rounded-full bg-[#F5B82E] text-[#07241C] text-xs font-bold uppercase tracking-wider hover:bg-[#D99B16] transition">
                                Menu &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Bottom 2 Columns: Cafe Atmosphere & Clean Detail -->
                    <div class="grid grid-cols-2 gap-5 flex-grow">
                        <!-- Left Box: Campus Cafe Atmosphere -->
                        <div class="relative rounded-3xl overflow-hidden aspect-square sm:aspect-auto group">
                            <img
                                src="{{ asset('/images/hero-photo.jpg') }}"
                                alt="Suasana Cafe UMCO"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-700"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <span class="absolute bottom-3 left-3 text-xs font-bold text-white uppercase tracking-wider">
                                Indoor AC Area
                            </span>
                        </div>

                        <!-- Right Box: Quality Commitment Pill -->
                        <div class="rounded-3xl bg-[#F4EEDF] p-6 flex flex-col justify-between">
                            <span class="w-10 h-10 rounded-full bg-[#0D3B2E] text-[#F5B82E] flex items-center justify-center font-display text-lg font-bold">
                                ☕
                            </span>
                            <div class="space-y-1">
                                <h4 class="font-display text-xl font-black uppercase text-[#0D3B2E]">Fresh Every Day</h4>
                                <p class="text-xs text-[#122018]/70 leading-relaxed">
                                    Biji kopi disangrai mingguan dan dikalibrasi barista setiap pagi.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- 4. "GOOD COFFEE, MADE SIMPLY" - VERTICAL PHOTOGRAPHIC PRODUCT ROW (TARAQ SECTION 3) -->
    <section class="py-16 sm:py-24 bg-[#FAF7EE] border-t border-[#EAE2D0]/60">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Favorit Kampus</span>
                    <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-[#0D3B2E] mt-1 leading-tight">
                        Good Coffee, Made Simply.
                    </h2>
                </div>
                <a
                    href="/menu"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-[#0D3B2E] hover:text-[#D99B16] transition"
                >
                    <span>Buka Katalog Lengkap</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Big Photographic Product Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($favoriteMenuItems->take(4) as $item)
                    <div class="bg-white rounded-3xl p-4 flex flex-col justify-between hover:bg-[#F4EEDF]/40 transition group">
                        
                        <!-- Dominant Image Frame -->
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

                        <!-- Card Info -->
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#145341]">
                                {{ $item->category?->name ?? 'Minuman' }}
                            </span>
                            <h3 class="font-display text-xl font-bold text-[#0D3B2E] truncate">
                                {{ $item->name }}
                            </h3>
                        </div>

                        <!-- Price & Order Action -->
                        <div class="pt-4 mt-3 border-t border-[#EAE2D0] flex items-center justify-between">
                            <span class="font-display font-black text-lg text-[#0D3B2E]">
                                {{ $item->formatted_price }}
                            </span>
                            <a
                                href="/reservasi?menu={{ $item->slug }}"
                                wire:navigate
                                class="w-9 h-9 rounded-full bg-[#0D3B2E] text-white hover:bg-[#F5B82E] hover:text-[#07241C] transition flex items-center justify-center font-black text-sm"
                                title="Pesan dan Amankan Meja"
                            >
                                +
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- 5. "MEET IN COMFORT" - FULL-BLEED BISTRO PATIO BANNER (TARAQ SECTION 4) -->
    <div class="px-3 sm:px-6 py-6">
        <section class="relative min-h-[55vh] sm:min-h-[65vh] rounded-[2rem] sm:rounded-[3rem] overflow-hidden bg-[#0D3B2E] text-white flex items-end">
            <!-- Full Photographic Layer: Bistro Table with Croissants & Iced Drinks in Dappled Sunlight -->
            <div class="absolute inset-0 z-0">
                <img
                    src="{{ asset('/images/bistro-patio-table.jpg') }}"
                    alt="Outdoor Bistro Patio UM.CO House"
                    class="w-full h-full object-cover object-center scale-100"
                    loading="lazy"
                />
                <!-- Warm Gradient Overlay for Readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#07241C]/90 via-[#07241C]/40 to-transparent"></div>
            </div>

            <!-- Content Over the Photo -->
            <div class="max-w-6xl mx-auto px-6 sm:px-12 lg:px-16 py-14 sm:py-20 relative z-10 w-full flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div class="max-w-xl space-y-3">
                    <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">Arsitektur Gedung H</span>
                    <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide leading-tight text-white drop-shadow">
                        Meet In Comfort.
                    </h2>
                    <p class="text-sm sm:text-base text-white/90 leading-relaxed drop-shadow font-medium">
                        Wing Barat ber-AC untuk nugas tanpa bising, dan Wing Timur outdoor dengan aneka jajanan street food sore hingga malam.
                    </p>
                </div>

                <a
                    href="/about"
                    wire:navigate
                    class="px-8 py-4 rounded-full text-xs font-black uppercase tracking-wider bg-white text-[#07241C] hover:bg-[#F5B82E] transition flex-shrink-0 text-center shadow-lg"
                >
                    Lihat Denah &amp; Lokasi &rarr;
                </a>
            </div>
        </section>
    </div>

    <!-- 6. "FROM BEAN TO CUP" - VISUAL EDITORIAL TIMELINE (TARAQ SECTION 6) -->
    <section class="py-16 sm:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-16 space-y-2">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Standar Kualitas</span>
                <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-[#0D3B2E]">
                    From Bean To Cup.
                </h2>
                <p class="text-xs sm:text-sm text-[#122018]/60 font-medium">Tiga komitmen di balik setiap tegukan kopi yang kami sajikan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Step 1 -->
                <div class="bg-white rounded-3xl p-8 space-y-4">
                    <span class="font-display text-5xl font-black text-[#145341]/20 block">01</span>
                    <h3 class="font-display text-2xl font-black text-[#0D3B2E] uppercase">Biji Pilihan Sumatera</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed font-medium">
                        Kurasi biji Arabica Mandheling dan Gayo grade 1 dengan profil rasa cokelat, rempah, dan aroma karamel yang tebal.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-3xl p-8 space-y-4">
                    <span class="font-display text-5xl font-black text-[#145341]/20 block">02</span>
                    <h3 class="font-display text-2xl font-black text-[#0D3B2E] uppercase">Kalibrasi Presisi Harian</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed font-medium">
                        Setiap pagi pukul 07.30 WIB, barista menguji rasio ekstraksi dan suhu air agar rasa cangkir pertamamu konsisten hingga malam hari.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-3xl p-8 space-y-4">
                    <span class="font-display text-5xl font-black text-[#145341]/20 block">03</span>
                    <h3 class="font-display text-2xl font-black text-[#0D3B2E] uppercase">Sajian Hangat Kampus</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed font-medium">
                        Dihidangkan segar dalam gelas kaca untuk dine-in santai, atau take-away cepat menuju ruang kuliah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. REAL REVIEWS: "WHY PEOPLE COME BACK" (TARAQ SECTION 8) -->
    <section class="py-16 sm:py-24 bg-[#F4EEDF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Ulasan Mahasiswa &amp; Dosen</span>
                <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-[#0D3B2E] mt-1 leading-tight">
                    Why People Come Back.
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($testimonials->take(3) as $review)
                    <div class="bg-white rounded-3xl p-7 flex flex-col justify-between space-y-6">
                        <div class="space-y-3">
                            <div class="flex items-center gap-1 text-[#F5B82E] text-base">
                                ★★★★★
                            </div>
                            <p class="text-sm text-[#122018]/85 leading-relaxed font-medium">
                                &ldquo;{{ $review->body }}&rdquo;
                            </p>
                        </div>
                        <div class="pt-4 border-t border-[#EAE2D0]">
                            <span class="font-display font-bold text-base text-[#0D3B2E] block">{{ $review->author_name }}</span>
                            <span class="text-xs text-[#122018]/50">{{ $review->author_role ?? 'Pengunjung Kafe' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 8. FINAL BOTTOM CTA: "COME IN. TAKE A BREATH" (TARAQ SECTION 9) -->
    <div class="px-3 sm:px-6 py-6 pb-12">
        <section class="rounded-[2rem] sm:rounded-[3rem] bg-[#07241C] text-white py-20 px-6 sm:px-12 text-center relative overflow-hidden">
            <div class="max-w-2xl mx-auto space-y-4 relative z-10">
                <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">Reservasi Meja</span>
                <h2 class="font-display text-3xl sm:text-5xl lg:text-6xl font-extrabold uppercase tracking-wide leading-tight text-white">
                    Come In.<br/>
                    Take A Breath.
                </h2>
                <p class="text-sm sm:text-base text-[#D1E0D7] leading-relaxed pt-2 font-medium">
                    Amankan meja belajarmu sekarang. Bebas pilih meja saja tanpa DP, atau pesan menu favorit sekaligus.
                </p>
                <div class="pt-6">
                    <a
                        href="/reservasi"
                        wire:navigate
                        class="px-9 py-4 rounded-full text-xs sm:text-sm font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition shadow-none inline-block active:scale-95"
                    >
                        Mulai Reservasi &rarr;
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.public>
