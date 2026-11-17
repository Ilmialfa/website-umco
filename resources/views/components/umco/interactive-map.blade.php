@props([
    'surface' => 'bg-[var(--kertas)]',
    'eyebrow' => 'arah jalan ke sini',
    'heading' => 'Peta Interaktif UMCO House',
    'subheading' => 'Berada di kawasan Kampus 2 UMRI Pekanbaru. Geser, perbesar, aktifkan sudut 3D, atau pindah ke tampilan Google Maps.',
    'ctaLabel' => 'Pesan meja',
    'ctaUrl' => '/reservasi',
])

@php
    $brandName = (\App\Support\Settings::get('brand.brand_name') ?: 'UMCO') . ' House';
    $address = trim((string) \App\Support\Settings::get('contact.address', 'Jl. Tuanku Tambusai No. 2, Delima, Kec. Binawidya, Kota Pekanbaru, Riau 28291'));
    $operatingDays = trim((string) \App\Support\Settings::get('hours.operating_days', 'Buka setiap hari, 08.00 - 22.00 WIB'));
    $plusCode = trim((string) \App\Support\Settings::get('contact.plus_code', 'FCX8+X6 Delima, Pekanbaru'));
    $mapsLink = trim((string) \App\Support\Settings::get('contact.google_maps_url', 'https://maps.app.goo.gl/mySUaws1ixmSqiia8')) ?: 'https://maps.app.goo.gl/mySUaws1ixmSqiia8';

    $rawCoords = trim((string) \App\Support\Settings::get('contact.coordinates', '0.4999292, 101.4155284'));
    $parts = array_map('trim', explode(',', $rawCoords));
    $lat = isset($parts[0]) && is_numeric($parts[0]) ? (float) $parts[0] : 0.4999292;
    $lng = isset($parts[1]) && is_numeric($parts[1]) ? (float) $parts[1] : 101.4155284;

    $googleEmbedUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.666443307438!2d101.41552840000001!3d0.4999291999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5a9dbd040689f%3A0x576c80063acf228c!2sUMCO%20HOUSE!5e0!3m2!1sid!2sid!4v1794884879042!5m2!1sid!2sid';
@endphp

<section
    id="lokasi-umco"
    data-nav-theme="light"
    data-module="location-map"
    data-lat="{{ $lat }}"
    data-lng="{{ $lng }}"
    data-brand="{{ $brandName }}"
    data-subtitle="Kampus 2 UMRI Pekanbaru"
    x-data="{
        mode: 'voyager',
        is3d: false,
        toast: null,
        toastTimer: null,
        showToast(msg) {
            this.toast = msg;
            if (this.toastTimer) clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => { this.toast = null; }, 2200);
        },
        setMode(next) {
            this.mode = next;
            if (next !== 'gmaps') {
                this.$root.dispatchEvent(new CustomEvent('umco:map-style', { detail: { style: next } }));
            }
        },
        toggle3D() {
            if (this.mode === 'gmaps') this.setMode('voyager');
            this.is3d = !this.is3d;
            this.$root.dispatchEvent(new CustomEvent('umco:map-3d', { detail: { enabled: this.is3d } }));
            this.showToast(this.is3d ? 'Mode perspektif 3D diaktifkan' : 'Kembali ke tampilan 2D');
        },
        focusLocation() {
            if (this.mode === 'gmaps') this.setMode('voyager');
            this.$root.dispatchEvent(new CustomEvent('umco:map-focus'));
        },
        zoomIn() {
            this.$root.dispatchEvent(new CustomEvent('umco:map-zoom-in'));
        },
        zoomOut() {
            this.$root.dispatchEvent(new CustomEvent('umco:map-zoom-out'));
        }
    }"
    @umco:map-fallback="mode = 'gmaps'"
    class="umco-section {{ $surface }} text-[var(--tinta)]"
