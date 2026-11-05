<x-layouts.public>
    <!-- 1. HERO CONTAINER (MELLOW & COFFEELO STYLE: ROUNDED FRAME WITH PROMINENT SPLASHING DRINK) -->
    <div class="px-3 sm:px-6 pt-2 pb-6">
        <section class="relative rounded-[2rem] sm:rounded-[2.5rem] lg:rounded-[3rem] overflow-hidden bg-[#07241C] text-white pt-24 sm:pt-28 pb-16 sm:pb-20 px-6 sm:px-10 lg:px-16">
            
            <!-- Soft Warm Radial Glow Behind Content -->
            <div class="absolute -top-40 -left-40 w-[30rem] h-[30rem] rounded-full bg-[#F5B82E]/15 blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/2 right-0 -translate-y-1/2 w-[32rem] h-[32rem] rounded-full bg-[#F5B82E]/10 blur-3xl pointer-events-none"></div>

            <div class="max-w-6xl mx-auto relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                    
                    <!-- Left Column: Big Bold Condensed Sans Typography (Zero Legs / Serifs) -->
                    <div class="lg:col-span-7 space-y-6 text-left">
                        
                        <!-- Top Location Tag (Mellow style) -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-xs font-bold tracking-widest uppercase text-[#F5B82E]">
                            <span class="w-2 h-2 rounded-full bg-[#F5B82E] animate-ping"></span>
                            <span class="text-white">Kampus UMRI Pekanbaru</span>
                        </div>

                        <!-- Massive Condensed Display Headline (Bebas Neue / Mellow / Taraq Style) -->
                        <h1 class="font-display text-5xl sm:text-7xl lg:text-8xl font-black uppercase tracking-tight text-white leading-[0.92]">
                            WHERE COFFEE<br/>
                            <span class="text-[#F5B82E]">MEETS CALM.</span>
                        </h1>

                        <!-- Punchy 2-Sentence Subtitle (Clean & Direct) -->
                        <p class="text-base sm:text-lg text-[#D1E0D7] font-medium leading-relaxed max-w-lg">
                            Ruang tenang ber-AC untuk fokus nugas di siang hari, dan courtyard terbuka hijau dengan aneka street food malam hari.
                        </p>

                        <!-- Action Button Pills -->
                        <div class="pt-2 flex flex-wrap items-center gap-4">
                            <a
                                href="/reservasi"
                                wire:navigate
                                class="px-8 py-4 rounded-full text-xs sm:text-sm font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 text-center shadow-none"
                            >
                                Reservasi Meja &rarr;
                            </a>
                            <a
                                href="/menu"
                                wire:navigate
                                class="px-7 py-4 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider bg-white/10 hover:bg-white/20 text-white backdrop-blur-md transition text-center"
                            >
                                Katalog Menu
                            </a>
                        </div>

                        <!-- Social Proof & Hours Sticker -->
                        <div class="pt-6 border-t border-white/10 flex items-center gap-6 text-xs text-white/80">
                            <div class="flex items-center gap-2">
                                <span class="font-display font-black text-2xl text-[#F5B82E]">4.9 ★</span>
                                <span class="text-[#9AB4A6] leading-tight block">Google Review<br/><span class="text-white/60">86 ulasan asli</span></span>
                            </div>
                            <span class="text-white/20">|</span>
                            <div>
                                <span class="font-display font-black text-2xl text-white">08.00 - 21.00</span>
                                <span class="text-[#9AB4A6] leading-tight block">Buka Setiap Hari<br/><span class="text-white/60">Kampus 2 UMRI</span></span>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Prominent Product Hero (High-Res Splashing Drink & Floating Beans) -->
                    <div class="lg:col-span-5 relative flex items-center justify-center">
                        
                        <!-- Floating Scalloped / Starburst Sticker (CoffeeLo & Brewhaus style) -->
                        <div class="absolute -top-4 -right-2 sm:top-2 sm:right-4 z-20 w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-[#F5B82E] text-[#07241C] flex flex-col items-center justify-center p-2 text-center rotate-12 shadow-none select-none">
                            <span class="font-display text-base sm:text-lg font-black uppercase leading-tight">FAVORIT #1</span>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-[#07241C]/80">Es Kopi Aren</span>
                        </div>

                        <!-- Floating Origin Pill Badge (CoffeeLo style) -->
                        <div class="absolute bottom-4 -left-2 sm:bottom-8 sm:left-0 z-20 bg-white/15 backdrop-blur-md rounded-2xl px-4 py-2.5 text-left border border-white/10 max-w-[180px]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#F5B82E] block">Single-Origin</span>
                            <span class="text-xs font-semibold text-white leading-tight block">Arabica Mandheling &amp; Gayo</span>
                        </div>

                        <!-- Hero Splashing Drink Cutout Image -->
                        <div class="relative w-full max-w-[380px] sm:max-w-[420px] mx-auto aspect-square flex items-center justify-center">
                            <img
                                src="{{ asset('/images/hero-iced-coffee-splash-cutout.png') }}"
                                alt="Signature Iced Coffee Splash UM.CO"
                                class="w-full h-full object-contain filter drop-shadow-2xl transition-transform duration-700 hover:scale-105"
                                loading="eager"
                            />
                        </div>

                    </div>

                </div>
            </div>
        </section>
    </div>

    <!-- 2. INFINITE RUNNING TICKER TAPE (MOCHANF STYLE) -->
    <div class="w-full bg-[#F5B82E] text-[#07241C] py-3 overflow-hidden select-none">
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
            <!-- Duplicate for seamless infinite loop -->
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

    <!-- 3. "LIFE BEGINS AFTER COFFEE" - TRIO SPECIALTY DRINKS & CATEGORY CLOUD (MOCHANF STYLE) -->
    <section class="py-16 sm:py-24 bg-[#FAF7EE] relative">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-10">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Pilihan Rasa UM.CO</span>
                <h2 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight text-[#0D3B2E] leading-none">
                    LIFE BEGINS <span class="bg-[#07241C] text-[#F5B82E] px-4 py-1 rounded-2xl inline-block mt-1 sm:mt-0">AFTER COFFEE</span>
                </h2>
                <p class="text-sm sm:text-base text-[#122018]/70 max-w-md mx-auto pt-1 font-medium">
                    Dari kopi susu gula aren yang creamy hingga manual brew single-origin segar setiap hari.
                </p>
            </div>

            <!-- Floating Coffee Bean Category Cloud (Mochanf style) -->
            <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 max-w-3xl mx-auto mb-12">
                <a href="/menu?kategori=kopi-susu" wire:navigate class="px-5 py-2.5 rounded-full bg-[#0D3B2E] text-white text-xs font-bold uppercase tracking-wider hover:bg-[#F5B82E] hover:text-[#07241C] transition">
                    Kopi Susu Aren
                </a>
                <a href="/menu?kategori=espresso" wire:navigate class="px-5 py-2.5 rounded-full bg-white text-[#0D3B2E] text-xs font-bold uppercase tracking-wider hover:bg-[#F5B82E] transition">
                    Espresso Based
                </a>
                <a href="/menu?kategori=manual-brew" wire:navigate class="px-5 py-2.5 rounded-full bg-white text-[#0D3B2E] text-xs font-bold uppercase tracking-wider hover:bg-[#F5B82E] transition">
                    Manual Brew V60
                </a>
                <a href="/menu?kategori=non-coffee" wire:navigate class="px-5 py-2.5 rounded-full bg-white text-[#0D3B2E] text-xs font-bold uppercase tracking-wider hover:bg-[#F5B82E] transition">
                    Matcha &amp; Cokelat
                </a>
                <a href="/menu?kategori=cold-brew" wire:navigate class="px-5 py-2.5 rounded-full bg-white text-[#0D3B2E] text-xs font-bold uppercase tracking-wider hover:bg-[#F5B82E] transition">
                    Cold Brew Signature
                </a>
                <a href="/menu?kategori=snack" wire:navigate class="px-5 py-2.5 rounded-full bg-white text-[#0D3B2E] text-xs font-bold uppercase tracking-wider hover:bg-[#F5B82E] transition">
                    Pastry &amp; Toast
                </a>
                <a href="/about#street-food" wire:navigate class="px-5 py-2.5 rounded-full bg-[#F5B82E] text-[#07241C] text-xs font-black uppercase tracking-wider hover:bg-[#D99B16] transition">
                    Street Food Malam
                </a>
            </div>

            <!-- Commercial Drink Trio Photographic Showcase Banner -->
            <div class="relative rounded-3xl sm:rounded-[2.5rem] overflow-hidden bg-[#07241C] text-white">
                <div class="relative aspect-[16/9] sm:aspect-[21/9] w-full">
                    <img
                        src="{{ asset('/images/drinks-trio-banner.jpg') }}"
                        alt="UM.CO Specialty Iced Drinks"
                        class="w-full h-full object-cover object-center"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-[#07241C] via-transparent to-black/20"></div>

                    <!-- Overlay Bottom Card -->
                    <div class="absolute bottom-4 left-4 right-4 sm:bottom-8 sm:left-8 sm:right-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-xs font-bold uppercase tracking-widest text-[#F5B82E]">Racikan Barista Kampus</span>
                            <h3 class="font-display text-2xl sm:text-4xl font-black uppercase tracking-tight text-white leading-none">
                                Iced Caramel • Matcha Cold Foam • Dark Mocha
                            </h3>
                        </div>
                        <a
                            href="/menu"
                            wire:navigate
                            class="px-6 py-3 rounded-full bg-[#F5B82E] text-[#07241C] font-black text-xs uppercase tracking-wider hover:bg-[#D99B16] transition flex-shrink-0 self-start sm:self-auto text-center"
                        >
                            Pesan Sekarang &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 4. "GOOD COFFEE, MADE SIMPLY" - PHOTOGRAPHIC MENU CARDS (TARAQ & BREWHAUS STYLE) -->
    <section class="py-16 sm:py-24 bg-[#FAF7EE] border-t border-[#EAE2D0]/60">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Favorit Pengunjung</span>
                    <h2 class="font-display text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight text-[#0D3B2E] mt-1 leading-none">
                        GOOD COFFEE, MADE SIMPLY.
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

            <!-- Big Photographic Product Cards Grid (Zero Text Clutter) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($favoriteMenuItems->take(4) as $item)
                    <div class="bg-white rounded-3xl p-4 flex flex-col justify-between hover:bg-[#F4EEDF]/50 transition group">
                        
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

                        <!-- Card Info: Clean, Minimalist -->
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

    <!-- 5. "MEET IN COMFORT" - ATMOSPHERE SHOWCASE (TARAQ STYLE) -->
    <div class="px-3 sm:px-6 py-6">
        <section class="relative min-h-[50vh] sm:min-h-[60vh] rounded-[2rem] sm:rounded-[2.5rem] overflow-hidden bg-[#0D3B2E] text-white flex items-end">
            <!-- Full Photographic Background Layer -->
            <div class="absolute inset-0 z-0">
                <img
                    src="{{ asset('/images/hero-photo.jpg') }}"
                    alt="Suasana UMCO House Kampus UMRI"
                    class="w-full h-full object-cover object-center opacity-45 scale-105"
                    loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-[#07241C] via-[#07241C]/60 to-transparent"></div>
            </div>

            <div class="max-w-6xl mx-auto px-6 sm:px-10 lg:px-12 py-14 sm:py-20 relative z-10 w-full flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div class="max-w-xl space-y-3">
                    <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">Arsitektur Gedung H</span>
                    <h2 class="font-display text-4xl sm:text-6xl font-black uppercase tracking-tight leading-none text-white">
                        MEET IN COMFORT.
                    </h2>
                    <p class="text-sm sm:text-base text-[#D1E0D7] leading-relaxed">
                        Wing Barat ber-AC untuk laptopan tanpa bising, dan Wing Timur outdoor dengan aneka jajanan street food sore hingga malam.
                    </p>
                </div>

                <a
                    href="/about"
                    wire:navigate
                    class="px-8 py-4 rounded-full text-xs font-black uppercase tracking-wider bg-white text-[#07241C] hover:bg-[#F5B82E] transition flex-shrink-0 text-center"
                >
                    Lihat Denah &amp; Lokasi &rarr;
                </a>
            </div>
        </section>
    </div>

    <!-- 6. REAL REVIEWS: "WHY PEOPLE COME BACK" (TARAQ STYLE) -->
    <section class="py-16 sm:py-24 bg-[#F4EEDF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Ulasan Mahasiswa &amp; Dosen</span>
                <h2 class="font-display text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight text-[#0D3B2E] mt-1 leading-none">
                    WHY PEOPLE COME BACK.
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

    <!-- 7. FINAL BOTTOM CTA: "COME IN. TAKE A BREATH" (TARAQ STYLE) -->
    <div class="px-3 sm:px-6 py-6 pb-12">
        <section class="rounded-[2rem] sm:rounded-[2.5rem] bg-[#07241C] text-white py-20 px-6 sm:px-12 text-center relative overflow-hidden">
            <div class="max-w-2xl mx-auto space-y-4 relative z-10">
                <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">Reservasi Meja</span>
                <h2 class="font-display text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight leading-none text-white">
                    COME IN.<br/>
                    TAKE A BREATH.
                </h2>
                <p class="text-sm sm:text-base text-[#D1E0D7] leading-relaxed pt-2">
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
