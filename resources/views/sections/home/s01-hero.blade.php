@php
    $cutoutSetting = \App\Support\Settings::get('brand.hero_cutout_image', '');
    $cutoutSrc = $cutoutSetting ?: ($section?->media['cutout'] ?? '/images/hero-cutout.webp');
    if ($cutoutSrc && ! str_starts_with($cutoutSrc, 'http') && ! str_starts_with($cutoutSrc, '/')) {
        $cutoutSrc = '/storage/' . ltrim($cutoutSrc, '/');
    }
    $eyebrow = $section?->body ?: 'Kampus 2 UMRI · Pekanbaru';
    $heading = $section?->heading ?: \App\Support\Settings::get('brand.hero_headline', 'Kopi hangat, meja lapang.');
    $subheading = $section?->subheading ?: \App\Support\Settings::get('brand.hero_description', 'Buka tiap hari 08.00 - 22.00 WIB di Kampus 2 UMRI Pekanbaru. Pesan meja dan menu langsung sebelum datang.');
    $ctaLabel = $section?->cta_label ?: 'Pesan meja';
    $ctaUrl = $section?->cta_url ?: '/reservasi';
@endphp

<section
    id="hero"
    data-nav-theme="dark"
    data-module="hero"
    class="relative umco-hero-gradient text-white overflow-hidden pt-20 sm:pt-24 lg:pt-24 pb-8 sm:pb-12 lg:pb-14"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-8 lg:gap-10 min-h-[70svh] sm:min-h-[64svh] lg:min-h-[76svh]">
            {{-- Kiri: Eyebrow, Judul Condensed Tinggi, Deskripsi, Catatan Tangan, & Dual CTA --}}
            <div class="sm:col-span-6 lg:col-span-6 py-4 sm:py-6 space-y-5 sm:space-y-6 relative z-10">
                <div data-reveal="rise" class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-white/90">
                    <span class="w-2 h-2 rounded-full bg-[var(--aksen-soft)]"></span>
                    <span>{{ $eyebrow }}</span>
                </div>

                <h1 data-reveal="lines" class="font-display font-bold [font-size:clamp(3.4rem,6.2vw,6.25rem)] leading-[0.90] tracking-[-0.025em] text-white max-w-2xl">
                    {{ $heading }}
                </h1>

                <p data-reveal="fade" class="type-body sm:type-body-l text-white/90 max-w-md leading-relaxed">
                    {{ $subheading }}
                </p>

                <p data-reveal="fade" class="font-hand text-2xl sm:text-[28px] leading-none text-[#E2F1E7]">
                    ~ seduhan hangat & dingin, tinggal pilih meja dulu
                </p>

                <div data-reveal="rise" class="flex flex-wrap items-center gap-3 pt-1">
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
                        href="{{ url('/menu') }}"
                        wire:navigate
                        class="btn-umco bg-white/15 text-white hover:bg-white hover:text-[#141916]"
                    >
                        Lihat menu
                    </a>
                </div>
            </div>

            {{-- Kanan: Gambar Cutout Tanpa Latar + Kabut/Awan Lembut Penutup Potongan Bawah --}}
            <div data-reveal="mask" class="relative sm:col-span-6 lg:col-span-6 flex justify-center sm:justify-end items-end self-end">
                <img
                    data-hero-cutout
                    src="{{ asset($cutoutSrc) }}"
                    alt="Suasana ramah UMCO Kampus 2 UMRI bersama segelas es kopi"
                    width="660"
                    height="920"
                    fetchpriority="high"
                    decoding="async"
                    class="relative z-10 w-[74vw] max-w-[300px] sm:w-full sm:max-w-[360px] lg:max-w-[490px] xl:max-w-[530px] h-auto object-contain object-bottom block select-none pointer-events-none [mask-image:linear-gradient(to_bottom,black_82%,transparent_100%)]"
                >

                {{-- Awan / kabut halus melapisi bagian bawah foto agar perpotongan menyatu --}}
                <div aria-hidden="true" class="pointer-events-none select-none absolute -bottom-12 inset-x-[-18%] z-20 h-28 sm:h-36 lg:h-44">
                    <div class="umco-hero-cloud-puff absolute left-[4%] bottom-4 w-[58%] h-24 sm:h-32 rounded-full"></div>
                    <div class="umco-hero-cloud-puff absolute right-[-4%] bottom-2 w-[62%] h-28 sm:h-36 rounded-full opacity-95"></div>
                    <div class="umco-hero-cloud-puff absolute left-[24%] -bottom-2 w-[56%] h-24 sm:h-32 rounded-full opacity-90"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kabut transisi bawah full-width menyambung ke warna kertas section berikutnya --}}
    <div aria-hidden="true" class="umco-hero-cloud-band pointer-events-none select-none absolute bottom-0 inset-x-0 z-20 h-24 sm:h-28 lg:h-36"></div>
</section>