>
    <div class="umco-container space-y-8 sm:space-y-10">
        {{-- Header editorial kiri --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="max-w-2xl space-y-2.5">
                <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                    {{ $eyebrow }}
                </p>
                <h2 data-reveal="rise" class="type-h2 font-display font-bold leading-[1.08]">
                    {{ $heading }}
                </h2>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] leading-relaxed">
                    {{ $subheading }}
                </p>
            </div>

            <a
                href="{{ $mapsLink }}"
                target="_blank"
                rel="noopener noreferrer"
                class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-[var(--mentega)] hover:text-[var(--mentega-tua)] underline underline-offset-4 shrink-0"
            >
                <span>Petunjuk arah di Google Maps</span>
                <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
            </a>
        </div>

        {{-- Kanvas Peta Interaktif --}}
        <div class="rounded-3xl bg-[var(--kertas-terang)] p-2 sm:p-3">
            <div class="relative w-full h-[380px] sm:h-[480px] lg:h-[520px] rounded-2xl overflow-hidden bg-[var(--batu)]">
                {{-- Layer MapLibre GL JS --}}
                <div
                    x-show="mode !== 'gmaps'"
                    data-map-canvas
                    class="absolute inset-0 w-full h-full"
                    aria-label="Peta interaktif lokasi UMCO House"
                ></div>

                {{-- Layer Google Maps Asli (saat tab Google Maps dipilih atau fallback) --}}
                <template x-if="mode === 'gmaps'">
                    <div class="absolute inset-0 z-0 w-full h-full">
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-6 text-center bg-[var(--batu)] text-[var(--tinta-70)]">
                            <p class="text-sm font-medium">Memuat Google Maps UMCO House...</p>
                            <a
                                href="{{ $mapsLink }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn-umco btn-umco-primary min-h-0 h-10 px-5 text-xs sm:text-sm"
                            >
                                <span>Buka Google Maps di tab baru</span>
                                <svg class="w-3.5 h-3.5 shrink-0"><use href="#icon-arrow-right"/></svg>
                            </a>
                        </div>
                        <iframe
                            src="{{ $googleEmbedUrl }}"
                            title="Google Maps UMCO House"
                            class="relative w-full h-full border-0"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </template>

                {{-- Panel Pilihan Gaya Peta (otomatis bergeser rapi saat mode Google Maps agar kotak info Google tidak tertutup) --}}
                <div
                    class="absolute z-20 rounded-2xl bg-[var(--kertas-terang)]/95 p-1.5 border border-[var(--tinta)]/8 transition-all"
                    :class="mode === 'gmaps' ? 'top-3 right-3 sm:top-4 sm:right-4 max-w-[calc(100%-1.5rem)]' : 'top-3 left-3 sm:top-4 sm:left-4 max-w-[calc(100%-6rem)]'"
                >
                    <p
                        x-show="mode !== 'gmaps'"
                        class="px-2.5 pt-1 pb-1.5 text-[10px] font-semibold tracking-[0.12em] uppercase text-[var(--tinta-40)] flex items-center gap-1.5"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--mentega)]"></span>
                        <span>Gaya Peta</span>
                    </p>
                    <div
                        class="flex flex-wrap gap-1"
                        :class="mode === 'gmaps' ? '' : 'sm:flex-col'"
                    >
                        <button
                            type="button"
                            @click="setMode('voyager')"
                            class="px-3 py-1.5 rounded-xl text-left text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap"
                            :class="mode === 'voyager' ? 'bg-[var(--mentega)] text-white' : 'text-[var(--tinta-70)] hover:bg-[var(--batu)] hover:text-[var(--tinta)]'"
                        >
                            Standar
                        </button>
                        <button
                            type="button"
                            @click="setMode('positron')"
                            class="px-3 py-1.5 rounded-xl text-left text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap"
                            :class="mode === 'positron' ? 'bg-[var(--mentega)] text-white' : 'text-[var(--tinta-70)] hover:bg-[var(--batu)] hover:text-[var(--tinta)]'"
                        >
                            Minimalis
                        </button>
                        <button
                            type="button"
                            @click="setMode('dark')"
                            class="px-3 py-1.5 rounded-xl text-left text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap"
                            :class="mode === 'dark' ? 'bg-[var(--mentega)] text-white' : 'text-[var(--tinta-70)] hover:bg-[var(--batu)] hover:text-[var(--tinta)]'"
                        >
                            Kontras Gelap
                        </button>
                        <button
                            type="button"
                            @click="setMode('gmaps')"
                            class="px-3 py-1.5 rounded-xl text-left text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap"
                            :class="mode === 'gmaps' ? 'bg-[var(--mentega)] text-white' : 'text-[var(--tinta-70)] hover:bg-[var(--batu)] hover:text-[var(--tinta)]'"
                        >
                            Google Maps
                        </button>
                    </div>
                </div>

                {{-- Panel Kanan Atas: Fokus, Mode 3D, Zoom (khusus MapLibre; Google Maps punya kontrol sendiri) --}}
                <div x-show="mode !== 'gmaps'" class="absolute top-3 right-3 sm:top-4 sm:right-4 z-10 flex flex-col gap-1.5">
                    <button
                        type="button"
                        @click="focusLocation()"
                        title="Pusatkan ke lokasi UMCO House"
                        aria-label="Pusatkan ke lokasi UMCO House"
                        class="w-10 h-10 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--mentega)] hover:text-white inline-flex items-center justify-center transition-colors cursor-pointer border border-[var(--tinta)]/10"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                        </svg>
                    </button>

                    <button
                        type="button"
                        @click="toggle3D()"
                        title="Aktifkan / matikan sudut 3D"
                        aria-label="Mode perspektif 3D"
                        class="w-10 h-10 rounded-full inline-flex items-center justify-center text-xs font-display font-bold transition-colors cursor-pointer border border-[var(--tinta)]/10"
                        :class="is3d && mode !== 'gmaps' ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--batu)]'"
                    >
                        3D
                    </button>

                    <div x-show="mode !== 'gmaps'" class="hidden sm:flex flex-col rounded-full bg-[var(--kertas-terang)] border border-[var(--tinta)]/10 overflow-hidden">
                        <button
                            type="button"
                            @click="zoomIn()"
                            aria-label="Perbesar peta"
                            class="w-10 h-9 inline-flex items-center justify-center text-sm font-bold text-[var(--tinta)] hover:bg-[var(--batu)] transition-colors cursor-pointer"
                        >+</button>
                        <span class="h-px bg-[var(--garis)]"></span>
                        <button
                            type="button"
                            @click="zoomOut()"
                            aria-label="Perkecil peta"
                            class="w-10 h-9 inline-flex items-center justify-center text-sm font-bold text-[var(--tinta)] hover:bg-[var(--batu)] transition-colors cursor-pointer"
                        >−</button>
                    </div>
                </div>

                {{-- Toast Mode 3D --}}
                <div
                    x-show="toast !== null"
                    x-transition.opacity.duration.150ms
                    style="display: none;"
                    class="pointer-events-none absolute bottom-4 left-1/2 -translate-x-1/2 z-20 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] px-4 py-1.5 text-xs font-semibold whitespace-nowrap"
                >
                    <span x-text="toast"></span>
                </div>

                {{-- Kartu Informasi Mengambang (Desktop / Tablet) --}}
                <div
                    x-show="mode !== 'gmaps'"
                    class="hidden sm:flex flex-col gap-3.5 absolute bottom-4 right-4 z-10 w-[392px] max-w-[calc(100%-2rem)] rounded-2xl bg-[var(--kertas-terang)] text-[var(--tinta)] p-4 sm:p-5 border border-[var(--tinta)]/8"
                >
                    <div class="flex items-start gap-2.5">
                        <span class="w-8 h-8 rounded-full bg-[#E8F1EB] text-[var(--mentega)] inline-flex items-center justify-center shrink-0 mt-0.5" aria-hidden="true">
                            <svg class="w-4 h-4"><use href="#icon-map-pin"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="font-display font-bold text-base leading-tight text-[var(--tinta)]">{{ $brandName }}</p>
                            <p class="text-xs font-medium text-[var(--mentega-tua)] mt-0.5">{{ $operatingDays }}</p>
                        </div>
                    </div>

                    <p class="text-xs text-[var(--tinta-70)] leading-relaxed">
                        {{ $address }}
                        @if ($plusCode !== '')
                            <span class="block mt-1 text-[11px] font-semibold text-[var(--tinta-40)]">Plus Code: {{ $plusCode }}</span>
                        @endif
                    </p>

                    <div class="flex items-center gap-2 pt-0.5">
                        <a
                            href="{{ $mapsLink }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn-umco btn-umco-primary flex-1 min-h-0 h-10 px-4 text-xs sm:text-sm whitespace-nowrap"
                        >
                            <span>Buka Google Maps</span>
                            <svg class="w-3.5 h-3.5 shrink-0"><use href="#icon-arrow-right"/></svg>
                        </a>
                        <a
                            href="{{ url($ctaUrl) }}"
                            wire:navigate.hover
                            class="btn-umco min-h-0 h-10 px-4 bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-white text-xs sm:text-sm whitespace-nowrap"
                        >
                            <span>{{ $ctaLabel }}</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Kartu Informasi di HP (tersusun rapi di bawah peta agar peta tidak tertutup) --}}
            <div class="sm:hidden p-4 pt-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-display font-bold text-base text-[var(--tinta)]">{{ $brandName }}</p>
                        <p class="text-xs font-medium text-[var(--mentega-tua)]">{{ $operatingDays }}</p>
                    </div>
                    @if ($plusCode !== '')
                        <span class="rounded-full bg-[var(--batu)] px-2.5 py-1 text-[11px] font-semibold text-[var(--tinta-70)] shrink-0">{{ $plusCode }}</span>
                    @endif
                </div>
                <p class="text-xs text-[var(--tinta-70)] leading-relaxed">{{ $address }}</p>
                <div class="flex items-center gap-2">
                    <a
                        href="{{ $mapsLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-umco btn-umco-primary flex-1 min-h-0 h-10 px-4 text-xs"
                    >
                        <span>Buka Google Maps</span>
                        <svg class="w-3.5 h-3.5 shrink-0"><use href="#icon-arrow-right"/></svg>
                    </a>
                    <a
                        href="{{ url($ctaUrl) }}"
                        wire:navigate.hover
                        class="btn-umco min-h-0 h-10 px-4 bg-[var(--batu)] text-[var(--tinta)] text-xs"
                    >
                        <span>{{ $ctaLabel }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
