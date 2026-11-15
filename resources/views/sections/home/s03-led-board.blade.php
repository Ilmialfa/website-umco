@php
    $openTime = \App\Support\Settings::get('hours.opening_time', '08:00');
    $closeTime = \App\Support\Settings::get('hours.closing_time', '22:00');
    $todayNote = \App\Support\Settings::get('brand.today_note', 'Gayo baru turun dari mesin sangrai Selasa lalu. Cocok diseduh V60.');
    $soldOut = $soldOutCount ?? 0;
    $nowWib = now('Asia/Jakarta');
    $initialClock = $nowWib->format('H.i');
    $featureImg = $section?->media['image'] ?? '/images/hero-iced-coffee-splash.webp';
@endphp

<section
    id="hari-ini"
    data-nav-theme="light"
    data-module="led-board"
    data-open-time="{{ $openTime }}"
    data-close-time="{{ $closeTime }}"
    data-patterns='@json($crowdPatterns)'
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)]"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            {{-- Kiri: Ringkasan Suasana & Status Kedai Arsitektural (Tanpa Kartu Boxy) --}}
            <div class="lg:col-span-7 space-y-8">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold tracking-normal text-[var(--mentega)]">
                        <span class="w-2 h-2 rounded-full bg-[var(--mentega)]"></span>
                        <span>Kondisi Kedai Hari Ini</span>
                    </div>
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                        {{ $section?->heading ?: 'Yang lagi jalan hari ini' }}
                    </h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        Buka setiap hari pukul {{ str_replace(':', '.', $openTime) }} - {{ str_replace(':', '.', $closeTime) }} WIB di Kampus 2 UMRI.
                    </p>
                </div>

                <div data-reveal-stagger class="grid grid-cols-2 border-y border-[var(--garis)]">
                    <div data-reveal="rise" class="py-5 sm:py-6 pr-4 border-b border-r border-[var(--garis)] space-y-1.5">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Status kedai</p>
                        <p
                            data-led-status
                            data-open="1"
                            class="font-display font-semibold text-2xl sm:text-4xl tracking-tight text-[var(--mentega)] data-[open='0']:text-[var(--tinta-70)]"
                        >
                            BUKA
                        </p>
                    </div>

                    <div data-reveal="rise" class="py-5 sm:py-6 pl-5 sm:pl-8 border-b border-[var(--garis)] space-y-1.5">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Waktu di Pekanbaru</p>
                        <p data-led-time class="font-display font-semibold text-2xl sm:text-4xl tracking-tight text-[var(--tinta)] tabular-nums">
                            {{ $initialClock }}
                        </p>
                    </div>

                    <div data-reveal="rise" class="py-5 sm:py-6 pr-4 border-r border-[var(--garis)] space-y-1.5">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Suasana jam ini</p>
                        <p data-led-crowd class="font-display font-semibold text-2xl sm:text-4xl tracking-tight text-[var(--tinta)]">
                            SEDANG
                        </p>
                    </div>

                    <div data-reveal="rise" class="py-5 sm:py-6 pl-5 sm:pl-8 space-y-1.5">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Menu habis hari ini</p>
                        <p class="font-display font-semibold text-2xl sm:text-4xl tracking-tight tabular-nums {{ $soldOut > 0 ? 'text-[var(--mentega)]' : 'text-[var(--tinta)]' }}">
                            {{ $soldOut }}
                        </p>
                    </div>
                </div>

                <div data-reveal="fade" class="border-l-2 border-[var(--mentega)] pl-4 py-1">
                    <p class="type-body text-[var(--tinta)] leading-relaxed">
                        {{ $todayNote }}
                    </p>
                </div>
            </div>

            {{-- Kanan: Visual Barista & Sajian Hari Ini --}}
            <div class="lg:col-span-5" data-reveal="mask">
                <div class="relative aspect-[4/4] sm:aspect-[4/3] lg:aspect-[4/4.4] rounded-2xl overflow-hidden bg-[var(--batu)]">
                    <img
                        src="{{ asset($featureImg) }}"
                        alt="Minuman racikan segar bar UMCO hari ini"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 bg-gradient-to-t from-[#171C19]/80 via-[#171C19]/20 to-transparent text-white">
                        <p class="text-sm font-medium text-white/95">Bar Seduh UMCO · Kampus 2 UMRI</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
