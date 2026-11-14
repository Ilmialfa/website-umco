<x-layouts.public
    page="home"
    initial-nav-theme="dark"
    title="UMCO — Kedai Kopi di Kampus UMRI Pekanbaru"
>
    <section
        data-nav-theme="dark"
        class="relative min-h-[82vh] sm:min-h-[88vh] flex items-end bg-[var(--tinta)] text-[var(--kertas)] pt-28 pb-14 sm:pb-20 overflow-hidden"
    >
        <picture class="absolute inset-0">
            <img
                src="{{ asset('/images/hero-photo.jpg') }}"
                alt="Suasana kedai kopi UMCO di Kampus UMRI"
                fetchpriority="high"
                decoding="async"
                class="w-full h-full object-cover object-center opacity-55"
            >
        </picture>
        <div class="absolute inset-0 bg-[var(--tinta)]/45"></div>

        <div class="umco-container relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                <div class="lg:col-span-8 space-y-5">
                    <h1 data-reveal="rise" class="type-display font-display font-extrabold tracking-tight text-[var(--kertas)]">
                        Kopi hangat, meja lapang di tepi kampus.
                    </h1>
                    <p data-reveal="fade" class="type-body text-[var(--kertas)]/85 max-w-xl">
                        Kedai kami di Kampus 2 UMRI buka tiap hari dari pagi sampai malam. Datang buat nugas, diskusi, atau sekadar rehat sebentar.
                    </p>
                    <div data-reveal="fade" class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="{{ url('/reservasi') }}" wire:navigate class="btn-umco btn-umco-primary">
                            <span class="btn-roll">
                                <span>Pesan meja</span>
                                <span aria-hidden="true">Pesan meja</span>
                            </span>
                        </a>
                        <a href="{{ url('/menu') }}" wire:navigate class="btn-umco border border-[var(--kertas)]/40 text-[var(--kertas)] hover:bg-[var(--kertas)] hover:text-[var(--tinta)]">
                            Lihat daftar menu
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 lg:text-right space-y-1 text-sm text-[var(--kertas)]/80">
                    <p class="font-hand text-2xl text-[var(--mentega)]">Buka tiap hari, 08.00 - 21.00 WIB</p>
                    <p>Kampus 2 UMRI, Pekanbaru</p>
                </div>
            </div>
        </div>
    </section>

    <section data-nav-theme="light" class="umco-section bg-[var(--kertas)] text-[var(--tinta)] edge-torn-top">
        <div class="umco-container space-y-10">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-[var(--garis)] pb-6">
                <div class="space-y-2">
                    <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                        Pilihan yang paling sering dipesan
                    </h2>
                    <p class="type-body text-[var(--tinta-muted)]">
                        Pilih langsung di karcis atau lihat seluruh katalog kami.
                    </p>
                </div>
                <a href="{{ url('/menu') }}" wire:navigate class="inline-flex items-center gap-2 font-semibold underline underline-offset-4">
                    <span>Buka semua menu</span>
                    <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                </a>
            </div>

            <div class="divide-y divide-[var(--garis)]" data-reveal-stagger>
                @forelse ($favoriteMenuItems as $item)
                    <a
                        href="{{ url('/reservasi') }}"
                        wire:navigate
                        class="menu-row-trigger flex items-baseline justify-between gap-4 py-4 hover:text-[var(--cabai)] transition-colors"
                    >
                        <div class="space-y-1">
                            <span class="menu-row-name text-2xl sm:text-3xl text-[var(--tinta)]">{{ $item->name }}</span>
                            @if ($item->description)
                                <p class="text-sm text-[var(--tinta-muted)] max-w-lg">{{ $item->description }}</p>
                            @endif
                        </div>
                        <span class="font-sans font-semibold text-base sm:text-lg tabular-nums whitespace-nowrap">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </span>
                    </a>
                @empty
                    <p class="py-6 text-[var(--tinta-muted)]">Daftar menu sedang disiapkan.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.public>
