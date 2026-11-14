@php
    $openTime = \App\Support\Settings::get('hours.opening_time', '08:00');
    $closeTime = \App\Support\Settings::get('hours.closing_time', '22:00');
    $todayNote = \App\Support\Settings::get('brand.today_note', 'Gayo baru turun dari mesin sangrai Selasa lalu. Cocok diseduh V60.');
    $soldOut = $soldOutCount ?? 0;
@endphp

<section
    id="hari-ini"
    data-nav-theme="light"
    data-module="led-board"
    data-open-time="{{ $openTime }}"
    data-close-time="{{ $closeTime }}"
    data-patterns='@json($crowdPatterns)'
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] border-b border-[var(--garis)]"
>
    <div class="umco-container space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                {{ $section?->heading ?: 'Yang lagi jalan hari ini' }}
            </h2>
            <p class="text-sm text-[var(--tinta-70)]">
                Jam buka {{ str_replace(':', '.', $openTime) }} - {{ str_replace(':', '.', $closeTime) }} WIB
            </p>
        </div>

        {{-- 4 sel papan: scroll-snap horizontal di HP, 2x2 di tablet, 4 kolom di desktop --}}
        <div class="flex sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-px bg-[var(--garis)] border border-[var(--garis)] overflow-x-auto snap-x snap-mandatory scrollbar-none">
            <div class="min-w-[78vw] sm:min-w-0 snap-start bg-[var(--kertas-terang)] p-6 sm:p-7 space-y-3">
                <p class="text-xs font-medium text-[var(--tinta-70)]">Status kedai</p>
                <p
                    data-led-status
                    data-open="1"
                    class="font-led text-4xl sm:text-5xl tracking-wider text-[var(--tinta)] data-[open='0']:text-[var(--cabai)]"
                >
                    BUKA
                </p>
            </div>

            <div class="min-w-[78vw] sm:min-w-0 snap-start bg-[var(--kertas-terang)] p-6 sm:p-7 space-y-3">
                <p class="text-xs font-medium text-[var(--tinta-70)]">Waktu di Pekanbaru</p>
                <p data-led-time class="font-led text-4xl sm:text-5xl tracking-wider text-[var(--tinta)] tabular-nums">
                    --.--
                </p>
            </div>

            <div class="min-w-[78vw] sm:min-w-0 snap-start bg-[var(--kertas-terang)] p-6 sm:p-7 space-y-3">
                <p class="text-xs font-medium text-[var(--tinta-70)]">Suasana jam ini</p>
                <p data-led-crowd class="font-led text-4xl sm:text-5xl tracking-wider text-[var(--tinta)]">
                    SEDANG
                </p>
            </div>

            <div class="min-w-[78vw] sm:min-w-0 snap-start bg-[var(--kertas-terang)] p-6 sm:p-7 space-y-3">
                <p class="text-xs font-medium text-[var(--tinta-70)]">Menu habis hari ini</p>
                <p class="font-led text-4xl sm:text-5xl tracking-wider tabular-nums {{ $soldOut > 0 ? 'text-[var(--cabai)]' : 'text-[var(--tinta)]' }}">
                    {{ $soldOut }}
                </p>
            </div>
        </div>

        <p data-reveal="fade" class="font-hand text-2xl sm:text-3xl text-[var(--cabai)]">
            {{ $todayNote }}
        </p>
    </div>
</section>
