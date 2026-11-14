@php
    $dayNames = [
        1 => 'Sen',
        2 => 'Sel',
        3 => 'Rab',
        4 => 'Kam',
        5 => 'Jum',
        6 => 'Sab',
        7 => 'Min',
    ];
@endphp

<section
    id="jam-ramai"
    data-nav-theme="light"
    data-module="crowd-chart"
    data-patterns='@json($crowdPatterns)'
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] border-b border-[var(--garis)]"
>
    <div class="umco-container space-y-8">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div class="space-y-2 max-w-xl">
                <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                    {{ $section?->heading ?: 'Kapan sepi, kapan penuh' }}
                </h2>
                <p class="type-body text-[var(--tinta-70)]">
                    {{ $section?->subheading ?: 'Klik batang jam mana saja untuk langsung memilih waktu datang.' }}
                </p>
            </div>

            <a
                data-crowd-cta
                href="{{ url('/reservasi') }}"
                wire:navigate
                class="inline-flex items-center px-4 h-10 rounded-full bg-[var(--mentega)] text-[var(--tinta)] text-sm font-semibold self-start lg:self-auto"
            >
                Pesan meja sesuai jam →
            </a>
        </div>

        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none" role="group" aria-label="Pilih hari">
            @foreach ($dayNames as $iso => $short)
                <button
                    type="button"
                    data-crowd-day="{{ $iso }}"
                    aria-pressed="false"
                    class="px-4 h-10 rounded-full border border-[var(--garis)] text-sm font-medium whitespace-nowrap cursor-pointer"
                >
                    {{ $short }}
                </button>
            @endforeach
        </div>

        <div data-crowd-bars class="pt-6 sm:pt-10 flex flex-col sm:flex-row sm:items-end gap-1.5 sm:gap-2"></div>

        <div class="flex flex-wrap items-center gap-6 text-xs text-[var(--tinta-70)] pt-2">
            <span class="inline-flex items-center gap-2">
                <span class="w-3 h-3 rounded-sm bg-[var(--tinta)]"></span>
                Perkiraan tingkat pengunjung
            </span>
            <span class="inline-flex items-center gap-2">
                <span class="w-3 h-3 rounded-sm bg-[var(--mentega)]"></span>
                Jam sekarang
            </span>
        </div>
    </div>
</section>
