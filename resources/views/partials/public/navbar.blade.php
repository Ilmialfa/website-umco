<header
    id="umco-navbar"
    data-page="{{ $page }}"
    data-initial-theme="{{ $initialNavTheme }}"
    data-theme="{{ $initialNavTheme }}"
    data-scrolled="0"
    data-hidden="0"
    class="fixed top-0 inset-x-0 z-50"
>
    {{-- Desktop & Tablet (>=640px): Kiri (Menu, Galeri, Cerita) | Tengah (UM.CO) | Kanan (Hubungi Kami, Reservasi) --}}
    <div class="nav-bar hidden sm:block">
        <div class="umco-container">
            <div class="grid grid-cols-3 items-center h-16 lg:h-[72px]">
                <nav class="flex items-center gap-5 lg:gap-7 text-[14px] lg:text-[15px] font-medium justify-self-start" aria-label="Navigasi utama">
                    @foreach ($navItems as $item)
                        <a
                            href="{{ $item['href'] }}"
                            @if (! str_contains($item['href'], '#')) wire:navigate @endif
                            class="nav-link-line whitespace-nowrap"
                            @if ($item['active']) aria-current="page" @endif
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <a
                    href="{{ url('/') }}"
                    wire:navigate
                    aria-label="UM.CO House — Beranda"
                    class="justify-self-center inline-flex items-center gap-1.5 lg:gap-2 font-display font-bold text-[24px] lg:text-[28px] tracking-[0.06em] leading-none group"
                >
                    <span>UM</span>
                    <img
                        src="{{ asset('/images/umco-logo.webp') }}"
                        alt=""
                        aria-hidden="true"
                        width="24"
                        height="36"
                        decoding="async"
                        class="h-8 lg:h-9 w-auto object-contain select-none transition-transform duration-300 group-hover:scale-105"
                    >
                    <span>CO</span>
                </a>

                <div class="justify-self-end flex items-center gap-5 lg:gap-6">
                    <a
                        href="{{ $collabWaLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="nav-link-line text-[14px] lg:text-[15px] font-medium whitespace-nowrap"
                    >
                        Hubungi Kami
                    </a>
                    <a
                        href="{{ url('/reservasi') }}"
                        wire:navigate
                        class="nav-reservasi-pill btn-umco min-h-0 h-9 lg:h-10 px-5 text-[13px] lg:text-[14px] whitespace-nowrap"
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

    {{-- Mobile (<640px): Bar ringkas modern + tirai menu editorial minimalis --}}
    <div
        class="nav-bar sm:hidden"
        x-data="{ openNav: false }"
        x-effect="
            const hdr = document.getElementById('umco-navbar');
            if (hdr) hdr.setAttribute('data-menu-open', openNav ? '1' : '0');
            document.documentElement.classList.toggle('overflow-hidden', openNav);
        "
        @keydown.escape.window="openNav = false"
        x-on:livewire:navigated.window="openNav = false"
    >
        <div class="grid grid-cols-3 items-center h-16 px-5">
            <button
                type="button"
                @click="openNav = !openNav"
                class="justify-self-start w-10 h-10 -ml-2 inline-flex flex-col items-center justify-center gap-[6px] rounded-full cursor-pointer active:scale-95 transition-transform duration-200"
                :aria-expanded="openNav.toString()"
                :aria-label="openNav ? 'Tutup menu navigasi' : 'Buka menu navigasi'"
            >
                <span
                    class="block w-5 h-[1.5px] rounded-full bg-current origin-center transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    :class="openNav ? 'translate-y-[3.75px] rotate-45' : ''"
                ></span>
                <span
                    class="block w-5 h-[1.5px] rounded-full bg-current origin-center transition-transform duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
                    :class="openNav ? '-translate-y-[3.75px] -rotate-45' : ''"
                ></span>
            </button>

            <a
                href="{{ url('/') }}"
                wire:navigate
                @click="openNav = false"
                aria-label="UM.CO House — Beranda"
                class="justify-self-center inline-flex items-center gap-1 font-display font-bold text-[22px] tracking-[0.06em] leading-none"
            >
                <span>UM</span>
                <img
                    src="{{ asset('/images/umco-logo.webp') }}"
                    alt=""
                    aria-hidden="true"
                    width="19"
                    height="28"
                    decoding="async"
                    class="h-7 w-auto object-contain select-none"
                >
                <span>CO</span>
            </a>

            <div class="justify-self-end">
                <a
                    href="{{ url('/reservasi') }}"
                    wire:navigate
                    @click="openNav = false"
                    class="nav-reservasi-pill inline-flex items-center justify-center h-9 px-4 rounded-full text-[13px] font-medium tracking-wide transition-colors duration-200"
                >
                    Reservasi
                </a>
            </div>
        </div>

        <div
            x-show="openNav"
            x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="h-[calc(100dvh-4rem)] bg-[var(--kertas)] text-[var(--tinta)] px-6 pt-6 pb-8 flex flex-col justify-between overflow-y-auto"
            style="display: none;"
        >
            <div>
                <p class="font-hand text-xl text-[var(--aksen)] mb-4">~ pilih tujuanmu</p>

                <nav class="divide-y divide-[var(--garis)]/70" aria-label="Navigasi seluler">
                    @foreach ($navItems as $idx => $item)
                        <a
                            href="{{ $item['href'] }}"
                            @click="openNav = false"
                            @if (! str_contains($item['href'], '#')) wire:navigate @endif
                            class="group flex items-baseline justify-between py-4"
                        >
                            <span class="flex items-baseline gap-4">
                                <span class="text-[11px] font-medium tabular-nums text-[var(--tinta-60)]">0{{ $idx + 1 }}</span>
                                <span class="font-display font-bold text-[38px] leading-none tracking-tight {{ $item['active'] ? 'text-[var(--aksen)]' : 'text-[var(--tinta)]' }}">
                                    {{ $item['label'] }}
                                </span>
                            </span>
                            <span class="text-sm text-[var(--tinta-60)] transition-transform duration-200 group-hover:translate-x-1">
                                &rarr;
                            </span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="pt-6 space-y-3">
                <a
                    href="{{ url('/reservasi') }}"
                    wire:navigate
                    @click="openNav = false"
                    class="w-full h-12 rounded-full bg-[var(--tinta)] text-[var(--kertas)] text-[15px] font-semibold inline-flex items-center justify-center active:scale-[0.99] transition-transform"
                >
                    Pesan meja sekarang
                </a>
                <a
                    href="{{ $collabWaLink }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    @click="openNav = false"
                    class="w-full h-11 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] text-sm font-medium inline-flex items-center justify-center gap-2"
                >
                    <span>Hubungi Kami (WhatsApp)</span>
                    <span class="text-xs text-[var(--tinta-60)]">&rarr;</span>
                </a>
                <p class="pt-2 text-center text-[11px] tracking-wide text-[var(--tinta-60)]">
                    {{ $brandShort }} &middot; {{ $operatingDays }}
                </p>
            </div>
        </div>
    </div>
</header>
