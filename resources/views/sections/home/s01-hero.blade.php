@php
    $cutoutSetting = \App\Support\Settings::get('brand.hero_cutout_image', '');
    $cutoutSrc = $cutoutSetting ?: ($section?->media['cutout'] ?? '/images/hero-cutout.webp');
    $isDefaultHeroCutout = $cutoutSrc === '/images/hero-cutout.webp';
    if ($cutoutSrc && ! str_starts_with($cutoutSrc, 'http') && ! str_starts_with($cutoutSrc, '/')) {
        $cutoutSrc = '/storage/' . ltrim($cutoutSrc, '/');
    }
    $eyebrow = $section?->body ?: (\App\Support\Settings::get('brand.tagline') ?: 'Kampus 2 UMRI · Pekanbaru');
    $heading = $section?->heading ?: \App\Support\Settings::get('brand.hero_headline', 'Kopi hangat, meja lapang.');
    $subheading = $section?->subheading ?: \App\Support\Settings::get('brand.hero_description', 'Buka tiap hari 08.00 - 22.00 WIB di Kampus 2 UMRI Pekanbaru. Pesan meja dan menu langsung sebelum datang.');
    $handNote = $section?->data['hand_note'] ?? (trim((string) \App\Support\Settings::get('brand.today_note', '')) ?: '~ seduhan hangat & dingin, tinggal pilih meja dulu');
    $ctaLabel = $section?->cta_label ?: 'Pesan meja';
    $ctaUrl = $section?->cta_url ?: '/reservasi';
    $secondaryLabel = $section?->data['secondary_label'] ?? 'Lihat menu';
    $secondaryUrl = $section?->data['secondary_url'] ?? '/menu';
@endphp

<section
    id="hero"
    data-nav-theme="dark"
    data-nav-transparent="hero"
    data-nav-stops="0:#0D3526,0.44:#154B36,0.70:#2B6E51,0.86:#9BC4AD,1:#F6F4ED"
    data-module="hero"
    class="relative umco-hero-gradient text-white overflow-hidden pt-20 sm:pt-24 lg:pt-24 pb-8 sm:pb-12 lg:pb-14"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-6 sm:gap-6 lg:gap-8 min-h-[68svh] sm:min-h-[64svh] lg:min-h-[76svh]">
            {{-- Kiri: Eyebrow, Judul Condensed Tinggi, Deskripsi, Catatan Tangan, & Dual CTA --}}
            <div class="sm:col-span-5 lg:col-span-5 py-4 sm:py-6 space-y-5 sm:space-y-6 relative z-10">
                <div class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-white/90">
                    <span class="w-2 h-2 rounded-full bg-[var(--aksen-soft)]"></span>
                    <span>{{ $eyebrow }}</span>
                </div>

                <h1 class="font-display font-bold [font-size:clamp(2.4rem,4.1vw,3.85rem)] leading-[1.03] tracking-[-0.035em] text-white max-w-xl">
                    {{ $heading }}
                </h1>

                <p class="type-body sm:type-body-l text-white/90 max-w-md leading-relaxed">
                    {{ $subheading }}
                </p>

                <p class="font-hand text-2xl sm:text-[28px] leading-none text-[#E2F1E7]">
                    {{ $handNote }}
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a
                        href="{{ url($ctaUrl) }}"
                        wire:navigate
                        class="btn-umco bg-[#F6F4ED] text-[#141916] hover:bg-white"
                    >
                        <span class="btn-roll">
                            <span>{{ $ctaLabel }}</span>
                            <span aria-hidden="true">{{ $ctaLabel }}</span>
                        </span>
                    </a>
                    <a
                        href="{{ url($secondaryUrl) }}"
                        wire:navigate
                        class="btn-umco bg-white/15 text-white hover:bg-white hover:text-[#141916]"
                    >
                        {{ $secondaryLabel }}
                    </a>
                </div>
            </div>

            {{-- Kanan: Gambar Gedung UMCO Tanpa Latar + Kabut/Awan Lembut Penutup Potongan Bawah --}}
            <div class="relative sm:col-span-7 lg:col-span-7 flex justify-center sm:justify-end items-end self-end">
                <img
                    data-hero-cutout
                    src="{{ asset($cutoutSrc) }}"
                    @if ($isDefaultHeroCutout)
                        srcset="{{ asset('/images/hero-cutout-640.webp') }} 640w, {{ asset('/images/hero-cutout-960.webp') }} 960w, {{ asset('/images/hero-cutout.webp') }} 1200w"
                        sizes="(max-width: 640px) 92vw, 50vw"
                    @endif
                    alt="Gedung UMCO House di Kampus 2 UMRI Pekanbaru"
                    width="1200"
                    height="800"
                    fetchpriority="high"
                    decoding="async"
                    class="relative z-10 w-full max-w-[460px] sm:max-w-full h-auto object-contain object-bottom block select-none pointer-events-none [mask-image:linear-gradient(to_bottom,black_84%,transparent_100%)]"
                >

                {{-- Awan / kabut halus melapisi bagian bawah foto agar perpotongan menyatu --}}
                <div aria-hidden="true" class="pointer-events-none select-none absolute -bottom-10 inset-x-[-6%] z-20 h-24 sm:h-32 lg:h-40">
                    <div class="umco-hero-cloud-puff absolute left-[4%] bottom-3 w-[56%] h-20 sm:h-28 rounded-full"></div>
                    <div class="umco-hero-cloud-puff absolute right-[-2%] bottom-2 w-[58%] h-24 sm:h-32 rounded-full opacity-95"></div>
                    <div class="umco-hero-cloud-puff absolute left-[24%] -bottom-2 w-[54%] h-20 sm:h-28 rounded-full opacity-90"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kabut transisi bawah full-width menyambung ke warna kertas section berikutnya --}}
    <div aria-hidden="true" class="umco-hero-cloud-band pointer-events-none select-none absolute bottom-0 inset-x-0 z-20 h-24 sm:h-28 lg:h-36"></div>
</section>
