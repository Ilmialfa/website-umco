@props([
    'code' => 'No. ......',
    'title' => 'Karcis meja',
    'compact' => false,
    'stamp' => null,
    'stampColor' => 'text-[var(--cabai)] border-[var(--cabai)]',
    'torn' => false,
])

<div {{ $attributes->class(['karcis-serrated bg-[var(--blush)] text-[var(--tinta)] relative select-none', $compact ? 'p-5' : 'p-6 sm:p-7']) }}>
    <div class="flex items-center justify-between gap-3">
        <span class="font-display font-extrabold tracking-tight text-base">UMCO</span>
        <span class="font-hand text-xl text-[var(--cabai)] tabular-nums">{{ $code }}</span>
    </div>

    <div class="mt-2 flex items-baseline justify-between gap-3">
        <h3 class="font-display font-extrabold tracking-tight {{ $compact ? 'text-2xl' : 'text-3xl sm:text-[2rem]' }} leading-none">
            {{ $title }}
        </h3>
        @if ($stamp)
            <span
                data-reveal="stamp"
                class="inline-block px-2.5 py-1 border-2 font-display font-bold text-[11px] tracking-wider -rotate-6 {{ $stampColor }}"
            >
                {{ $stamp }}
            </span>
        @endif
    </div>

    <div class="mt-5 space-y-2.5 text-sm">
        {{ $slot }}
    </div>

    @if (isset($stub))
        <div class="karcis-stub {{ $torn ? 'is-torn' : '' }} mt-5 pt-4 border-t border-dashed border-[var(--tinta)]/30">
            {{ $stub }}
        </div>
    @else
        <p class="mt-5 pt-3 border-t border-dashed border-[var(--tinta)]/30 text-xs text-[var(--tinta)]/80 leading-relaxed">
            Tunjukkan di kasir. Karcis hangus 15 menit setelah jam pesan.
        </p>
    @endif
</div>
