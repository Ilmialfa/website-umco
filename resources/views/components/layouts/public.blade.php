<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? \App\Support\Settings::get('seo.meta_title', 'UM.CO House — Coffee Shop Kampus UMRI Pekanbaru') }}</title>
    <meta name="description" content="{{ $description ?? \App\Support\Settings::get('seo.meta_description', 'Coffee shop di dalam Kampus UMRI Pekanbaru. Gedung arsitektur huruf H: indoor AC tenang untuk nugas & kerja, outdoor adem dengan aneka street food malam hari.') }}">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? \App\Support\Settings::get('seo.meta_title', 'UM.CO House') }}">
    <meta property="og:description" content="{{ $description ?? \App\Support\Settings::get('seo.meta_description', '') }}">
    <meta property="og:image" content="{{ asset(\App\Support\Settings::get('seo.og_image', '/images/logo.svg')) }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? \App\Support\Settings::get('seo.meta_title', 'UM.CO House') }}">
    <meta name="twitter:description" content="{{ $description ?? \App\Support\Settings::get('seo.meta_description', '') }}">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- JSON-LD Local SEO Schema -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "CafeOrCoffeeShop",
        "name": "{{ \App\Support\Settings::get('brand.brand_name', 'UM.CO House') }}",
        "image": "{{ asset('/images/logo.svg') }}",
        "url": "{{ url('/') }}",
        "telephone": "{{ \App\Support\Settings::get('contact.phone_wa', '082248001571') }}",
        "priceRange": "{{ \App\Support\Settings::get('rating.price_range', 'Rp 25.000 – Rp 50.000') }}",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "KAMPUS UMRI, Jl. Tuanku Tambusai No.2, Delima, Binawidya",
            "addressLocality": "Pekanbaru",
            "addressRegion": "Riau",
            "postalCode": "28291",
            "addressCountry": "ID"
        },
        "geo": {
            "@@type": "GeoCoordinates",
            "latitude": 0.4999292,
            "longitude": 101.4155284
        },
        "openingHoursSpecification": [
            {
                "@@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                "opens": "{{ \App\Support\Settings::get('hours.opening_time', '08:00') }}",
                "closes": "{{ \App\Support\Settings::get('hours.closing_time', '21:00') }}"
            }
        ],
        "aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": "{{ \App\Support\Settings::get('rating.google_rating', '4.9') }}",
            "reviewCount": "86"
        },
        "sameAs": [
            "{{ \App\Support\Settings::get('contact.instagram_url', 'https://www.instagram.com/um.co_house') }}"
        ]
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-oat text-charcoal font-sans antialiased selection:bg-moss selection:text-alabaster min-h-full flex flex-col">

    <!-- FLOATING RESPONSIVE HEADER -->
    <header
        x-data="{
            scrolled: false,
            mobileOpen: false,
            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = (window.pageYOffset > 30);
                });
            }
        }"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
        :class="scrolled ? 'bg-forest/95 backdrop-blur-md py-3 shadow-md border-b border-white/10 text-alabaster' : 'bg-transparent py-5 text-alabaster'"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">

                <!-- Left Navigation (Desktop) & Hamburger (Mobile) -->
                <div class="flex items-center">
                    <!-- Mobile Hamburger Button (min 44px tap target) -->
                    <button
                        type="button"
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden inline-flex items-center justify-center w-11 h-11 rounded-full border border-white/20 hover:border-white/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-crema transition"
                        aria-label="Buka Menu Navigasi"
                    >
                        <svg x-show="!mobileOpen" class="w-5 h-5 text-alabaster" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" class="w-5 h-5 text-alabaster" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Desktop Nav Links (Left side) -->
                    <nav class="hidden md:flex items-center gap-8">
                        <a
                            href="/"
                            wire:navigate
                            class="text-sm font-medium tracking-wide transition hover:text-crema {{ request()->is('/') ? 'text-crema font-semibold' : 'text-alabaster/90' }}"
                        >
                            Beranda
                        </a>
                        <a
                            href="/menu"
                            wire:navigate
                            class="text-sm font-medium tracking-wide transition hover:text-crema {{ request()->is('menu*') ? 'text-crema font-semibold' : 'text-alabaster/90' }}"
                        >
                            Menu
                        </a>
                        <a
                            href="/about"
                            wire:navigate
                            class="text-sm font-medium tracking-wide transition hover:text-crema {{ request()->is('about*') ? 'text-crema font-semibold' : 'text-alabaster/90' }}"
                        >
                            Cerita &amp; Lokasi
                        </a>
                    </nav>
                </div>

                <!-- Center Logo (UM.CO) -->
                <div class="flex-shrink-0">
                    <a href="/" wire:navigate class="inline-flex items-center gap-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-crema rounded-lg py-1 px-2">
                        <span class="font-serif text-2xl lg:text-3xl font-bold tracking-wider text-alabaster">
                            UM.CO
                        </span>
                        <span class="w-2 h-2 rounded-full bg-crema"></span>
                    </a>
                </div>

                <!-- Right Actions: Primary (Reservasi) & Secondary (Hubungi Kami via WA) -->
                <div class="flex items-center gap-3">
                    @php
                        $waNumber = \App\Support\Settings::get('contact.phone_wa', '082248001571');
                        $waLink = \App\Support\PhoneNumber::waLink($waNumber, 'Halo UM.CO House, saya ingin bertanya tentang menu atau reservasi.');
                    @endphp

                    <a
                        href="{{ $waLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-full text-xs font-semibold tracking-wide border border-white/30 text-alabaster hover:bg-white/10 hover:border-white/50 transition duration-200"
                    >
                        Hubungi Kami
                    </a>

                    <a
                        href="/reservasi"
                        wire:navigate
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-full text-xs font-bold tracking-wide bg-crema text-forest hover:bg-crema-dark hover:text-alabaster transition duration-200 shadow-sm"
                    >
                        Reservasi Meja
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Overlay -->
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden fixed inset-0 top-[60px] bg-forest-deep/98 backdrop-blur-xl z-40 p-6 flex flex-col justify-between"
            style="display: none;"
        >
            <div class="space-y-6 pt-4">
                <p class="text-xs uppercase tracking-widest text-sage font-semibold">Navigasi Halaman</p>
                <div class="flex flex-col gap-4 text-xl font-serif">
                    <a
                        href="/"
                        wire:navigate
                        @click="mobileOpen = false"
                        class="py-2 border-b border-white/10 transition {{ request()->is('/') ? 'text-crema font-bold' : 'text-alabaster' }}"
                    >
                        Beranda
                    </a>
                    <a
                        href="/menu"
                        wire:navigate
                        @click="mobileOpen = false"
                        class="py-2 border-b border-white/10 transition {{ request()->is('menu*') ? 'text-crema font-bold' : 'text-alabaster' }}"
                    >
                        Katalog Menu
                    </a>
                    <a
                        href="/about"
                        wire:navigate
                        @click="mobileOpen = false"
                        class="py-2 border-b border-white/10 transition {{ request()->is('about*') ? 'text-crema font-bold' : 'text-alabaster' }}"
                    >
                        Cerita &amp; Denah H
                    </a>
                    <a
                        href="/reservasi"
                        wire:navigate
                        @click="mobileOpen = false"
                        class="py-2 border-b border-white/10 text-crema font-bold"
                    >
                        Reservasi Meja
                    </a>
                </div>
            </div>

            <div class="space-y-4 pt-6 border-t border-white/10">
                <a
                    href="{{ $waLink }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full inline-flex items-center justify-center py-3.5 rounded-full border border-white/30 text-alabaster font-semibold text-sm hover:bg-white/10 transition"
                >
                    Chat WhatsApp Admin
                </a>
                <p class="text-center text-xs text-sage">
                    Buka setiap hari sampai pukul {{ \App\Support\Settings::get('hours.closing_time', '21:00') }} WIB
                </p>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT SLOT -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- FOOTER (SECTION 4.3) -->
    <footer id="public-footer" class="bg-forest text-alabaster relative overflow-hidden pt-20 pb-8 border-t border-forest-deep">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Top Footer Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-16 border-b border-white/10">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center gap-1.5">
                        <span class="font-serif text-3xl font-bold tracking-wider text-alabaster">
                            {{ \App\Support\Settings::get('brand.brand_name', 'UM.CO House') }}
                        </span>
                        <span class="w-2.5 h-2.5 rounded-full bg-crema"></span>
                    </div>
                    <p class="text-sm text-sage leading-relaxed max-w-sm">
                        {{ \App\Support\Settings::get('brand.tagline', 'Ruang tenang untuk nugas, tempat ramai untuk bercerita.') }}
                    </p>
                    <div class="pt-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-crema">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span x-data="{
                                isOpen() {
                                    const now = new Date();
                                    // Asia/Jakarta offset is UTC+7
                                    const hours = now.getHours();
                                    return (hours >= 8 && hours < 21);
                                }
                            }" x-text="isOpen() ? 'Buka Sekarang • Tutup 21.00 WIB' : 'Tutup • Buka Besok 08.00 WIB'">
                                Buka Sekarang
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-widest text-crema font-bold">Navigasi</p>
                    <ul class="space-y-2.5 text-sm text-alabaster/80">
                        <li><a href="/" wire:navigate class="hover:text-crema transition">Beranda</a></li>
                        <li><a href="/menu" wire:navigate class="hover:text-crema transition">Katalog Menu</a></li>
                        <li><a href="/about" wire:navigate class="hover:text-crema transition">Cerita Arsitektur Gedung H</a></li>
                        <li><a href="/reservasi" wire:navigate class="hover:text-crema transition">Reservasi Meja &amp; Menu</a></li>
                        <li><a href="/specimen" class="hover:text-crema transition text-white/40 text-xs">Design Specimen</a></li>
                    </ul>
                </div>

                <!-- Col 3: Alamat & Jam Buka -->
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-widest text-crema font-bold">Lokasi Kampus</p>
                    <p class="text-sm text-alabaster/80 leading-relaxed">
                        {{ \App\Support\Settings::get('contact.address', 'KAMPUS UMRI, Jl. Tuanku Tambusai No.2, Delima, Binawidya, Kota Pekanbaru, Riau 28291') }}
                    </p>
                    <div class="pt-2">
                        <a
                            href="{{ \App\Support\Settings::get('contact.google_maps_url', '#') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs text-crema hover:underline underline-offset-4"
                        >
                            <span>Buka di Google Maps</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Col 4: Instagram & Kolaborasi -->
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-widest text-crema font-bold">Ikuti Kabar Promo</p>
                    <p class="text-sm text-alabaster/80 leading-relaxed">
                        Info kegiatan, menu baru, dan agenda komunitas kampus selalu diperbarui di Instagram kami.
                    </p>
                    <div class="pt-1 flex flex-col gap-2">
                        <a
                            href="{{ \App\Support\Settings::get('contact.instagram_url', 'https://www.instagram.com/um.co_house') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-crema hover:text-white transition"
                        >
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <span>{{ \App\Support\Settings::get('contact.instagram_handle', '@um.co_house') }}</span>
                        </a>
                        <a
                            href="{{ $waLink }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-alabaster/90 hover:text-white transition"
                        >
                            <span>WhatsApp: {{ \App\Support\PhoneNumber::format($waNumber) }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Staff Link -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-sage gap-4 relative z-20">
                <p>&copy; {{ date('Y') }} {{ \App\Support\Settings::get('brand.brand_name', 'UM.CO House') }} — Kampus UMRI Pekanbaru. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="/admin" class="hover:text-crema transition text-white/40 hover:text-white">Panel Staf &amp; Kasir</a>
                    <span>•</span>
                    <span>Zona Waktu Asia/Jakarta (WIB)</span>
                </div>
            </div>
        </div>

        <!-- DRAMATIC BOTTOM SECTION: GIANT WORDMARK + OVERLAPPING SIGNATURE DRINK (SECTION 4.3) -->
        <div class="relative w-full overflow-hidden mt-12 pt-8 select-none pointer-events-none">
            <!-- Giant Wordmark UM.CO -->
            <div class="w-full text-center leading-none">
                <span
                    class="font-serif font-black tracking-tighter text-white/[0.07] block uppercase"
                    style="font-size: clamp(6rem, 21vw, 24rem); mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 90%); -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 90%);"
                >
                    {{ \App\Support\Settings::get('brand.footer_wordmark', 'UM.CO') }}
                </span>
            </div>

            <!-- Overlapping Signature Drink (Center Front) -->
            <div class="absolute bottom-[-15px] sm:bottom-[-20px] left-1/2 -translate-x-1/2 w-48 sm:w-64 md:w-80 lg:w-96 z-10 footer-signature-drink pointer-events-auto">
                <img
                    src="{{ asset(\App\Support\Settings::get('brand.signature_drink_image', '/images/signature-drink.svg')) }}"
                    alt="Signature Drink UM.CO House"
                    class="w-full h-auto drop-shadow-2xl transition-transform duration-500 hover:scale-105"
                    loading="lazy"
                />
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
