<x-layouts.public>
    <x-slot:title>Cerita Kami, Denah Arsitektur & Lokasi — UM.CO House Kampus UMRI</x-slot:title>
    <x-slot:description>Mengenal UM.CO House di Kampus Universitas Muhammadiyah Riau (UMRI) Pekanbaru. Arsitektur unik berbentuk huruf H: Wing Barat Indoor AC, Courtyard Hijau, dan Street Food Corner.</x-slot:description>

    <!-- HERO HEADER: BOLD SANS DISPLAY (NO SERIFS) -->
    <div class="px-3 sm:px-6 pt-4 pb-6">
        <section class="rounded-[2rem] sm:rounded-[2.5rem] bg-[#07241C] text-white py-20 sm:py-28 px-6 sm:px-12 text-center relative overflow-hidden">
            <div class="max-w-3xl mx-auto space-y-4 relative z-10">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-xs font-black uppercase tracking-widest text-[#F5B82E]">
                    Cerita &amp; Ruang
                </span>
                <h1 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide leading-tight text-white">
                    Where Coffee Meets Campus.
                </h1>
                <p class="text-sm sm:text-base text-[#9AB4A6] max-w-lg mx-auto">
                    Rumah singgah mahasiswa dan dosen di Kampus 2 UMRI Pekanbaru.
                </p>
            </div>
        </section>
    </div>

    <!-- 2. EDITORIAL NARRATIVE & BIG PHOTOGRAPHY -->
    <section class="py-12 lg:py-20 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Ruang Ketiga Kampus</span>
                    <h2 class="font-display text-2xl sm:text-4xl font-extrabold uppercase tracking-wide text-[#0D3B2E] leading-tight">
                        Tempat Lahirnya Ide &amp; Kolaborasi.
                    </h2>
                    <p class="text-sm sm:text-base text-[#122018]/80 leading-relaxed font-medium">
                        UM.CO House dirancang sebagai ruang antara: bukan ruang kelas yang kaku, bukan pula kamar kos yang menyendiri. Tempat beristirahat sejenak, bertukar pikiran, dan menikmati seduhan kopi berkualitas dengan harga terjangkau.
                    </p>

                    <!-- Key Numbers -->
                    <div class="grid grid-cols-3 gap-6 pt-4 border-t border-[#EAE2D0]">
                        <div>
                            <span class="font-display text-3xl font-black text-[#0D3B2E]">4.9 ★</span>
                            <span class="text-xs text-[#122018]/60 block mt-0.5">Google Rating</span>
                        </div>
                        <div>
                            <span class="font-display text-3xl font-black text-[#0D3B2E]">100+</span>
                            <span class="text-xs text-[#122018]/60 block mt-0.5">Kapasitas Kursi</span>
                        </div>
                        <div>
                            <span class="font-display text-3xl font-black text-[#0D3B2E]">100M</span>
                            <span class="text-xs text-[#122018]/60 block mt-0.5">Wi-Fi Fiber</span>
                        </div>
                    </div>
                </div>

                <!-- Big Photo Container -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl overflow-hidden aspect-[4/3] bg-white">
                        <img
                            src="{{ asset('/images/hero-photo.jpg') }}"
                            alt="UMCO House Kampus UMRI"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. DENAH ARSITEKTUR HURUF H -->
    <section class="py-16 bg-[#F4EEDF]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-14 space-y-2">
                <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Denah Bangunan</span>
                <h2 class="font-display text-2xl sm:text-4xl font-extrabold uppercase tracking-wide text-[#0D3B2E]">
                    Arsitektur Huruf &ldquo;H&rdquo;
                </h2>
                <p class="text-xs sm:text-sm text-[#122018]/60">Dua sayap suasana berbeda yang dihubungkan oleh taman tengah terbuka.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Wing Barat -->
                <div class="bg-white rounded-3xl p-8 space-y-4">
                    <span class="text-[10px] font-black uppercase tracking-wider text-[#145341] px-3 py-1 rounded-full bg-[#FAF7EE] inline-block">
                        Wing Barat (Indoor AC)
                    </span>
                    <h3 class="font-display text-2xl font-black text-[#0D3B2E] uppercase">Zona Tenang</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed">
                        Pendingin udara 22°C stabil, colokan listrik di setiap meja, dan Slow Bar specialty coffee. Pilihan tepat untuk laptopan dan rapat kecil tanpa kebisingan jalan.
                    </p>
                </div>

                <!-- Courtyard -->
                <div class="bg-[#07241C] text-white rounded-3xl p-8 space-y-4">
                    <span class="text-[10px] font-black uppercase tracking-wider text-[#F5B82E] px-3 py-1 rounded-full bg-white/10 inline-block">
                        Courtyard Tengah
                    </span>
                    <h3 class="font-display text-2xl font-black text-white uppercase">Taman Hijau</h3>
                    <p class="text-xs sm:text-sm text-[#9AB4A6] leading-relaxed">
                        Jantung terbuka dengan pepohonan rindang dan sirkulasi udara alami. Tempat ideal untuk mengobrol santai atau menikmati angin sore kampus.
                    </p>
                </div>

                <!-- Wing Timur -->
                <div class="bg-white rounded-3xl p-8 space-y-4">
                    <span class="text-[10px] font-black uppercase tracking-wider text-[#145341] px-3 py-1 rounded-full bg-[#FAF7EE] inline-block">
                        Wing Timur (Semi-Outdoor)
                    </span>
                    <h3 class="font-display text-2xl font-black text-[#0D3B2E] uppercase">Street Food</h3>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed">
                        Area santai yang hidup mulai sore hari dengan aneka kuliner gurih UMKM mitra kampus.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. STREET FOOD DIRECTORY -->
    <section class="py-16 bg-[#FAF7EE]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-baseline justify-between border-b border-[#EAE2D0] pb-3">
                <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-[#0D3B2E]">Street Food Corner</h2>
                <span class="text-xs font-bold text-[#145341]">Buka 15.00 – 21.00 WIB</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($streetStalls as $stall)
                    <div class="bg-white rounded-3xl p-6 flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="text-[10px] font-mono font-bold text-[#145341]">{{ $stall->active_hours }}</span>
                            <h3 class="font-display font-bold text-lg text-[#0D3B2E]">{{ $stall->name }}</h3>
                            <p class="text-xs text-[#122018]/60 leading-relaxed">{{ $stall->description }}</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-[#EAE2D0]">
                            <span class="text-[10px] uppercase font-bold text-[#145341] block">Menu Andalan:</span>
                            <span class="text-xs font-bold text-[#0D3B2E]">{{ $stall->signature_item }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. LOCATION & GOOGLE MAPS -->
    <section
        x-data="{ mapLoaded: false }"
        class="py-16 bg-[#F4EEDF]"
    >
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-xs font-black uppercase tracking-widest text-[#145341]">Lokasi Kampus</span>
                    <h2 class="font-display text-3xl sm:text-4xl font-black uppercase text-[#0D3B2E]">
                        Kampus 2 UMRI Pekanbaru
                    </h2>
                    <p class="text-xs sm:text-sm text-[#122018]/70 leading-relaxed">
                        Jl. Tuanku Tambusai Ujung, Delima, Pekanbaru. Masuk dari gerbang utama, gedung UM.CO berada di sebelah kanan lapangan utama.
                    </p>
                    <div class="pt-2">
                        <a
                            href="{{ \App\Support\Settings::get('contact.google_maps_url', 'https://maps.google.com/?q=UMRI+Pekanbaru') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-[#0D3B2E] text-white hover:bg-[#07241C] transition inline-block"
                        >
                            Buka di Google Maps &rarr;
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="w-full aspect-[16/10] rounded-3xl overflow-hidden bg-white relative">
                        <div
                            x-show="!mapLoaded"
                            class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-[#07241C] text-white"
                        >
                            <h3 class="font-display text-xl font-bold uppercase mb-2">Peta Interaktif Kampus</h3>
                            <button
                                type="button"
                                @click="mapLoaded = true"
                                class="px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] cursor-pointer"
                            >
                                Muat Peta Google Maps
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
                                class="w-full h-full"
                            ></iframe>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
