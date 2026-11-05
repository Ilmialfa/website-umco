<x-layouts.public>
    <!-- 1. HERO SECTION: PHOTOGRAPHIC & EMERALD WITH SEAMLESS BLEND -->
    <section class="relative min-h-[92vh] flex items-center pt-28 pb-20 lg:pt-36 lg:pb-28 bg-[#0D3B2E] text-white overflow-hidden">
        <!-- Ambient Warm Glow in Background -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#F5B82E] blur-3xl"></div>
            <div class="absolute bottom-10 -right-20 w-96 h-96 rounded-full bg-[#145341] blur-3xl"></div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                <!-- Left Hero Copy -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Rating Badge (Mochanf / Mellow style with Yellow Accent) -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/10 text-xs font-semibold backdrop-blur-sm">
                        <span class="inline-flex items-center gap-1 text-[#F5B82E]">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-bold text-white">4.9 / 5.0</span>
                        </span>
                        <span class="text-white/40">•</span>
                        <span class="text-[#9AB4A6]">Pilihan Kopi Civitas UMRI</span>
                    </div>

                    <!-- Big Minimalist Punchy Headline -->
                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.08] text-white">
                        Kopi Enak,<br/>
                        Suasana Tenang,<br/>
                        <span class="text-[#F5B82E]">Tepat di Kampus.</span>
                    </h1>

                    <!-- Clean Human Copywriting -->
                    <p class="text-base sm:text-lg text-[#9AB4A6] max-w-xl leading-relaxed">
                        Rumah singgah mahasiswa dan dosen di Kampus 2 UMRI Pekanbaru. Ruang ber-AC yang sejuk untuk laptopan, dan courtyard terbuka hijau dengan aneka street food malam hari.
                    </p>

                    <!-- CTAs with vibrant yellow button -->
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a
                            href="/reservasi"
                            wire:navigate
                            class="px-7 py-3.5 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 text-center"
                        >
                            Reservasi Meja &rarr;
                        </a>
                        <a
                            href="/menu"
                            wire:navigate
                            class="px-7 py-3.5 rounded-full text-xs sm:text-sm font-bold tracking-wide text-white hover:bg-white/10 transition text-center"
                        >
                            Lihat Menu
                        </a>
                    </div>

                    <!-- Mini Key Features -->
                    <div class="pt-6 grid grid-cols-3 gap-4 border-t border-white/10 text-xs text-[#9AB4A6]">
                        <div>
                            <span class="font-bold text-white block text-sm">Indoor AC</span>
                            Colokan di setiap meja
                        </div>
                        <div>
                            <span class="font-bold text-white block text-sm">100 Mbps</span>
                            Wi-Fi kampus kencang
                        </div>
                        <div>
                            <span class="font-bold text-white block text-sm">Street Food</span>
                            Buka sore s/d 21.00 WIB
                        </div>
                    </div>
                </div>

                <!-- Right Hero Image: Real High-Res Commercial Photograph -->
                <div class="lg:col-span-5 relative">
                    <div class="relative w-full aspect-[4/3] lg:aspect-[5/4] rounded-3xl overflow-hidden group">
                        <img
                            src="{{ asset('/images/hero-photo.jpg') }}"
                            alt="Suasana UM.CO House Kampus UMRI"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-out"
                        />
                        <!-- Subtle gradient vignette -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#07241C]/60 via-transparent to-transparent"></div>

                        <!-- Floating Badge on Photo -->
                        <div class="absolute bottom-4 left-4 right-4 p-4 rounded-2xl bg-[#07241C]/80 backdrop-blur-md text-white flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-[#F5B82E] font-bold block">Signature Blend</span>
                                <span class="font-serif text-sm font-bold">Kopi Susu Gula Aren UM.CO</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-[#F5B82E]">Rp 18.000</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ORGANIC WAVE SECTION DIVIDER (Transition from Emerald to Warm Cream) -->
        <div class="absolute bottom-0 left-0 right-0 leading-none overflow-hidden select-none pointer-events-none">
            <svg class="relative block w-full h-12 sm:h-16 lg:h-20 text-[#FAF7EE]" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0 C150,90 350,-40 500,40 C650,120 900,20 1200,60 L1200,120 L0,120 Z" fill="currentColor"></path>
            </svg>
        </div>
    </section>

    <!-- 2. ANNOUNCEMENTS / BANNERS (IF ACTIVE) -->
    @if ($banners->isNotEmpty())
        <section class="py-6 bg-[#FAF7EE]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                @foreach ($banners as $banner)
                    <div class="rounded-3xl p-6 sm:p-8 bg-[#F4EEDF] flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-[#0D3B2E] text-white">
                                Informasi Terbaru
                            </span>
                            <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#0D3B2E]">{{ $banner->title }}</h3>
                            <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed">{{ $banner->description }}</p>
                        </div>
                        @if ($banner->cta_url && $banner->cta_label)
                            <a
                                href="{{ $banner->cta_url }}"
                                class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#0D3B2E] text-white hover:bg-[#07241C] transition flex-shrink-0"
                            >
                                {{ $banner->cta_label }} &rarr;
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- 3. MENU HIGHLIGHTS: ASYMMETRIC BEST SELLERS (CLEAN ZERO-SHADOW CARDS) -->
    <section class="py-16 lg:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Menu Favorit Kampus</span>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-black text-[#0D3B2E] mt-1">
                        Paling Sering Dipesan
                    </h2>
                </div>
                <a
                    href="/menu"
                    wire:navigate
                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#0D3B2E] hover:text-[#D99B16] transition"
                >
                    <span>Lihat Semua Menu</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Best Sellers Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($favoriteMenuItems as $item)
                    <div class="bg-white rounded-3xl p-5 flex flex-col justify-between hover:bg-[#F4EEDF]/50 transition group">
                        <div>
                            <div class="aspect-square rounded-2xl overflow-hidden bg-[#FAF7EE] mb-4 relative">
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    loading="lazy"
                                />
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C]">
                                    Favorit
                                </span>
                            </div>

                            <span class="text-[11px] font-semibold uppercase tracking-wider text-[#145341]">
                                {{ $item->category?->name ?? 'Minuman' }}
                            </span>
                            <h3 class="font-serif text-xl font-bold text-[#0D3B2E] mt-0.5">{{ $item->name }}</h3>
                            <p class="text-xs text-[#122018]/70 mt-1 line-clamp-2 leading-relaxed">
                                {{ $item->description }}
                            </p>
                        </div>

                        <div class="pt-4 mt-4 border-t border-[#EAE2D0] flex items-center justify-between">
                            <span class="font-serif font-black text-lg text-[#0D3B2E]">
                                {{ $item->formatted_price }}
                            </span>
                            <a
                                href="/reservasi?menu={{ $item->slug }}"
                                wire:navigate
                                class="inline-flex items-center gap-1 px-4 py-1.5 rounded-full text-xs font-bold bg-[#0D3B2E] text-white hover:bg-[#07241C] transition"
                            >
                                Pesan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 4. DENAH ARSITEKTUR HURUF H & SUASANA (INTERACTIVE MOOD SWITCHER) -->
    <section class="py-16 lg:py-24 bg-[#F4EEDF] relative" x-data="{ activeZone: 'west' }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Arsitektur Gedung H</span>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-black text-[#0D3B2E] mt-1">
                    Satu Tempat, Tiga Zona Suasana
                </h2>
                <p class="text-sm text-[#122018]/70 mt-3 leading-relaxed">
                    Dirancang dengan dua sayap dan taman terbuka di tengah, menyesuaikan kebutuhanmu mulai dari fokus laptopan hingga nongkrong ramai.
                </p>
            </div>

            <!-- Zone Pills Switcher -->
            <div class="flex items-center justify-center gap-2 mb-10 flex-wrap">
                <button
                    type="button"
                    @click="activeZone = 'west'"
                    class="px-5 py-2.5 rounded-full text-xs font-bold tracking-wide transition cursor-pointer"
                    :class="activeZone === 'west' ? 'bg-[#0D3B2E] text-white' : 'bg-white text-[#0D3B2E] hover:bg-white/80'"
                >
                    Wing Barat (Indoor AC)
                </button>
                <button
                    type="button"
                    @click="activeZone = 'center'"
                    class="px-5 py-2.5 rounded-full text-xs font-bold tracking-wide transition cursor-pointer"
                    :class="activeZone === 'center' ? 'bg-[#0D3B2E] text-white' : 'bg-white text-[#0D3B2E] hover:bg-white/80'"
                >
                    Courtyard (Taman Tengah)
                </button>
                <button
                    type="button"
                    @click="activeZone = 'east'"
                    class="px-5 py-2.5 rounded-full text-xs font-bold tracking-wide transition cursor-pointer"
                    :class="activeZone === 'east' ? 'bg-[#0D3B2E] text-white' : 'bg-white text-[#0D3B2E] hover:bg-white/80'"
                >
                    Wing Timur (Street Food Outdoor)
                </button>
            </div>

            <!-- Zone Content Cards (Zero-Border, Zero-Shadow) -->
            <div class="bg-white rounded-3xl p-8 sm:p-12">
                <!-- Wing Barat -->
                <div x-show="activeZone === 'west'" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#145341]">Zona Fokus &amp; Belajar</span>
                        <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#0D3B2E]">Wing Barat (Indoor AC)</h3>
                        <p class="text-sm text-[#122018]/70 leading-relaxed">
                            Dilengkapi pendingin ruangan stabil 22°C, colokan listrik di hampir setiap meja, dan Slow Bar specialty coffee. Pilihan utama untuk mengerjakan skripsi, tugas kuliah, atau meeting kelompok kecil tanpa kebisingan jalan.
                        </p>
                        <div class="pt-2 flex items-center gap-6 text-xs text-[#0D3B2E] font-medium">
                            <span>✔ 36 Kursi Ergonomis</span>
                            <span>✔ Wi-Fi 100 Mbps</span>
                            <span>✔ Area Bebas Asap Rokok</span>
                        </div>
                    </div>
                    <div class="rounded-2xl overflow-hidden aspect-[4/3] bg-[#FAF7EE]">
                        <img src="{{ asset('/images/hero-photo.jpg') }}" alt="Indoor AC UMCO" class="w-full h-full object-cover" loading="lazy" />
                    </div>
                </div>

                <!-- Courtyard -->
                <div x-show="activeZone === 'center'" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center" style="display: none;">
                    <div class="space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#145341]">Taman Hijau &amp; Udara Segar</span>
                        <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#0D3B2E]">Courtyard Pusat</h3>
                        <p class="text-sm text-[#122018]/70 leading-relaxed">
                            Area terbuka hijau di tengah gedung dengan pepohonan rindang dan pencahayaan hangat sore hari. Tempat ideal untuk menghela napas, mengobrol santai bersama sahabat, atau menikmati angin sore kampus.
                        </p>
                        <div class="pt-2 flex items-center gap-6 text-xs text-[#0D3B2E] font-medium">
                            <span>✔ Meja Payung Peneduh</span>
                            <span>✔ Sirkulasi Udara Alami</span>
                            <span>✔ Dekat Akses Musholla</span>
                        </div>
                    </div>
                    <div class="rounded-2xl overflow-hidden aspect-[4/3] bg-[#FAF7EE]">
                        <img src="{{ asset('/images/hero-photo.jpg') }}" alt="Courtyard UMCO" class="w-full h-full object-cover" loading="lazy" />
                    </div>
                </div>

                <!-- Wing Timur -->
                <div x-show="activeZone === 'east'" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center" style="display: none;">
                    <div class="space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#145341]">Kuliner Malam Hari</span>
                        <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#0D3B2E]">Street Food Corner</h3>
                        <p class="text-sm text-[#122018]/70 leading-relaxed">
                            Area semi-outdoor yang hidup dari sore hingga malam hari. Menyajikan aneka jajanan street food UMKM lokal: tahu walik renyah, donat artisan, sate taichan, hingga mocktail segar ramah kantong mahasiswa.
                        </p>
                        <div class="pt-2 flex items-center gap-6 text-xs text-[#0D3B2E] font-medium">
                            <span>✔ 40+ Tempat Duduk</span>
                            <span>✔ Buka 15.00 – 21.00 WIB</span>
                            <span>✔ Smoking Allowed</span>
                        </div>
                    </div>
                    <div class="rounded-2xl overflow-hidden aspect-[4/3] bg-[#FAF7EE]">
                        <img src="{{ asset('/images/hero-photo.jpg') }}" alt="Street Food UMCO" class="w-full h-full object-cover" loading="lazy" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. REAL REVIEWS: 4.9 GOOGLE REVIEW (TRANSPARENT & CLEAN) -->
    <section class="py-16 lg:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Ulasan Pengunjung</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-black text-[#0D3B2E] mt-1">
                    Cerita Mereka yang Datang
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($testimonials as $review)
                    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between">
                        <div>
                            <!-- 5 Stars in Yellow -->
                            <div class="flex items-center gap-1 text-[#F5B82E] mb-3">
                                @for ($i = 0; $i < $review->rating; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-xs text-[#122018]/80 leading-relaxed italic">
                                &ldquo;{{ $review->body }}&rdquo;
                            </p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-[#EAE2D0]">
                            <span class="font-bold text-xs text-[#0D3B2E] block">{{ $review->author_name }}</span>
                            <span class="text-[10px] text-[#122018]/50">{{ $review->author_role ?? 'Pengunjung Kafe' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. FINAL CALL TO ACTION: RESERVASI MEJA DENGAN ORGANIC CURVE -->
    <section class="py-20 bg-[#0D3B2E] text-white relative overflow-hidden text-center">
        <!-- Ambient Warm Glow -->
        <div class="absolute inset-0 pointer-events-none opacity-15">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full bg-[#F5B82E] blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-6">
            <span class="text-xs font-bold uppercase tracking-widest text-[#F5B82E]">Kunjungan Tanpa Hambatan</span>
            <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                Mau Nugas atau Nongkrong Hari Ini?<br/>
                Amankan Mejamu Sekarang.
            </h2>
            <p class="text-sm sm:text-base text-[#9AB4A6] max-w-xl mx-auto leading-relaxed">
                Reservasi meja gratis tanpa DP, atau pilih sambil pesan menu agar langsung tersaji hangat saat kamu tiba.
            </p>
            <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                <a
                    href="/reservasi"
                    wire:navigate
                    class="px-8 py-4 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 shadow-md"
                >
                    Mulai Reservasi &rarr;
                </a>
                <a
                    href="/about"
                    wire:navigate
                    class="px-8 py-4 rounded-full text-xs sm:text-sm font-bold tracking-wide text-white/90 hover:text-white hover:bg-white/10 transition"
                >
                    Lihat Denah Gedung &amp; Lokasi
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>
