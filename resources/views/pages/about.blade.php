<x-layouts.public>
    <x-slot:title>Cerita Kami, Denah Arsitektur & Lokasi — UM.CO House Kampus UMRI</x-slot:title>
    <x-slot:description>Mengenal UM.CO House di Kampus Universitas Muhammadiyah Riau (UMRI) Pekanbaru. Arsitektur unik berbentuk huruf H: Wing Barat Indoor AC, Courtyard Hijau, dan Street Food Corner.</x-slot:description>

    <!-- SECTION 1: HERO STORY WITH EMERALD & ORGANIC CURVE -->
    <section class="pt-32 pb-20 md:pt-40 md:pb-24 bg-[#0D3B2E] text-white relative overflow-hidden text-center">
        <!-- Ambient Warm Glow -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-20 -left-20 w-96 h-96 rounded-full bg-[#F5B82E] blur-3xl"></div>
            <div class="absolute bottom-0 right-10 w-96 h-96 rounded-full bg-[#145341] blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-4">
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-white/10 text-[#F5B82E]">
                Cerita &amp; Ruang Kampus
            </span>
            <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                Rumah Singgah di Jantung Kampus UMRI
            </h1>
            <p class="text-sm sm:text-base text-[#9AB4A6] max-w-xl mx-auto leading-relaxed">
                Tempat bertemunya ide, ketenangan nugas, dan obrolan hangat yang menyatukan civitas akademika dan warga Pekanbaru.
            </p>
        </div>

        <!-- ORGANIC WAVE DIVIDER -->
        <div class="absolute bottom-0 left-0 right-0 leading-none overflow-hidden select-none pointer-events-none">
            <svg class="relative block w-full h-12 sm:h-16 text-[#FAF7EE]" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0 C150,90 350,-40 500,40 C650,120 900,20 1200,60 L1200,120 L0,120 Z" fill="currentColor"></path>
            </svg>
        </div>
    </section>

    <!-- SECTION 2: EDITORIAL NARRATIVE (CLEAN & SPACIOUS) -->
    <section class="py-16 lg:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Awal Mula</span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-black text-[#0D3B2E] leading-tight">
                        Lahir dari Kebutuhan Ruang Belajar yang Menghargai Waktu &amp; Ide
                    </h2>
                    <p class="text-sm sm:text-base text-[#122018]/80 leading-relaxed">
                        Di sela-sela padatnya jadwal kuliah dan bimbingan, mahasiswa dan dosen memerlukan ruang antara: bukan ruang kelas yang kaku, bukan pula kamar kos yang menyendiri. UM.CO House hadir sebagai rumah ketiga tersebut.
                    </p>
                    <p class="text-sm sm:text-base text-[#122018]/80 leading-relaxed">
                        Kami menyajikan kopi nusantara terkalibrasi harian, internet 100 Mbps stabil, dan stopkontak di setiap meja dengan harga yang tetap ramah di kantong.
                    </p>

                    <!-- Fast Facts -->
                    <div class="grid grid-cols-3 gap-6 pt-4 border-t border-[#EAE2D0]">
                        <div>
                            <span class="font-serif text-3xl font-black text-[#0D3B2E]">4.9★</span>
                            <span class="text-xs text-[#122018]/60 block mt-0.5">Google Rating</span>
                        </div>
                        <div>
                            <span class="font-serif text-3xl font-black text-[#0D3B2E]">100+</span>
                            <span class="text-xs text-[#122018]/60 block mt-0.5">Kapasitas Kursi</span>
                        </div>
                        <div>
                            <span class="font-serif text-3xl font-black text-[#0D3B2E]">100 Mbps</span>
                            <span class="text-xs text-[#122018]/60 block mt-0.5">Wi-Fi Fiber Optic</span>
                        </div>
                    </div>
                </div>

                <!-- Right Photo -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl overflow-hidden aspect-[4/3] bg-white">
                        <img
                            src="{{ asset('/images/hero-photo.jpg') }}"
                            alt="Suasana UMCO House Kampus UMRI"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: DENAH ARSITEKTUR HURUF H -->
    <section class="py-16 lg:py-24 bg-[#F4EEDF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Tata Ruang Fisik</span>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-black text-[#0D3B2E] mt-1">
                    Arsitektur Berbentuk Huruf &ldquo;H&rdquo;
                </h2>
                <p class="text-sm text-[#122018]/70 mt-2">
                    Satu gedung dengan dua suasana berbeda yang dihubungkan oleh taman tengah terbuka.
                </p>
            </div>

            <!-- 3 Zones Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Wing Barat -->
                <div class="bg-white rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#145341]">Wing Barat (Indoor AC)</span>
                        <h3 class="font-serif text-2xl font-bold text-[#0D3B2E] mt-1">Zona Tenang &amp; Kerja</h3>
                        <p class="text-xs sm:text-sm text-[#122018]/70 mt-3 leading-relaxed">
                            Dilengkapi pendingin udara 22°C stabil, colokan listrik di setiap meja, dan Slow Bar specialty coffee. Pilihan tepat untuk laptopan dan rapat kecil.
                        </p>
                    </div>
                    <ul class="mt-6 pt-6 border-t border-[#EAE2D0] space-y-2 text-xs text-[#0D3B2E]">
                        <li>✔ 36 kursi ergonomis</li>
                        <li>✔ Wi-Fi 100 Mbps prioritas</li>
                        <li>✔ Bebas asap rokok</li>
                    </ul>
                </div>

                <!-- Courtyard -->
                <div class="bg-[#0D3B2E] text-white rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#F5B82E]">Courtyard Pusat</span>
                        <h3 class="font-serif text-2xl font-bold text-white mt-1">Taman Hijau &amp; Akustik</h3>
                        <p class="text-xs sm:text-sm text-[#9AB4A6] mt-3 leading-relaxed">
                            Jantung terbuka dengan pepohonan rindang dan sirkulasi udara alami. Tempat ideal untuk mengobrol santai atau menikmati musik akustik akhir pekan.
                        </p>
                    </div>
                    <ul class="mt-6 pt-6 border-t border-white/10 space-y-2 text-xs text-[#9AB4A6]">
                        <li>✔ Kanopi peneduh alami</li>
                        <li>✔ Bangku kayu komunal</li>
                        <li>✔ Akses mudah ke Musholla</li>
                    </ul>
                </div>

                <!-- Wing Timur -->
                <div class="bg-white rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#145341]">Wing Timur (Semi-Outdoor)</span>
                        <h3 class="font-serif text-2xl font-bold text-[#0D3B2E] mt-1">Street Food Corner</h3>
                        <p class="text-xs sm:text-sm text-[#122018]/70 mt-3 leading-relaxed">
                            Area semi-outdoor yang hidup dari sore hingga malam hari dengan aneka kuliner gurih UMKM mitra kampus.
                        </p>
                    </div>
                    <ul class="mt-6 pt-6 border-t border-[#EAE2D0] space-y-2 text-xs text-[#0D3B2E]">
                        <li>✔ 40+ kursi outdoor</li>
                        <li>✔ 4 gerai kuliner UMKM</li>
                        <li>✔ Buka 15.00 – 21.00 WIB</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: OUTDOOR STREET FOOD DIRECTORY -->
    <section class="py-16 lg:py-24 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Jajanan Sore &amp; Malam</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-black text-[#0D3B2E] mt-1">
                    Gerai Street Food Corner
                </h2>
                <p class="text-xs sm:text-sm text-[#122018]/60 mt-1">Buka setiap sore mulai 15.00 hingga 21.00 WIB di pelataran Wing Timur.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($streetStalls as $stall)
                    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between">
                        <div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#FAF7EE] text-[#145341] inline-block mb-3">
                                {{ $stall->active_hours ?? '15.00 - 21.00 WIB' }}
                            </span>
                            <h3 class="font-serif text-lg font-bold text-[#0D3B2E]">{{ $stall->name }}</h3>
                            <p class="text-xs text-[#122018]/70 mt-1 leading-relaxed">{{ $stall->description }}</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-[#EAE2D0]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#145341] block">Menu Andalan:</span>
                            <span class="text-xs font-medium text-[#0D3B2E]">{{ $stall->signature_item ?? 'Aneka varian hangat' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION 5: FASILITAS KAMPUS LENGKAP -->
    <section class="py-16 lg:py-24 bg-[#F4EEDF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Kenyamanan Pengunjung</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-black text-[#0D3B2E] mt-1">
                    Fasilitas Lengkap
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($facilities as $facility)
                    <div class="bg-white rounded-3xl p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FAF7EE] text-[#0D3B2E] flex items-center justify-center flex-shrink-0 font-bold text-lg">
                            ✓
                        </div>
                        <div>
                            <h3 class="font-serif text-base font-bold text-[#0D3B2E]">{{ $facility->label }}</h3>
                            <span class="text-xs text-[#122018]/50">Tersedia untuk seluruh pengunjung</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION 6: LOKASI & PETUNJUK ARAH (CLICK-TO-LOAD FOR 100 PERFORMANCE) -->
    <section
        x-data="{ mapLoaded: false }"
        class="py-16 lg:py-24 bg-[#FAF7EE]"
    >
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Location info -->
                <div class="lg:col-span-5 space-y-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Lokasi &amp; Akses</span>
                        <h2 class="font-serif text-3xl sm:text-4xl font-black text-[#0D3B2E] mt-1">
                            Mudah Dijangkau di Kampus 2 UMRI
                        </h2>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm text-[#122018]/80 leading-relaxed">
                        <p>
                            <strong>Alamat:</strong><br/>
                            {{ \App\Support\Settings::get('contact.address', 'KAMPUS 2 UMRI, Jl. Tuanku Tambusai Ujung, Delima, Pekanbaru, Riau.') }}
                        </p>
                        <p>
                            <strong>Petunjuk Masuk:</strong><br/>
                            Masuk melalui Gerbang Utama Kampus 2 UMRI. Gedung UM.CO House berada di sisi kanan lapangan utama, mudah dikenali dengan bangunan berarsitektur modern huruf H.
                        </p>
                        <p>
                            <strong>Parkir:</strong><br/>
                            Tersedia kantong parkir motor dan mobil luas di depan gedung dengan keamanan kampus.
                        </p>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a
                            href="{{ \App\Support\Settings::get('contact.google_maps_url', 'https://maps.google.com/?q=UMRI+Pekanbaru') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-[#0D3B2E] text-white hover:bg-[#07241C] transition"
                        >
                            Buka di Google Maps &rarr;
                        </a>
                        <a
                            href="https://www.google.com/maps/dir/?api=1&destination=0.4999292,101.4155284"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3 rounded-full text-xs font-bold tracking-wide bg-white text-[#0D3B2E] hover:bg-[#F4EEDF] transition"
                        >
                            Petunjuk Rute
                        </a>
                    </div>
                </div>

                <!-- Right: Map Container -->
                <div class="lg:col-span-7">
                    <div class="w-full aspect-[16/10] rounded-3xl overflow-hidden bg-white relative">
                        <div
                            x-show="!mapLoaded"
                            class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-[#0D3B2E] text-white"
                        >
                            <span class="w-12 h-12 rounded-full bg-white/10 text-[#F5B82E] flex items-center justify-center font-bold text-xl mb-3">
                                📍
                            </span>
                            <h3 class="font-serif text-xl font-bold">Peta Interaktif Kampus UMRI</h3>
                            <p class="text-xs text-[#9AB4A6] max-w-xs mt-1 mb-6">Klik di bawah untuk memuat peta Google Maps langsung di halaman ini.</p>
                            <button
                                type="button"
                                @click="mapLoaded = true"
                                class="px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] cursor-pointer"
                            >
                                Muat Peta Interaktif
                            </button>
                        </div>

                        <template x-if="mapLoaded">
                            <iframe
                                src="https://maps.google.com/maps?q=Kampus+Universitas+Muhammadiyah+Riau+Pekanbaru&t=&z=16&ie=UTF8&iwloc=&output=embed"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                class="w-full h-full"
                            ></iframe>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 7: FINAL BOTTOM CTA -->
    <section class="py-16 bg-[#0D3B2E] text-white text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <h2 class="font-serif text-3xl sm:text-4xl font-black">
                Rencanakan Kunjunganmu Hari Ini
            </h2>
            <p class="text-xs sm:text-sm text-[#9AB4A6] max-w-md mx-auto">
                Amankan tempat dudukmu terlebih dahulu untuk belajar atau rapat kelompok santai.
            </p>
            <div class="pt-4">
                <a
                    href="/reservasi"
                    wire:navigate
                    class="px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition"
                >
                    Reservasi Meja Sekarang &rarr;
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>
