@props([
    'title' => null,
    'description' => null,
    'page' => 'default',
    'initialNavTheme' => 'light',
])

@php
    $brandName = \App\Support\Settings::get('brand.brand_name', 'UMCO');
    $metaTitle = $title ?? \App\Support\Settings::get('seo.meta_title', 'UMCO — Kedai Kopi di Kampus UMRI Pekanbaru');
    $metaDescription = $description ?? \App\Support\Settings::get('seo.meta_description', 'Kedai kopi di Kampus 2 UMRI Pekanbaru. Tempat nongkrong, nugas, dan pesan meja dengan menu siap saji.');
    $rawWa = \App\Support\Settings::get('contact.phone_wa', '02248001571');
    $waDigits = method_exists(\App\Support\PhoneNumber::class, 'normalizeBusiness')
        ? \App\Support\PhoneNumber::normalizeBusiness($rawWa)
        : preg_replace('/\D+/', '', (string) $rawWa);
    if ($waDigits && str_starts_with($waDigits, '0')) {
        $waDigits = '62' . substr($waDigits, 1);
    }
    $waLink = $waDigits ? 'https://wa.me/' . $waDigits : '#';
    $mapsUrl = \App\Support\Settings::get('contact.google_maps_url', 'https://maps.google.com/?q=UMRI+Pekanbaru');
    $instagramUrl = \App\Support\Settings::get('contact.instagram_url', 'https://instagram.com/umco.house');
    $openTime = \App\Support\Settings::get('hours.opening_time', '08.00');
    $closeTime = \App\Support\Settings::get('hours.closing_time', '21.00');
    $address = \App\Support\Settings::get('contact.address', 'Kampus 2 UMRI, Jl. Tuanku Tambusai Ujung, Delima, Pekanbaru');
    $smoothScroll = \App\Support\Settings::get('motion.smooth_scroll', false) ? '1' : '0';

    $navItems = [
        ['label' => 'Menu', 'href' => url('/menu'), 'active' => request()->is('menu*')],
        ['label' => 'Cerita', 'href' => url('/cerita'), 'active' => request()->is('cerita*')],
    ];
@endphp

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ asset('/images/hero-photo.jpg') }}">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="preload" href="/fonts/plus-jakarta-sans-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/instrument-sans-latin.woff2" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-style: normal;
            font-weight: 200 800;
            font-display: swap;
            src: url('/fonts/plus-jakarta-sans-latin.woff2') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
        @font-face {
            font-family: 'Instrument Sans';
            font-style: normal;
            font-weight: 400 700;
            font-display: swap;
            src: url('/fonts/instrument-sans-latin.woff2') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
    </style>
    <link rel="stylesheet" href="/fonts/fonts-accent.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="/fonts/fonts-accent.css"></noscript>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "CafeOrCoffeeShop",
        "name": "{{ $brandName }}",
        "image": "{{ asset('/images/hero-photo.jpg') }}",
        "url": "{{ url('/') }}",
        "telephone": "{{ $rawWa }}",
        "priceRange": "Rp 18.000 - Rp 36.000",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "Kampus 2 UMRI, Jl. Tuanku Tambusai Ujung, Delima, Binawidya",
            "addressLocality": "Pekanbaru",
            "addressRegion": "Riau",
            "postalCode": "28291",
            "addressCountry": "ID"
        },
        "openingHoursSpecification": [
            {
                "@@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                "opens": "{{ str_replace('.', ':', $openTime) }}",
                "closes": "{{ str_replace('.', ':', $closeTime) }}"
            }
        ]
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body
    data-page="{{ $page }}"
    data-smooth-scroll="{{ $smoothScroll }}"
    class="min-h-full flex flex-col bg-[var(--kertas)] text-[var(--tinta)] font-sans antialiased overflow-x-hidden"
