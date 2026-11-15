@php
    $media = $section?->media ?? [];
    $desktopImg = $media['desktop'] ?? '/images/hero-banner-full.jpg';
    $mobileImg = $media['mobile'] ?? '/images/hero-photo.jpg';
    $videoSrc = ! empty($media['video']) ? $media['video'] : '/videos/hero-espresso-loop.mp4';
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
    {{-- Frame Visual Utama: video espresso desktop + foto responsif mobile/tablet --}}
    <div class="relative h-[58svh] sm:h-auto sm:min-h-[86svh] lg:min-h-[96svh] w-full bg-[var(--tinta)] overflow-hidden flex flex-col justify-end">
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
            <div class="hidden sm:block absolute inset-0 bg-gradient-to-t from-[var(--tinta)] via-[var(--tinta)]/35 to-[var(--tinta)]/25"></div>
            <div class="sm:hidden absolute inset-0 bg-gradient-to-t from-[var(--tinta)]/50 via-transparent to-[var(--tinta)]/30"></div>
        </div>

        {{-- Tablet & Desktop: Komposisi Arsitektural Bersih (tanpa stiker & tanpa huruf raksasa menumpuk) --}}
        <div class="hidden sm:block relative z-10 w-full pb-12 lg:pb-16 pt-36">
            <div class="umco-container">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-end">
                    <div class="lg:col-span-7 space-y-5">
                        <div class="inline-flex items-center gap-2.5 text-sm font-medium text-[var(--kertas)]/85">
                            <span class="w-2 h-2 rounded-full bg-[var(--mentega)]"></span>
                            <span>Kampus 2 UMRI, Pekanbaru</span>
                        </div>
                        <h1 data-reveal="lines" class="type-display font-display font-semibold text-[var(--kertas)] max-w-2xl">
                            {{ $heading }}
                        </h1>
                    </div>

                    <div class="lg:col-span-5 space-y-6 lg:pb-1">
                        <p data-reveal="fade" class="type-body-l text-[var(--kertas)]/85 leading-relaxed max-w-md">
                            {{ $subheading }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ url($ctaUrl) }}" wire:navigate class="btn-umco btn-umco-primary">
                                <span class="btn-roll">
                                    <span>{{ $ctaLabel }}</span>
                                    <span aria-hidden="true">{{ $ctaLabel }}</span>
                                </span>
                            </a>
                            <a href="{{ url('/menu') }}" wire:navigate class="btn-umco border border-[var(--kertas)]/35 text-[var(--kertas)] hover:bg-[var(--kertas)] hover:text-[var(--tinta)]">
                                Lihat menu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- HP (<640px): Teks bersih di atas Warm Alabaster tepat di bawah frame foto --}}
    <div class="sm:hidden bg-[var(--kertas)] text-[var(--tinta)] px-5 py-8 space-y-4">
        <div class="inline-flex items-center gap-2 text-xs font-medium text-[var(--tinta-70)]">
            <span class="w-2 h-2 rounded-full bg-[var(--mentega)]"></span>
            <span>Kampus 2 UMRI, Pekanbaru</span>
        </div>
        <h1 class="type-h1 font-display font-semibold text-[var(--tinta)]">
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
</section>
