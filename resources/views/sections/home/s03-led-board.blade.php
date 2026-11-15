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
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-stretch">
            {{-- Kiri: Ringkasan Suasana & Status Kedai Real-time --}}
            <div class="lg:col-span-7 flex flex-col justify-between space-y-8">
                <div class="space-y-3">
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                        {{ $section?->heading ?: 'Yang lagi jalan hari ini' }}
                    </h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        Buka setiap hari pukul {{ str_replace(':', '.', $openTime) }} - {{ str_replace(':', '.', $closeTime) }} WIB.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <div class="rounded-2xl bg-[var(--batu)] p-5 sm:p-6 space-y-2">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Status kedai</p>
                        <p
                            data-led-status
                            data-open="1"
                            class="font-display font-semibold text-2xl sm:text-4xl tracking-tight text-[var(--tinta)] data-[open='0']:text-[var(--mentega-tua)]"
                        >
                            BUKA
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--batu)] p-5 sm:p-6 space-y-2">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Waktu di Pekanbaru</p>
                        <p data-led-time class="font-display font-semibold text-2xl sm:text-4xl tracking-tight text-[var(--tinta)] tabular-nums">
                            {{ $initialClock }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--batu)] p-5 sm:p-6 space-y-2">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Suasana jam ini</p>
                        <p data-led-crowd class="font-display font-semibold text-2xl sm:text-4xl tracking-tight text-[var(--tinta)]">
                            SEDANG
                        </p>
                    </div>

                    <div class="rounded-2xl bg-[var(--batu)] p-5 sm:p-6 space-y-2">
                        <p class="text-xs font-medium text-[var(--tinta-70)]">Menu habis hari ini</p>
                        <p class="font-display font-semibold text-2xl sm:text-4xl tracking-tight tabular-nums {{ $soldOut > 0 ? 'text-[var(--mentega-tua)]' : 'text-[var(--tinta)]' }}">
                            {{ $soldOut }}
                        </p>
                    </div>
                </div>

                <div class="rounded-2xl bg-[var(--kertas)] px-5 py-4 flex items-start gap-3">
                    <span class="w-2 h-2 rounded-full bg-[var(--mentega)] mt-2 shrink-0"></span>
                    <p data-reveal="fade" class="type-body text-[var(--tinta)] leading-relaxed">
                        {{ $todayNote }}
                    </p>
                </div>
            </div>

            {{-- Kanan: Visual Barista & Sajian Hari Ini --}}
            <div class="lg:col-span-5">
                <div class="relative h-64 sm:h-80 lg:h-full lg:min-h-[420px] rounded-2xl overflow-hidden bg-[var(--batu)]">
                    <img
                        src="{{ asset($featureImg) }}"
                        alt="Minuman racikan segar bar UMCO hari ini"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6 bg-gradient-to-t from-[var(--tinta)]/80 via-[var(--tinta)]/25 to-transparent text-[var(--kertas)]">
                        <p class="text-sm font-medium text-[var(--kertas)]/90">Bar Seduh UMCO · Kampus 2 UMRI</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
