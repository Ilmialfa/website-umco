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
        ['label' => 'Biji', 'href' => url('/#biji'), 'active' => false],
        ['label' => 'Seduh', 'href' => url('/#seduh'), 'active' => false],
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

    <link rel="preload" href="/fonts/anybody-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/familjen-grotesk-latin.woff2" as="font" type="font/woff2" crossorigin>
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
        x-data="{ mobileMenuOpen: false }"
        @keydown.escape.window="mobileMenuOpen = false"
        class="fixed top-0 inset-x-0 z-50 pointer-events-none"
    >
        {{-- Desktop (>=1024px): 3 separated pills --}}
        <div class="hidden lg:block pt-4">
            <div class="umco-container flex items-center justify-between gap-4">
                <a
                    href="{{ url('/') }}"
                    wire:navigate
                    class="nav-pill pointer-events-auto inline-flex items-center px-5 h-11 rounded-full font-display font-bold text-lg tracking-tight"
                >
                    UMCO
                </a>

                <nav class="nav-pill pointer-events-auto inline-flex items-center gap-7 px-6 h-11 rounded-full text-[15px] font-medium" aria-label="Navigasi utama">
                    @foreach ($navItems as $item)
                        <a
                            href="{{ $item['href'] }}"
                            @if (!str_contains($item['href'], '#')) wire:navigate @endif
                            class="nav-link-line"
                            @if ($item['active']) aria-current="page" @endif
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <a
                    href="{{ url('/reservasi') }}"
                    wire:navigate
                    class="btn-umco btn-umco-primary pointer-events-auto h-11 min-h-0 px-5 text-[15px]"
                >
                    <span class="btn-roll">
                        <span>Pesan meja</span>
                        <span aria-hidden="true">Pesan meja</span>
                    </span>
                </a>
            </div>
        </div>

        {{-- Tablet (640px-1023px): single compact bar --}}
        <div class="hidden sm:block lg:hidden pt-3">
            <div class="umco-container">
                <div class="nav-pill pointer-events-auto flex items-center justify-between h-12 px-5 rounded-full">
                    <a href="{{ url('/') }}" wire:navigate class="font-display font-bold text-lg tracking-tight">
                        UMCO
                    </a>

                    <nav class="flex items-center gap-6 text-[15px] font-medium" aria-label="Navigasi tablet">
                        <a href="{{ url('/menu') }}" wire:navigate class="nav-link-line" @if (request()->is('menu*')) aria-current="page" @endif>Menu</a>
                        <a href="{{ url('/cerita') }}" wire:navigate class="nav-link-line" @if (request()->is('cerita*')) aria-current="page" @endif>Cerita</a>
                    </nav>

                    <div class="flex items-center gap-3">
                        <a href="{{ url('/reservasi') }}" wire:navigate class="btn-umco btn-umco-primary min-h-0 h-9 px-4 text-sm">
                            Pesan meja
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile (<640px): thin 56px top bar with pure text trigger --}}
        <div class="sm:hidden">
            <div class="nav-pill pointer-events-auto flex items-center justify-between h-14 px-5">
                <a href="{{ url('/') }}" wire:navigate class="font-display font-bold text-lg tracking-tight">
                    UMCO
                </a>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ url('/reservasi') }}"
                        wire:navigate
                        class="inline-flex items-center justify-center h-9 px-3.5 rounded-full bg-[var(--mentega)] text-[var(--tinta)] text-sm font-semibold"
                    >
                        Pesan
                    </a>
                    <button
                        type="button"
                        @click="mobileMenuOpen = true"
                        class="inline-flex items-center justify-center h-9 px-2 text-sm font-semibold underline underline-offset-4"
                        aria-label="Buka menu navigasi"
                    >
                        Menu
                    </button>
                </div>
            </div>
        </div>

        {{-- Full-screen Mobile Drawer (--mentega solid, zero shadow/blur) --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            style="display: none;"
            class="sm:hidden fixed inset-0 z-[60] bg-[var(--mentega)] text-[var(--tinta)] pointer-events-auto flex flex-col justify-between px-6 py-5 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-label="Menu UMCO"
        >
            <div class="flex items-center justify-between h-10">
                <a href="{{ url('/') }}" wire:navigate @click="mobileMenuOpen = false" class="font-display font-bold text-lg">
                    UMCO
                </a>
                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="inline-flex items-center gap-1.5 h-10 px-2 text-sm font-semibold underline underline-offset-4"
                >
                    Tutup
                </button>
            </div>

            <nav class="my-auto py-8 flex flex-col gap-3 font-display font-bold" aria-label="Tautan utama">
                <a href="{{ url('/') }}" wire:navigate @click="mobileMenuOpen = false" class="text-[2.6rem] leading-[0.95] tracking-tight py-1">
                    Beranda
                </a>
                <a href="{{ url('/menu') }}" wire:navigate @click="mobileMenuOpen = false" class="text-[2.6rem] leading-[0.95] tracking-tight py-1">
                    Daftar menu
                </a>
                <a href="{{ url('/cerita') }}" wire:navigate @click="mobileMenuOpen = false" class="text-[2.6rem] leading-[0.95] tracking-tight py-1">
                    Cerita kami
                </a>
                <a href="{{ url('/reservasi') }}" wire:navigate @click="mobileMenuOpen = false" class="text-[2.6rem] leading-[0.95] tracking-tight py-1 text-[var(--cabai)]">
                    Pesan meja
                </a>
            </nav>

            <div class="pt-5 border-t border-[var(--tinta)]/15 space-y-3 text-sm">
                <div class="inline-flex items-center gap-2 font-medium">
                    <span class="w-2 h-2 rounded-full bg-[var(--cabai)]"></span>
                    <span>Buka tiap hari, {{ $openTime }} - {{ $closeTime }} WIB</span>
                </div>
                <p class="text-[var(--tinta)]/80">{{ $address }}</p>
                <div class="flex flex-wrap gap-4 pt-1 font-semibold">
                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4">
                        Chat WhatsApp
                    </a>
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4">
                        Peta lokasi
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
                    <p class="font-display font-bold text-xl tracking-tight">UMCO</p>
                    <p class="text-sm sm:text-[15px] text-[var(--kertas)]/80 leading-relaxed max-w-sm">
                        {{ $address }}. Buka setiap hari pukul {{ $openTime }} - {{ $closeTime }} WIB.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-1 text-sm font-medium">
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4 hover:text-[var(--mentega)] transition-colors">
                            Buka di Google Maps
                        </a>
                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4 hover:text-[var(--mentega)] transition-colors">
                            WhatsApp kasir
                        </a>
                    </div>
                </div>

                <div class="sm:col-span-4 grid grid-cols-2 gap-6 text-sm">
                    <div class="space-y-2.5">
                        <p class="font-semibold text-[var(--kertas)]">Halaman</p>
                        <ul class="space-y-2 text-[var(--kertas)]/75">
                            <li><a href="{{ url('/') }}" wire:navigate class="hover:text-[var(--mentega)] transition-colors">Beranda</a></li>
                            <li><a href="{{ url('/menu') }}" wire:navigate class="hover:text-[var(--mentega)] transition-colors">Menu</a></li>
                            <li><a href="{{ url('/cerita') }}" wire:navigate class="hover:text-[var(--mentega)] transition-colors">Cerita kami</a></li>
                            <li><a href="{{ url('/reservasi') }}" wire:navigate class="hover:text-[var(--mentega)] transition-colors">Pesan meja</a></li>
                        </ul>
                    </div>
                    <div class="space-y-2.5">
                        <p class="font-semibold text-[var(--kertas)]">Kontak</p>
                        <ul class="space-y-2 text-[var(--kertas)]/75">
                            <li><a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-[var(--mentega)] transition-colors">Instagram</a></li>
                            <li><a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="hover:text-[var(--mentega)] transition-colors">WhatsApp</a></li>
                            <li><a href="{{ url('/admin') }}" class="hover:text-[var(--mentega)] transition-colors">Staf</a></li>
                        </ul>
                    </div>
                </div>

                <div class="sm:col-span-3 sm:text-right space-y-2 text-sm text-[var(--kertas)]/70">
                    <p>&copy; {{ date('Y') }} UMCO House</p>
                    <p>Kampus 2 UMRI, Pekanbaru</p>
                </div>
            </div>

            <div class="relative pt-10 sm:pt-14 flex flex-col items-center justify-end overflow-hidden select-none">
                <div
                    data-footer-wordmark
                    class="font-display font-extrabold text-[30vw] sm:text-[22vw] leading-[0.8] tracking-[-0.04em] text-[var(--kertas)] text-center whitespace-nowrap"
                >
                    UMCO
                </div>

                <img
                    data-footer-cutout
                    src="{{ asset('/images/footer-cutout.webp') }}"
                    alt="Segelas kopi susu dingin UMCO"
                    width="418"
                    height="540"
                    loading="lazy"
                    decoding="async"
                    class="relative z-10 -mt-[22vw] sm:-mt-[17vw] w-36 sm:w-48 lg:w-56 h-auto pointer-events-none"
                >
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