>
    @include('partials.icons-sprite')

    <div
        id="umco-nav-skeleton"
        class="hidden fixed inset-0 z-[70] bg-[var(--kertas)] pt-24 px-5 sm:px-8 pointer-events-none"
        aria-hidden="true"
    >
        <div class="umco-container space-y-6">
            <div class="skeleton-block h-10 w-56 sm:w-80"></div>
            <div class="space-y-3 max-w-xl">
                <div class="skeleton-block h-5 w-full"></div>
                <div class="skeleton-block h-5 w-3/4"></div>
            </div>
            <div class="skeleton-block h-56 sm:h-72 w-full"></div>
        </div>
    </div>

    <header
        id="umco-navbar"
        data-page="{{ $page }}"
        data-initial-theme="{{ $initialNavTheme }}"
        data-theme="{{ $initialNavTheme }}"
        data-scrolled="0"
        data-hidden="0"
        class="fixed top-0 inset-x-0 z-50"
    >
        {{-- Desktop & Tablet (>=640px): Clean full-width 3-zone header — Left (Menu, Cerita) | Center (UMCO) | Right (Reservasi) --}}
        <div class="nav-bar hidden sm:block">
            <div class="umco-container">
                <div class="grid grid-cols-3 items-center h-16 lg:h-[72px]">
                    <nav class="flex items-center gap-6 lg:gap-8 text-[14px] lg:text-[15px] font-medium justify-self-start" aria-label="Navigasi utama">
                        @foreach ($navItems as $item)
                            <a
                                href="{{ $item['href'] }}"
                                wire:navigate
                                class="nav-link-line"
                                @if ($item['active']) aria-current="page" @endif
                            >
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <a
                        href="{{ url('/') }}"
                        wire:navigate
                        class="justify-self-center font-display font-semibold text-[18px] lg:text-[20px] tracking-[0.12em]"
                    >
                        UMCO
                    </a>

                    <div class="justify-self-end">
                        <a
                            href="{{ url('/reservasi') }}"
                            wire:navigate
                            class="btn-umco btn-umco-primary min-h-0 h-9 lg:h-10 px-5 text-[13px] lg:text-[14px]"
                        >
                            <span class="btn-roll">
                                <span>Reservasi</span>
                                <span aria-hidden="true">Reservasi</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile (<640px): Compact 3-zone header — Left (Menu, Cerita) | Center (UMCO) | Right (Reservasi) --}}
        <div class="nav-bar sm:hidden">
            <div class="grid grid-cols-3 items-center h-14 px-4">
                <nav class="flex items-center gap-3.5 text-[13px] font-medium justify-self-start" aria-label="Navigasi utama">
                    <a
                        href="{{ url('/menu') }}"
                        wire:navigate
                        class="nav-link-line"
                        @if (request()->is('menu*')) aria-current="page" @endif
                    >
                        Menu
                    </a>
                    <a
                        href="{{ url('/cerita') }}"
                        wire:navigate
                        class="nav-link-line"
                        @if (request()->is('cerita*')) aria-current="page" @endif
                    >
                        Cerita
                    </a>
                </nav>

                <a href="{{ url('/') }}" wire:navigate class="justify-self-center font-display font-semibold text-base tracking-[0.12em]">
                    UMCO
                </a>

                <div class="justify-self-end">
                    <a
                        href="{{ url('/reservasi') }}"
                        wire:navigate
                        class="inline-flex items-center justify-center h-8 px-3.5 rounded-full bg-[var(--mentega)] text-white text-xs font-semibold tracking-wide"
                    >
                        Reservasi
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <div
        data-module="scroll-meter"
        class="hidden lg:flex fixed bottom-5 right-6 z-40 items-center px-3.5 h-9 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--garis-kuwat)] text-xs font-medium tabular-nums select-none pointer-events-none"
        aria-hidden="true"
    >
        0 cm
    </div>

    <footer id="public-footer" class="relative bg-[var(--tinta)] text-[var(--kertas)] pt-14 sm:pt-20 pb-0 overflow-hidden">
        <div class="umco-container">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-8 sm:gap-10 pb-12 border-b border-white/15">
                <div class="sm:col-span-5 space-y-3">
                    <p class="font-display font-bold text-xl tracking-[0.06em]">UMCO</p>
                    <p class="text-sm sm:text-[15px] text-[var(--kertas)]/80 leading-relaxed max-w-sm">
                        {{ $address }}. Buka setiap hari pukul {{ $openTime }} - {{ $closeTime }} WIB.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-1 text-sm font-medium">
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4 hover:text-[#9FD3B6] transition-colors">
                            Buka di Google Maps
                        </a>
                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4 hover:text-[#9FD3B6] transition-colors">
                            WhatsApp kasir
                        </a>
                    </div>
                </div>

                <div class="sm:col-span-4 grid grid-cols-2 gap-6 text-sm">
                    <div class="space-y-2.5">
                        <p class="font-semibold text-[var(--kertas)]">Halaman</p>
                        <ul class="space-y-2 text-[var(--kertas)]/75">
                            <li><a href="{{ url('/') }}" wire:navigate class="hover:text-[#9FD3B6] transition-colors">Beranda</a></li>
                            <li><a href="{{ url('/menu') }}" wire:navigate class="hover:text-[#9FD3B6] transition-colors">Menu</a></li>
                            <li><a href="{{ url('/cerita') }}" wire:navigate class="hover:text-[#9FD3B6] transition-colors">Cerita kami</a></li>
                            <li><a href="{{ url('/reservasi') }}" wire:navigate class="hover:text-[#9FD3B6] transition-colors">Pesan meja</a></li>
                        </ul>
                    </div>
                    <div class="space-y-2.5">
                        <p class="font-semibold text-[var(--kertas)]">Kontak</p>
                        <ul class="space-y-2 text-[var(--kertas)]/75">
                            <li><a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-[#9FD3B6] transition-colors">Instagram</a></li>
                            <li><a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="hover:text-[#9FD3B6] transition-colors">WhatsApp</a></li>
                            <li><a href="{{ url('/admin') }}" class="hover:text-[#9FD3B6] transition-colors">Staf</a></li>
                        </ul>
                    </div>
                </div>

                <div class="sm:col-span-3 sm:text-right space-y-2 text-sm text-[var(--kertas)]/70">
                    <p>&copy; {{ date('Y') }} UMCO House</p>
                    <p>Kampus 2 UMRI, Pekanbaru</p>
                </div>
            </div>

            <div class="relative pt-10 sm:pt-14 pb-2 flex flex-col items-center justify-end overflow-hidden select-none">
                <div
                    data-footer-wordmark
                    class="w-full font-display font-semibold text-[22vw] sm:text-[18vw] leading-[0.84] tracking-[-0.015em] text-[var(--kertas)] text-center whitespace-nowrap will-change-transform"
                >
                    UMCO
                </div>

                <img
                    data-footer-cutout
                    src="{{ asset('/images/footer-cutout.webp') }}"
                    alt="Segelas kopi susu dingin UMCO"
                    width="295"
                    height="629"
                    loading="lazy"
                    decoding="async"
                    class="relative z-10 -mt-[20vw] sm:-mt-[16vw] w-28 sm:w-40 lg:w-44 h-auto pointer-events-none"
                >
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
