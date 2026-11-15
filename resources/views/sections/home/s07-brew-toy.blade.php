@php
    $verdicts = $section?->data['verdicts'] ?? [];
    $brewVideo = $section?->media['video'] ?? '/videos/latte-art-loop.mp4';
    $brewPoster = $section?->media['poster'] ?? '/images/hero-photo.webp';
@endphp

<section
    id="seduh"
    data-nav-theme="light"
    data-module="brew-toy"
    data-verdicts='@json($verdicts)'
    class="umco-section bg-[var(--batu)] text-[var(--tinta)]"
>
    <div class="umco-container space-y-10">
        <div class="max-w-2xl space-y-2">
            <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                {{ $section?->heading ?: 'Coba takar seduhanmu' }}
            </h2>
            <p class="type-body text-[var(--tinta-70)]">
                {{ $section?->subheading ?: 'Geser tiga tuas di bawah ini untuk melihat kira-kira karakter rasa di bar.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">
            {{-- Kiri: Panel Tuas Takaran di Atas Permukaan Putih Bersih --}}
            <div class="lg:col-span-6 rounded-2xl bg-[var(--kertas-terang)] p-6 sm:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-semibold">
                            <label for="brew-coffee">Takaran kopi</label>
                            <span data-brew-coffee-val class="tabular-nums text-[var(--mentega-tua)]">15 gram</span>
                        </div>
                        <input id="brew-coffee" data-brew-coffee type="range" min="10" max="25" value="15" step="1" class="umco-range">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-semibold">
                            <label for="brew-temp">Suhu air panas</label>
                            <span data-brew-temp-val class="tabular-nums text-[var(--mentega-tua)]">92 C</span>
                        </div>
                        <input id="brew-temp" data-brew-temp type="range" min="80" max="100" value="92" step="1" class="umco-range">
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-semibold">
                            <label for="brew-time">Lama tuang</label>
                            <span data-brew-time-val class="tabular-nums text-[var(--mentega-tua)]">3 menit</span>
                        </div>
                        <input id="brew-time" data-brew-time type="range" min="1" max="5" value="3" step="1" class="umco-range">
                    </div>
                </div>

                <div class="pt-4 border-t border-[var(--garis)] space-y-2">
                    <div class="flex items-center justify-between text-xs text-[var(--tinta-70)]">
                        <span>Perbandingan seduh</span>
                        <span data-brew-ratio class="font-semibold text-[var(--tinta)] tabular-nums">1 : 15,0</span>
                    </div>
                    <p data-brew-verdict class="font-hand text-2xl sm:text-3xl leading-snug text-[var(--tinta)]">
                        Pas. Ini yang biasanya kami keluarkan di bar.
                    </p>
                    <p class="text-xs text-[var(--tinta-70)]">
                        Simulasi rasa di layar untuk seru-seruan, tidak masuk ke form pesan menu.
                    </p>
                </div>
            </div>

            {{-- Kanan: Video Barista Latte Art + Swatch Ekstraksi --}}
            <div class="lg:col-span-6">
                <div class="relative h-72 sm:h-96 lg:h-full lg:min-h-[380px] rounded-2xl overflow-hidden bg-[var(--tinta)]">
                    <video
                        class="hidden sm:block w-full h-full object-cover opacity-90"
                        src="{{ asset($brewVideo) }}"
                        poster="{{ asset($brewPoster) }}"
                        autoplay
                        loop
                        muted
                        playsinline
                        preload="metadata"
                    ></video>
                    <img
                        src="{{ asset($brewPoster) }}"
                        alt="Barista UMCO menuang susu panas untuk seduhan harian"
                        loading="lazy"
                        decoding="async"
                        class="sm:hidden w-full h-full object-cover"
                    >
                    <div class="absolute inset-x-4 bottom-4 rounded-xl bg-[var(--kertas-terang)]/95 px-4 py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                data-brew-tone
                                class="w-4 h-4 rounded-full shrink-0 transition-colors duration-300"
                                style="background-color: #6B4226;"
                            ></span>
                            <span class="text-xs sm:text-sm font-medium text-[var(--tinta)]">Warna & kepekatan seduhan bar</span>
                        </div>
                        <span class="text-xs font-semibold text-[var(--mentega-tua)]">225 ml air</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
