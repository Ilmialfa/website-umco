@php
    $cutoutSetting = \App\Support\Settings::get('brand.hero_cutout_image', '');
    $cutoutSrc = $cutoutSetting ?: ($section?->media['cutout'] ?? '/images/hero-cutout.webp');
    if ($cutoutSrc && ! str_starts_with($cutoutSrc, 'http') && ! str_starts_with($cutoutSrc, '/')) {
        $cutoutSrc = '/storage/' . ltrim($cutoutSrc, '/');
    }
    $eyebrow = $section?->body ?: 'Kampus 2 UMRI · Pekanbaru';
    $heading = $section?->heading ?: \App\Support\Settings::get('brand.hero_headline', 'Kopi hangat, meja lapang di tepi kampus.');
    $subheading = $section?->subheading ?: \App\Support\Settings::get('brand.hero_description', 'Buka tiap hari 08.00 - 22.00 WIB di Kampus 2 UMRI Pekanbaru. Pesan meja dan menu langsung sebelum datang.');
    $ctaLabel = $section?->cta_label ?: 'Pesan meja';
    $ctaUrl = $section?->cta_url ?: '/reservasi';
@endphp

<section
    id="hero"
    data-nav-theme="dark"
    data-module="hero"
    class="relative umco-hero-gradient text-white overflow-hidden pt-20 sm:pt-28 lg:pt-24"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 sm:grid-cols-12 items-end gap-8 lg:gap-10 min-h-[78svh] sm:min-h-[68svh] lg:min-h-[84svh]">
            {{-- Kiri: Eyebrow, Judul Condensed Tinggi, Deskripsi, & Dual CTA --}}
            <div class="sm:col-span-6 lg:col-span-6 pb-4 sm:pb-14 lg:pb-20 pt-6 sm:pt-8 space-y-5 sm:space-y-6">
                <div class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-white/85">
                    <span class="w-2 h-2 rounded-full bg-[var(--aksen-soft)]"></span>
                    <span>{{ $eyebrow }}</span>
                </div>

                <h1 data-reveal="lines" class="type-display font-display font-bold text-white max-w-xl">
                    {{ $heading }}
                </h1>

                <p data-reveal="fade" class="type-body sm:type-body-l text-white/85 max-w-md leading-relaxed">
                    {{ $subheading }}
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a
                        href="{{ url($ctaUrl) }}"
                        wire:navigate
                        class="btn-umco bg-[#F6F4ED] text-[#141916] border border-[#F6F4ED] hover:bg-white"
                    >
                        <span class="btn-roll">
                            <span>{{ $ctaLabel }}</span>
                            <span aria-hidden="true">{{ $ctaLabel }}</span>
                        </span>
                    </a>
                    <a
                        href="{{ url('/menu') }}"
                        wire:navigate
                        class="btn-umco border border-white/40 text-white hover:bg-white hover:text-[#141916]"
                    >
                        Lihat menu
                    </a>
                </div>
            </div>

            {{-- Kanan: Gambar Cutout Tanpa Latar yang bisa diunggah admin dari Filament --}}
            <div class="sm:col-span-6 lg:col-span-6 flex justify-center sm:justify-end items-end self-end">
                <img
                    data-hero-cutout
                    src="{{ asset($cutoutSrc) }}"
                    alt="Suasana ramah UMCO Kampus 2 UMRI bersama segelas es kopi"
                    width="660"
                    height="920"
                    fetchpriority="high"
                    decoding="async"
                    class="w-[74vw] max-w-[300px] sm:w-full sm:max-w-[360px] lg:max-w-[490px] xl:max-w-[530px] h-auto object-contain object-bottom block select-none pointer-events-none"
                >
            </div>
        </div>
    </div>
</section>
