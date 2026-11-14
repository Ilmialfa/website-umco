@php
    $media = $section?->media ?? [];
    $desktopImg = $media['desktop'] ?? '/images/hero-banner-full.jpg';
    $mobileImg = $media['mobile'] ?? '/images/hero-photo.jpg';
    $videoSrc = $media['video'] ?? '';
    $heading = $section?->heading ?: 'Kopi hangat, meja lapang di tepi kampus.';
    $subheading = $section?->subheading ?: 'Kampus 2 UMRI, Pekanbaru. Buka tiap hari jam 08.00 sampai 22.00.';
    $ctaLabel = $section?->cta_label ?: 'Pesan meja';
    $ctaUrl = $section?->cta_url ?: '/reservasi';

    $desktopWebp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $desktopImg);
    $desktopAvif = preg_replace('/\.(jpe?g|png)$/i', '.avif', $desktopImg);
    $mobileWebp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $mobileImg);
    $mobileAvif = preg_replace('/\.(jpe?g|png)$/i', '.avif', $mobileImg);
    $hasDesktopAvif = $desktopAvif !== $desktopImg && is_file(public_path(ltrim($desktopAvif, '/')));
    $hasDesktopWebp = $desktopWebp !== $desktopImg && is_file(public_path(ltrim($desktopWebp, '/')));
    $hasMobileAvif = $mobileAvif !== $mobileImg && is_file(public_path(ltrim($mobileAvif, '/')));
    $hasMobileWebp = $mobileWebp !== $mobileImg && is_file(public_path(ltrim($mobileWebp, '/')));
@endphp

<section
    id="hero"
    data-nav-theme="dark"
    data-module="hero"
    class="relative bg-[var(--kertas)] sm:bg-[var(--tinta)] text-[var(--tinta)] sm:text-[var(--kertas)] overflow-x-clip"
>
    {{-- Area foto: 64svh di HP (tanpa teks menumpuk), full-bleed di tablet/desktop --}}
    <div class="relative h-[64svh] sm:h-auto sm:min-h-[84svh] lg:min-h-[100svh] w-full bg-[var(--tinta)] overflow-hidden flex flex-col justify-end">
        <div data-hero-media class="absolute inset-0 will-change-transform">
            <picture class="block w-full h-full">
                @if ($hasMobileAvif)
                    <source media="(max-width: 639px)" srcset="{{ asset($mobileAvif) }}" type="image/avif">
                @endif
                @if ($hasMobileWebp)
                    <source media="(max-width: 639px)" srcset="{{ asset($mobileWebp) }}" type="image/webp">
                @endif
                <source media="(max-width: 639px)" srcset="{{ asset($mobileImg) }}">
                @if ($hasDesktopAvif)
                    <source srcset="{{ asset($desktopAvif) }}" type="image/avif">
                @endif
                @if ($hasDesktopWebp)
                    <source srcset="{{ asset($desktopWebp) }}" type="image/webp">
                @endif
                <img
                    src="{{ asset($desktopImg) }}"
                    alt="Suasana kedai kopi UMCO di Kampus UMRI Pekanbaru"
                    fetchpriority="high"
                    decoding="async"
                    class="w-full h-full object-cover object-center opacity-75"
                >
            </picture>
            @if ($videoSrc)
                <div
                    data-hero-video-host
                    data-video-src="{{ asset($videoSrc) }}"
                    data-poster="{{ asset($desktopImg) }}"
                    class="hidden lg:block absolute inset-0 pointer-events-none"
                ></div>
            @endif
            <div class="hidden sm:block absolute inset-0 bg-gradient-to-t from-[var(--tinta)]/85 via-[var(--tinta)]/25 to-transparent"></div>
            <div class="sm:hidden absolute inset-0 bg-gradient-to-t from-[var(--tinta)]/45 via-transparent to-[var(--tinta)]/30"></div>
        </div>

        {{-- HP: wordmark UMCO separuh atas menempel di batas bawah foto (warna kertas) --}}
        <div class="sm:hidden relative z-10 px-5 pointer-events-none select-none overflow-hidden h-9">
            <span class="block font-display font-extrabold text-[19vw] leading-[0.82] tracking-[-0.04em] text-[var(--kertas)]">
                UMCO
            </span>
        </div>

        {{-- Tablet & Desktop: konten penuh di bawah foto --}}
        <div class="hidden sm:block relative z-10 w-full pb-12 lg:pb-16 pt-32">
            <div class="umco-container">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                    <div class="lg:col-span-7 space-y-5">
                        <h1 data-reveal="lines" class="type-h1 font-display font-extrabold text-[var(--kertas)] max-w-2xl">
                            {{ $heading }}
                        </h1>
                        <div
                            data-hero-wordmark
                            data-reveal="chars-wdth"
                            aria-hidden="true"
                            class="font-display font-extrabold text-[13vw] lg:text-[9.5rem] leading-[0.84] tracking-[-0.04em] text-[var(--kertas)] select-none"
                        >
                            UMCO
                        </div>
                    </div>

                    <div class="lg:col-span-5 lg:pr-52 xl:pr-56 space-y-5 lg:pb-3">
                        <p data-reveal="fade" class="type-body text-[var(--kertas)]/85">
                            {{ $subheading }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ url($ctaUrl) }}" wire:navigate class="btn-umco btn-umco-primary">
                                <span class="btn-roll">
                                    <span>{{ $ctaLabel }}</span>
                                    <span aria-hidden="true">{{ $ctaLabel }}</span>
                                </span>
                            </a>
                            <a href="{{ url('/menu') }}" wire:navigate class="btn-umco border border-[var(--kertas)]/45 text-[var(--kertas)] hover:bg-[var(--kertas)] hover:text-[var(--tinta)]">
                                Lihat menu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Desktop kanan bawah: karcis kecil miring mengintip (bebas dari kolom CTA) --}}
        <a
            href="#karcis-teaser"
            aria-label="Coba isi karcis reservasi di halaman utama"
            class="hidden lg:block absolute -bottom-16 right-6 xl:right-10 z-20 w-44 xl:w-48 rotate-[5deg] hover:rotate-[1deg] transition-transform duration-200 focus:outline-none"
        >
            <x-umco.karcis code="No. ——" title="Karcis meja" compact>
                <x-umco.karcis-line label="Hari" value="Hari ini" />
                <x-umco.karcis-line label="Orang" value="2 orang" />
            </x-umco.karcis>
        </a>
    </div>

    {{-- HP (<640px): separuh bawah wordmark + judul & tombol di atas --kertas (tanpa menumpuk foto) --}}
    <div class="sm:hidden bg-[var(--kertas)] text-[var(--tinta)] pb-12">
        <div class="px-5 overflow-hidden select-none pointer-events-none h-9">
            <span class="block -translate-y-9 font-display font-extrabold text-[19vw] leading-[0.82] tracking-[-0.04em] text-[var(--tinta)]">
                UMCO
            </span>
        </div>

        <div class="px-5 pt-5 space-y-4">
            <h1 class="type-h1 font-display font-extrabold text-[var(--tinta)]">
                {{ $heading }}
            </h1>
            <p class="type-body text-[var(--tinta-70)]">
                {{ $subheading }}
            </p>
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <a href="{{ url($ctaUrl) }}" wire:navigate class="btn-umco btn-umco-primary">
                    <span>{{ $ctaLabel }}</span>
                </a>
                <a href="{{ url('/menu') }}" wire:navigate class="btn-umco btn-umco-secondary">
                    Lihat menu
                </a>
            </div>
        </div>
    </div>
</section>
