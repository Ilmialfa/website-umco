<x-layouts.public>
    <!-- 1. HERO SECTION: CINEMATIC AMBIENT VIDEO HERO (TARAQ & MELLOW ART DIRECTION) -->
    <div class="px-3 sm:px-6 pt-2 pb-6">
        <section
            x-data="{
                videoPlaying: true,
                toggleVideo() {
                    const v = this.$refs.heroVideo;
                    if (v.paused) {
                        v.play();
                        this.videoPlaying = true;
                    } else {
                        v.pause();
                        this.videoPlaying = false;
                    }
                }
            }"
            class="relative min-h-[90vh] sm:min-h-[94vh] rounded-[2.5rem] sm:rounded-[3rem] overflow-hidden bg-[#07241C] text-white flex items-center shadow-2xl"
        >
            
            <!-- Background Video Layer (Cinematic Coffee Extraction Loop) -->
            <video
                x-ref="heroVideo"
                autoplay
                loop
                muted
                playsinline
                poster="{{ asset('/images/hero-banner-full.jpg') }}"
                class="absolute inset-0 w-full h-full object-cover object-center opacity-65 scale-100 transition-opacity duration-1000"
            >
                <source src="{{ asset('/videos/hero-espresso-loop.mp4') }}" type="video/mp4">
            </video>

            <!-- Warm Vignette Gradient Overlays: Ensure Crystal-Clear Text Contrast -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#07241C]/95 via-[#07241C]/70 to-transparent sm:w-3/4"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#07241C]/90 via-transparent to-[#07241C]/40"></div>

            <!-- Hero Typography & Action Controls Floating Over Video -->
            <div class="max-w-6xl mx-auto px-6 sm:px-12 lg:px-16 py-28 sm:py-36 relative z-10 w-full">
                <div class="max-w-2xl space-y-6">

                    <!-- Live Campus Status Capsule -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-black/40 backdrop-blur-md text-xs font-bold uppercase tracking-widest text-[#F5B82E] border border-white/15">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-white">Live Dari Kampus 2 UMRI Pekanbaru</span>
                    </div>

                    <!-- Giant Geometric Display Headline (Spacious Outfit, Zero Berdempetan) -->
                    <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-extrabold uppercase tracking-wide text-white leading-[1.08] drop-shadow-xl">
                        Where Coffee<br/>
                        <span class="text-[#F5B82E]">Meets Calm.</span>
                    </h1>

                    <!-- Punchy 2-Sentence Subtitle -->
                    <p class="text-base sm:text-lg text-white/90 font-medium leading-relaxed max-w-lg drop-shadow">
                        Ruang tenang ber-AC untuk fokus nugas di siang hari, dan courtyard terbuka hijau dengan aneka street food malam hari.
                    </p>

                    <!-- Primary Action Buttons -->
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a
                            href="/reservasi"
                            wire:navigate
                            class="px-8 py-4 rounded-full text-xs sm:text-sm font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 text-center shadow-lg cursor-pointer"
                        >
                            Reservasi Meja Sekarang &rarr;
                        </a>
                        <a
                            href="/menu"
                            wire:navigate
                            class="px-7 py-4 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider bg-white/20 hover:bg-white/30 text-white backdrop-blur-md transition text-center border border-white/20 cursor-pointer"
                        >
                            Katalog Menu
                        </a>
                    </div>

                    <!-- Bottom Bar: Rating Pill + Interactive Video Control -->
                    <div class="pt-6 border-t border-white/20 flex flex-wrap items-center justify-between gap-4 text-xs text-white/90">
                        <div class="flex items-center gap-4">
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

                        <!-- Video Play/Pause Toggle -->
                        <button
                            type="button"
                            @click="toggleVideo()"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/40 backdrop-blur-md text-xs font-semibold text-white/80 hover:text-white border border-white/10 transition cursor-pointer"
                            title="Nyalakan / Hentikan Video Background"
                        >
                            <span x-show="videoPlaying">⏸ Jeda Video</span>
                            <span x-show="!videoPlaying" style="display: none;">▶ Putar Video</span>
                        </button>
                    </div>

                </div>
            </div>
        </section>
    </div>

    <!-- 2. INFINITE RUNNING TICKER RIBBON (TARAQ & MOCHANF STYLE) -->
    <div class="w-full bg-[#F5B82E] text-[#07241C] py-3.5 overflow-hidden select-none">
        <div class="animate-marquee flex items-center gap-8 whitespace-nowrap font-display text-base sm:text-xl font-extrabold uppercase tracking-widest">
            <span>★ UM.CO HOUSE</span>
            <span>★ ESPRESSO BAR</span>
            <span>★ KAMPUS 2 UMRI PEKANBARU</span>
            <span>★ SLOW BAR V60</span>
            <span>★ COLD BREW BOTOLAN</span>
            <span>★ RUANG AC NUGAS INDOOR</span>
            <span>★ COURTYARD STREET FOOD</span>
            <span>★ BUKA 08.00 - 21.00 WIB</span>
            <span>★ 100% ARABICA GRADE 1</span>
            <!-- Seamless loop duplicate -->
            <span>★ UM.CO HOUSE</span>
            <span>★ ESPRESSO BAR</span>
            <span>★ KAMPUS 2 UMRI PEKANBARU</span>
            <span>★ SLOW BAR V60</span>
            <span>★ COLD BREW BOTOLAN</span>
            <span>★ RUANG AC NUGAS INDOOR</span>
            <span>★ COURTYARD STREET FOOD</span>
            <span>★ BUKA 08.00 - 21.00 WIB</span>
            <span>★ 100% ARABICA GRADE 1</span>
        </div>
    </div>

    <!-- 3. SECTION: THE ARTISAN BAR — INTERACTIVE TASTE SELECTOR (SANGAT BEDA & ESTETIK) -->
    <section
        x-data="{
            activeTaste: 'aren',
            tastes: {
                aren: {
                    name: 'Es Kopi Susu Aren',
                    category: 'Signature Espresso',
                    price: 'Rp 22.000',
                    slug: 'kopi-susu-umco-aren',
                    desc: 'Kopi susu gula aren racikan khas UM.CO dengan double ristretto biji Mandheling, susu segar creamy, dan pemanis alami nira aren lokal.',
                    notes: ['Karamel Tebal', 'Gula Aren Asli', 'Body Gurih', 'Sedang'],
                    temp: 'Dingin (Iced)',
                    origin: 'Arabica Mandheling & Gayo',
                    image: '{{ asset('/images/hero-iced-coffee-splash.jpg') }}'
                },
                matcha: {
                    name: 'Matcha Latte Uji',
                    category: 'Artisan Non-Coffee',
                    price: 'Rp 24.000',
                    slug: 'matcha-latte-uji',
                    desc: 'Bubuk green tea murni asal Uji Jepang dipadukan dengan susu segar berbusa lembut dan sirup vanila ringan. Sejuk dan menenangkan pikiran.',
                    notes: ['Earthy Matcha', 'Cold Foam Lembut', 'Manis Pas', 'Velvety'],
                    temp: 'Dingin / Hangat',
                    origin: 'Ceremonial Grade Uji',
                    image: '{{ asset('/images/drinks-trio-banner.jpg') }}'
                },
                latte: {
                    name: 'Cafe Latte Single Origin',
                    category: 'Classic Espresso',
                    price: 'Rp 20.000',
                    slug: 'cafe-latte',
                    desc: 'Single shot espresso Arabica Sumatera dengan micro-foam susu hangat yang halus dan latte art cantik di cangkir keramik.',
                    notes: ['Dark Cacao', 'Nutty', 'Busa Halus', 'Seimbang'],
                    temp: 'Hangat (Hot)',
                    origin: 'Full Arabica Sumatera',
                    image: '{{ asset('/images/hero-banner-full.jpg') }}'
                },
                pastry: {
                    name: 'Donat Artisan Brulee',
                    category: 'Warm Bites & Toast',
                    price: 'Rp 16.000',
                    slug: 'donat-artisan-brulee-cinnamon',
                    desc: 'Donat kentang artisan empuk dengan lapisan gula karamel renyah torch brulee dan taburan bubuk kayu manis wangi.',
                    notes: ['Karamel Renyah', 'Adonan Fluffy', 'Kayu Manis', 'Fresh Baked'],
                    temp: 'Hangat Saji',
                    origin: 'Dapur UM.CO Fresh Daily',
                    image: '{{ asset('/images/bistro-patio-table.jpg') }}'
                }
            }
        }"
        class="py-20 sm:py-28 bg-[#0A261C] text-white relative overflow-hidden"
    >
        <!-- Background Ambient Glow -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#F5B82E]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6 border-b border-white/10 pb-8">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">01 / Curated Taste</span>
                    <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-white mt-1 leading-tight">
                        Pilihan Rasa Khas Barista.
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-[#9AB4A6] max-w-sm font-medium">
                    Pilih karakter rasa favoritmu di bawah ini untuk melihat detail racikan dan langsung amankan pesanan.
                </p>
            </div>

            <!-- Taste Navigation Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-4 scrollbar-none mb-10">
                <button
                    type="button"
                    @click="activeTaste = 'aren'"
                    class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition cursor-pointer whitespace-nowrap"
                    :class="activeTaste === 'aren' ? 'bg-[#F5B82E] text-[#07241C]' : 'bg-white/10 text-white hover:bg-white/20'"
                >
                    ☕ Kopi Susu Aren
                </button>
                <button
                    type="button"
                    @click="activeTaste = 'matcha'"
                    class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition cursor-pointer whitespace-nowrap"
                    :class="activeTaste === 'matcha' ? 'bg-[#F5B82E] text-[#07241C]' : 'bg-white/10 text-white hover:bg-white/20'"
                >
                    🍵 Matcha Uji
                </button>
                <button
                    type="button"
                    @click="activeTaste = 'latte'"
                    class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition cursor-pointer whitespace-nowrap"
                    :class="activeTaste === 'latte' ? 'bg-[#F5B82E] text-[#07241C]' : 'bg-white/10 text-white hover:bg-white/20'"
                >
                    🥛 Classic Latte
                </button>
                <button
                    type="button"
                    @click="activeTaste = 'pastry'"
                    class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition cursor-pointer whitespace-nowrap"
                    :class="activeTaste === 'pastry' ? 'bg-[#F5B82E] text-[#07241C]' : 'bg-white/10 text-white hover:bg-white/20'"
                >
                    🍩 Donat Brulee
                </button>
            </div>

            <!-- Interactive Spotlight Card -->
            <div class="bg-white/5 border border-white/10 rounded-3xl sm:rounded-[2.5rem] p-6 sm:p-10 lg:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    <!-- Left: Large Food/Drink Image -->
                    <div class="lg:col-span-5 relative rounded-2xl overflow-hidden aspect-square bg-[#07241C]">
                        <img
                            :src="tastes[activeTaste].image"
                            :alt="tastes[activeTaste].name"
                            class="w-full h-full object-cover transition-all duration-700"
                            loading="lazy"
                        />
                        <div class="absolute top-4 left-4">
                            <span class="px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C]" x-text="tastes[activeTaste].category"></span>
                        </div>
                    </div>

                    <!-- Right: Recipe Details & 1-Click Pre-order -->
                    <div class="lg:col-span-7 space-y-6">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="font-display text-2xl sm:text-4xl font-extrabold uppercase tracking-wide text-white" x-text="tastes[activeTaste].name"></h3>
                                <span class="font-mono text-xl sm:text-2xl font-bold text-[#F5B82E]" x-text="tastes[activeTaste].price"></span>
                            </div>
                            <p class="text-sm sm:text-base text-[#D1E0D7] mt-3 leading-relaxed font-medium" x-text="tastes[activeTaste].desc"></p>
                        </div>

                        <!-- Flavor Notes Badges -->
                        <div class="space-y-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-[#F5B82E] block">Karakter &amp; Profil Rasa</span>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="note in tastes[activeTaste].notes" :key="note">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white/10 text-white text-xs font-semibold" x-text="'• ' + note"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Origin & Serving -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10 text-xs">
                            <div>
                                <span class="text-white/50 block">Penyajian</span>
                                <span class="font-bold text-white mt-0.5 block" x-text="tastes[activeTaste].temp"></span>
                            </div>
                            <div>
                                <span class="text-white/50 block">Bahan / Asal Biji</span>
                                <span class="font-bold text-white mt-0.5 block" x-text="tastes[activeTaste].origin"></span>
                            </div>
                        </div>

                        <!-- 1-Click Action to Reservation Engine with Selected Menu -->
                        <div class="pt-4 flex flex-wrap items-center gap-4">
                            <a
                                :href="'/reservasi?menu=' + tastes[activeTaste].slug"
                                wire:navigate
                                class="px-8 py-3.5 rounded-full text-xs font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 cursor-pointer shadow-lg"
                            >
                                Pre-Order &amp; Amankan Meja &rarr;
                            </a>
                            <a
                                href="/menu"
                                wire:navigate
                                class="px-6 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white/10 hover:bg-white/20 text-white transition cursor-pointer"
                            >
                                Buka Semua Menu
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- 4. SECTION: DUAL-ZONE ARCHITECTURAL SPLIT-SCREEN (WING BARAT vs WING TIMUR - SANGAT UNIK!) -->
    <section class="py-20 sm:py-28 bg-[#FAF7EE] relative">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-14">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">02 / Dua Zona, Satu Rumah</span>
                <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-[#0D3B2E] leading-tight">
                    Satu Gedung, Dua Suasana Berbeda.
                </h2>
                <p class="text-sm sm:text-base text-[#122018]/70 max-w-md mx-auto font-medium">
                    Denah arsitektur berbentuk huruf "H" memisahkan area tenang fokus kerja dan area santai terbuka.
                </p>
            </div>

            <!-- Dual Split Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                
                <!-- Zone 1: Wing Barat (Indoor AC Focus) -->
                <div class="rounded-3xl sm:rounded-[2.5rem] bg-[#07241C] text-white p-8 sm:p-10 flex flex-col justify-between space-y-8 relative overflow-hidden group shadow-xl">
                    <div class="space-y-4 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-[#F5B82E] text-[#07241C]">
                                Wing Barat (Indoor)
                            </span>
                            <span class="text-xs text-white/60 font-semibold">08.00 – 21.00 WIB</span>
                        </div>

                        <h3 class="font-display text-2xl sm:text-4xl font-extrabold uppercase tracking-wide text-white leading-tight">
                            Zona Tenang<br/>
                            Fokus &amp; Belajar.
                        </h3>

                        <p class="text-sm text-[#D1E0D7] leading-relaxed font-medium">
                            Didesain khusus untuk laptopan, tugas kuliah, dan rapat kecil. Bebas bising jalan raya dengan suhu pendingin udara stabil dan meja bercolokan listrik.
                        </p>

                        <!-- Feature Badges -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10 text-xs">
                                <span class="font-bold text-white block">❄️ AC 22°C</span>
                                <span class="text-white/60 text-[11px]">Suhu sejuk stabil</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10 text-xs">
                                <span class="font-bold text-white block">⚡ Colokan di Meja</span>
                                <span class="text-white/60 text-[11px]">Di setiap kursi</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10 text-xs">
                                <span class="font-bold text-white block">📶 Wi-Fi 100M</span>
                                <span class="text-white/60 text-[11px]">Fiber optik cepat</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/5 border border-white/10 text-xs">
                                <span class="font-bold text-white block">☕ Slow Bar</span>
                                <span class="text-white/60 text-[11px]">Manual brew V60</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/10 relative z-10">
                        <a
                            href="/reservasi?note=Indoor%20Wing%20Barat"
                            wire:navigate
                            class="w-full inline-flex items-center justify-center py-4 rounded-full text-xs font-black uppercase tracking-wider bg-white text-[#07241C] hover:bg-[#F5B82E] transition cursor-pointer"
                        >
                            Reservasi Meja Indoor &rarr;
                        </a>
                    </div>
                </div>

                <!-- Zone 2: Wing Timur (Courtyard Outdoor & Street Food) -->
                <div class="rounded-3xl sm:rounded-[2.5rem] bg-[#145341] text-white p-8 sm:p-10 flex flex-col justify-between space-y-8 relative overflow-hidden group shadow-xl">
                    <div class="space-y-4 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="px-3.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-white text-[#07241C]">
                                Wing Timur (Outdoor)
                            </span>
                            <span class="text-xs text-white/70 font-semibold">15.00 – 21.00 WIB (Puncak Street Food)</span>
                        </div>

                        <h3 class="font-display text-2xl sm:text-4xl font-extrabold uppercase tracking-wide text-white leading-tight">
                            Courtyard Terbuka<br/>
                            &amp; Street Food Malam.
                        </h3>

                        <p class="text-sm text-[#D1E0D7] leading-relaxed font-medium">
                            Taman tengah terbuka di bawah pepohonan rindang. Menjelang senja, aneka gerobak street food kampus menyajikan sate taichan, dimsum, toast, dan minuman segar.
                        </p>

                        <!-- Feature Badges -->
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3 rounded-2xl bg-white/10 border border-white/10 text-xs">
                                <span class="font-bold text-white block">🍃 Udara Terbuka</span>
                                <span class="text-white/70 text-[11px]">Angin sore kampus</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/10 border border-white/10 text-xs">
                                <span class="font-bold text-white block">🍢 6+ Stand Jajanan</span>
                                <span class="text-white/70 text-[11px]">Street food nikmat</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/10 border border-white/10 text-xs">
                                <span class="font-bold text-white block">🌙 Lampu Festoon</span>
                                <span class="text-white/70 text-[11px]">Suasana hangat malam</span>
                            </div>
                            <div class="p-3 rounded-2xl bg-white/10 border border-white/10 text-xs">
                                <span class="font-bold text-white block">🚬 Outdoor Ramah</span>
                                <span class="text-white/70 text-[11px]">Area ngobrol leluasa</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/10 relative z-10">
                        <a
                            href="/reservasi?note=Outdoor%20Courtyard"
                            wire:navigate
                            class="w-full inline-flex items-center justify-center py-4 rounded-full text-xs font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition cursor-pointer"
                        >
                            Reservasi Meja Courtyard &rarr;
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 5. SECTION: EDITORIAL LOOKBOOK WITH LATTE ART VIDEO LOOP (TARAQ SECTION 2 STYLE) -->
    <section class="py-20 sm:py-28 bg-[#F4EEDF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">03 / Momen &amp; Seduhan</span>
                <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-[#0D3B2E] leading-tight">
                    Every Pour Tells A Story.
                </h2>
                <p class="text-sm sm:text-base text-[#122018]/70 max-w-md mx-auto font-medium">
                    Setiap cangkir diracik segar oleh barista dengan standar ekstraksi presisi.
                </p>
            </div>

            <!-- Asymmetrical 4-Card Media Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                
                <!-- Card 1: Looping Video Preview of Latte Art (Col 5) -->
                <div class="md:col-span-5 relative rounded-3xl sm:rounded-[2.5rem] overflow-hidden bg-[#07241C] min-h-[380px] group shadow-lg">
                    <video
                        autoplay
                        loop
                        muted
                        playsinline
                        poster="{{ asset('/images/hero-photo.jpg') }}"
                        class="w-full h-full object-cover object-center scale-100 group-hover:scale-105 transition duration-700"
                    >
                        <source src="{{ asset('/videos/latte-art-loop.mp4') }}" type="video/mp4">
                    </video>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#07241C]/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 text-white">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#F5B82E]">Barista Craft</span>
                        <h4 class="font-display text-xl font-bold uppercase">Fresh Micro-Foam Latte</h4>
                    </div>
                </div>

                <!-- Card 2: Dappled Sunlight Bistro Table Photo (Col 7) -->
                <div class="md:col-span-7 flex flex-col gap-6">
                    
                    <div class="relative rounded-3xl sm:rounded-[2.5rem] overflow-hidden aspect-[16/9] bg-[#07241C] group shadow-lg">
                        <img
                            src="{{ asset('/images/bistro-patio-table.jpg') }}"
                            alt="Bistro Patio Outdoor UMCO"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-700"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6 text-white flex items-end justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-[#F5B82E]">Courtyard Patio</span>
                                <h4 class="font-display text-xl sm:text-2xl font-bold uppercase">Croissants &amp; Cold Brew</h4>
                            </div>
                            <span class="px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold text-white">
                                Outdoor Seating
                            </span>
                        </div>
                    </div>

                    <!-- Bottom 2-split cards -->
                    <div class="grid grid-cols-2 gap-6 flex-grow">
                        <div class="bg-white rounded-3xl p-6 flex flex-col justify-between">
                            <span class="w-10 h-10 rounded-full bg-[#0D3B2E] text-[#F5B82E] flex items-center justify-center font-bold text-sm">
                                01
                            </span>
                            <div class="space-y-1">
                                <h5 class="font-display text-lg font-bold text-[#0D3B2E] uppercase">Biji Grade 1</h5>
                                <p class="text-xs text-[#122018]/70 leading-relaxed font-medium">Kurasi biji kopi Mandheling &amp; Gayo dengan profil sangrai seimbang.</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl p-6 flex flex-col justify-between">
                            <span class="w-10 h-10 rounded-full bg-[#0D3B2E] text-[#F5B82E] flex items-center justify-center font-bold text-sm">
                                02
                            </span>
                            <div class="space-y-1">
                                <h5 class="font-display text-lg font-bold text-[#0D3B2E] uppercase">Kalibrasi Harian</h5>
                                <p class="text-xs text-[#122018]/70 leading-relaxed font-medium">Uji rasio ekstraksi setiap pagi pukul 07.30 WIB agar rasa konsisten.</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- 6. SECTION: LIVE RESERVATION RADAR (INTERACTIVE SLOT PICKER - SANGAT NIAT!) -->
    <div class="px-3 sm:px-6 py-6">
        <section class="rounded-[2.5rem] sm:rounded-[3rem] bg-[#07241C] text-white py-16 sm:py-20 px-6 sm:px-12 lg:px-16 relative overflow-hidden shadow-2xl">
            <!-- Subtle Radial Amber Glow -->
            <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-[#F5B82E]/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-4xl mx-auto space-y-8 relative z-10 text-center">
                
                <div class="space-y-2">
                    <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">04 / Amankan Meja Kamu</span>
                    <h2 class="font-display text-3xl sm:text-5xl lg:text-6xl font-extrabold uppercase tracking-wide text-white leading-tight">
                        Pilih Jam Kedatangan.
                    </h2>
                    <p class="text-sm sm:text-base text-[#D1E0D7] max-w-lg mx-auto font-medium">
                        Ketuk salah satu slot jam di bawah ini untuk langsung menuju tiket reservasi mejamu.
                    </p>
                </div>

                <!-- Instant Quick-Picker Slot Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 max-w-3xl mx-auto">
                    @foreach (['09:00', '11:30', '14:00', '16:30', '19:00', '20:00'] as $quickTime)
                        <a
                            href="/reservasi?time={{ $quickTime }}"
                            wire:navigate
                            class="p-4 rounded-2xl bg-white/10 hover:bg-[#F5B82E] hover:text-[#07241C] text-white transition text-center group cursor-pointer border border-white/10"
                        >
                            <span class="font-display text-xl font-bold block">{{ $quickTime }}</span>
                            <span class="text-[10px] text-white/60 group-hover:text-[#07241C]/80 uppercase tracking-wider block mt-0.5">WIB</span>
                        </a>
                    @endforeach
                </div>

                <!-- Transparency & Assurance Badges -->
                <div class="pt-4 flex flex-wrap items-center justify-center gap-6 text-xs text-white/80 font-medium border-t border-white/10">
                    <span class="inline-flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span> Reservasi Meja Saja 100% Gratis (Tanpa DP)
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span> Pre-Order Menu Opsional
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="text-emerald-400 font-bold">✓</span> Tiket Digital + WhatsApp Kasir
                    </span>
                </div>

                <div>
                    <a
                        href="/reservasi"
                        wire:navigate
                        class="px-9 py-4 rounded-full text-xs sm:text-sm font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition shadow-lg inline-block active:scale-95 cursor-pointer"
                    >
                        Buka Formulir Reservasi Lengkap &rarr;
                    </a>
                </div>

            </div>
        </section>
    </div>

    <!-- 7. SECTION: REAL VOICES (TESTIMONIALS) -->
    <section class="py-20 sm:py-28 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Ulasan Pengunjung</span>
                    <h2 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide text-[#0D3B2E] mt-1 leading-tight">
                        Why People Come Back.
                    </h2>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold text-[#145341]">
                    <span>Nilai 4.9 di Google Maps</span>
                    <span>★★★★★</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($testimonials->take(3) as $review)
                    <div class="bg-white rounded-3xl p-7 flex flex-col justify-between space-y-6 shadow-sm">
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
</x-layouts.public>
