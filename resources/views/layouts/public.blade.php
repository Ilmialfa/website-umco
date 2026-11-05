<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? \App\Support\Settings::get('seo.meta_title', 'UM.CO House — Coffee & Communal Space Kampus UMRI') }}</title>
    <meta name="description" content="{{ $description ?? \App\Support\Settings::get('seo.meta_description', 'Coffee shop & ruang santai di Kampus UMRI Pekanbaru. Suasana teduh indoor AC & outdoor street food malam hari.') }}">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'UM.CO House' }}">
    <meta property="og:description" content="{{ $description ?? 'Coffee shop di Kampus UMRI Pekanbaru.' }}">
    <meta property="og:image" content="{{ asset('/images/hero-photo.jpg') }}">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- JSON-LD Local SEO Schema -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "CafeOrCoffeeShop",
        "name": "{{ \App\Support\Settings::get('brand.brand_name', 'UM.CO House') }}",
        "image": "{{ asset('/images/hero-photo.jpg') }}",
        "url": "{{ url('/') }}",
        "telephone": "{{ \App\Support\Settings::get('contact.phone_wa', '082248001571') }}",
        "priceRange": "Rp 15.000 – Rp 35.000",
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
        }
    }
    </script>

    <!-- Google Fonts: Outfit (Spacious Geometric Sans, Zero Berdempetan) & Plus Jakarta Sans (Clean Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#FAF7EE] text-[#122018] font-sans antialiased selection:bg-[#F5B82E] selection:text-[#07241C] min-h-full flex flex-col">

    <!-- MINIMALIST RESPONSIVE NAVBAR: FLOATING PILL / TRANSPARENT OVER HERO (MELLOW STYLE) -->
    <header
        x-data="{
            scrolled: false,
            mobileOpen: false,
            init() {
                this.scrolled = window.pageYOffset > 30;
                window.addEventListener('scroll', () => {
                    this.scrolled = window.pageYOffset > 30;
                }, { passive: true });
            }
        }"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 pointer-events-none py-3 sm:py-4 px-4 sm:px-6"
    >
        <div
            class="max-w-5xl mx-auto transition-all duration-300 pointer-events-auto rounded-full px-5 sm:px-7 py-2.5 sm:py-3 text-white"
            :class="scrolled
                ? 'bg-[#07241C]/95 backdrop-blur-md shadow-none'
                : '{{ request()->is('/') ? 'bg-[#07241C]/80 backdrop-blur-md md:bg-transparent md:backdrop-blur-none' : 'bg-[#07241C]/95 backdrop-blur-md' }}'"
        >
            <div class="flex items-center justify-between">

                <!-- Left Navigation (Desktop) & Hamburger (Mobile) -->
                <div class="flex items-center">
                    <!-- Mobile Hamburger -->
                    <button
                        type="button"
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 transition text-white"
                        aria-label="Toggle Menu"
                    >
                        <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center gap-7 text-xs font-bold tracking-wider uppercase">
                        <a
                            href="/"
                            wire:navigate
                            class="transition-colors hover:text-[#F5B82E] {{ request()->is('/') ? 'text-[#F5B82E]' : 'text-white/90' }}"
                        >
                            Beranda
                        </a>
                        <a
                            href="/menu"
                            wire:navigate
                            class="transition-colors hover:text-[#F5B82E] {{ request()->is('menu*') ? 'text-[#F5B82E]' : 'text-white/90' }}"
                        >
                            Menu
                        </a>
                        <a
                            href="/about"
                            wire:navigate
                            class="transition-colors hover:text-[#F5B82E] {{ request()->is('about*') ? 'text-[#F5B82E]' : 'text-white/90' }}"
                        >
                            Cerita &amp; Lokasi
                        </a>
                    </nav>
                </div>

                <!-- Center Logo -->
                <div class="flex-shrink-0">
                    <a href="/" wire:navigate class="inline-flex items-center gap-1.5 focus:outline-none group">
                        <span class="font-display text-2xl sm:text-3xl font-black tracking-widest text-white group-hover:text-[#F5B82E] transition">
                            UM.CO
                        </span>
                        <span class="w-2 h-2 rounded-full bg-[#F5B82E]"></span>
                    </a>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-3">
                    @php
                        $waNumber = \App\Support\Settings::get('contact.phone_wa', '082248001571');
                        $waLink = \App\Support\PhoneNumber::waLink($waNumber, 'Halo UM.CO House, saya ingin bertanya seputar kafe dan reservasi meja.');
                    @endphp

                    <a
                        href="{{ $waLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-full text-xs font-bold tracking-wider uppercase text-white/90 hover:text-white hover:bg-white/10 transition"
                    >
                        WhatsApp
                    </a>

                    <a
                        href="/reservasi"
                        wire:navigate
                        class="inline-flex items-center justify-center px-5 py-2 rounded-full text-xs font-black tracking-wider uppercase bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition active:scale-95 shadow-none"
                    >
                        Reservasi Meja
                    </a>
                </div>
            </div>
        </div>
                        Reservasi Meja
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Overlay -->
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden fixed inset-x-4 top-20 bg-[#07241C]/95 backdrop-blur-xl rounded-3xl p-6 text-white pointer-events-auto"
            style="display: none;"
        >
            <div class="flex flex-col gap-4 font-display text-lg">
                <a href="/" wire:navigate @click="mobileOpen = false" class="py-2 border-b border-white/10 {{ request()->is('/') ? 'text-[#F5B82E] font-bold' : 'text-white' }}">
                    Beranda
                </a>
                <a href="/menu" wire:navigate @click="mobileOpen = false" class="py-2 border-b border-white/10 {{ request()->is('menu*') ? 'text-[#F5B82E] font-bold' : 'text-white' }}">
                    Katalog Menu
                </a>
                <a href="/about" wire:navigate @click="mobileOpen = false" class="py-2 border-b border-white/10 {{ request()->is('about*') ? 'text-[#F5B82E] font-bold' : 'text-white' }}">
                    Cerita Arsitektur &amp; Lokasi
                </a>
                <a href="/reservasi" wire:navigate @click="mobileOpen = false" class="py-2 text-[#F5B82E] font-bold">
                    Reservasi Meja
                </a>
            </div>

            <div class="mt-6 pt-4 border-t border-white/10">
                <a
                    href="{{ $waLink }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full inline-flex items-center justify-center py-3 rounded-full bg-[#F5B82E] text-[#07241C] font-bold text-xs"
                >
                    Chat WhatsApp Kasir
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT SLOT -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- FOOTER: CLEAN, SPACIOUS, MODERN (ZERO OVERLAPPING, ZERO COLLISION) -->
    <footer id="public-footer" class="bg-[#07241C] text-white relative overflow-hidden pt-16 sm:pt-20 pb-12">
        
        <!-- Subtle Ambient Background Wordmark (Background Layer Only, Never Colliding With Text) -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none overflow-hidden opacity-[0.03]">
            <span class="font-display font-black text-[22vw] tracking-[0.2em] text-white whitespace-nowrap">
                UM.CO
            </span>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Footer Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-white/10">
                <!-- Brand Info -->
                <div class="md:col-span-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="font-display text-3xl font-black tracking-wider text-white">UM.CO</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#F5B82E]"></span>
                    </div>
                    <p class="text-sm text-[#9AB4A6] leading-relaxed max-w-sm font-medium">
                        Ruang temu hangat di Kampus UMRI Pekanbaru. Dari seduhan kopi harian hingga street food malam hari di courtyard terbuka.
                    </p>
                    <div class="pt-1">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/5 text-xs text-[#F5B82E] font-medium border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Buka Setiap Hari: 08.00 – 21.00 WIB</span>
                        </span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="md:col-span-3 space-y-3">
                    <p class="text-xs uppercase tracking-widest text-[#F5B82E] font-bold">Navigasi</p>
                    <ul class="space-y-2.5 text-sm text-white/80 font-medium">
                        <li><a href="/" wire:navigate class="hover:text-[#F5B82E] transition">Beranda</a></li>
                        <li><a href="/menu" wire:navigate class="hover:text-[#F5B82E] transition">Katalog Menu</a></li>
                        <li><a href="/about" wire:navigate class="hover:text-[#F5B82E] transition">Cerita &amp; Lokasi Kampus</a></li>
                        <li><a href="/reservasi" wire:navigate class="hover:text-[#F5B82E] transition">Reservasi Meja</a></li>
                    </ul>
                </div>

                <!-- Location & Contact -->
                <div class="md:col-span-4 space-y-3">
                    <p class="text-xs uppercase tracking-widest text-[#F5B82E] font-bold">Lokasi Kampus</p>
                    <p class="text-sm text-white/80 leading-relaxed font-medium">
                        Kampus 2 UMRI, Jl. Tuanku Tambusai Ujung, Delima, Pekanbaru, Riau.
                    </p>
                    <div class="pt-2 flex items-center gap-3">
                        <a
                            href="{{ \App\Support\Settings::get('contact.google_maps_url', 'https://maps.google.com/?q=UMRI+Pekanbaru') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#F5B82E] hover:underline"
                        >
                            <span>Buka di Google Maps</span>
                            <span>&rarr;</span>
                        </a>
                        <span class="text-white/30">•</span>
                        <a href="/admin" class="text-xs text-white/40 hover:text-white transition">Panel Staf</a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright (Unobstructed, Clean, Spacious) -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#9AB4A6] gap-4 font-medium">
                <p>&copy; {{ date('Y') }} UM.CO House Kampus UMRI. All rights reserved.</p>
                <p>Designed for Campus Living &amp; Coffee Lovers</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
