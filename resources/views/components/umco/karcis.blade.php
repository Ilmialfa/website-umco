@props([
    'code' => 'No. ......',
    'title' => 'Karcis meja',
    'compact' => false,
    'size' => 'md',
    'stamp' => null,
    'stampColor' => 'text-[var(--mentega-tua)] border-[var(--mentega-tua)]',
    'torn' => false,
])

@php
    $isLg = $size === 'lg';
    $padX = $compact ? 'px-5 sm:px-6' : ($isLg ? 'px-7 sm:px-9' : 'px-6 sm:px-7');
    $padY = $isLg ? 'py-7 sm:py-8' : '';
@endphp

<div {{ $attributes->class(['karcis-serrated text-[var(--tinta)] relative select-none -rotate-1', $padX, $padY]) }}>
    <div class="flex items-center justify-between gap-3">
        <span class="font-display font-bold tracking-[0.08em] {{ $isLg ? 'text-base' : 'text-sm' }}">UMCO</span>
        <span class="font-hand {{ $isLg ? 'text-3xl' : 'text-2xl' }} text-[var(--cabai)] tabular-nums">{{ $code }}</span>
    </div>

    <div class="mt-2.5 flex items-baseline justify-between gap-3">
        <p class="font-display font-bold tracking-tight {{ $compact ? 'text-2xl' : ($isLg ? 'text-4xl sm:text-[2.6rem]' : 'text-3xl sm:text-[2.1rem]') }} leading-none">
            {{ $title }}
        </p>
        @if ($stamp)
            <span
                data-reveal="stamp"
                class="inline-block px-2.5 py-1 rounded-full bg-white/60 font-display font-semibold text-[11px] tracking-wider -rotate-3 {{ $stampColor }}"
            >
                {{ $stamp }}
            </span>
        @endif
    </div>

    <div class="{{ $isLg ? 'mt-7 space-y-3.5 text-[15px] sm:text-base' : 'mt-5 space-y-2.5 text-sm' }}">
        {{ $slot }}
    </div>

    @if (isset($stub))
        <div class="karcis-stub {{ $torn ? 'is-torn' : '' }} mt-5 pt-4 border-t border-dashed border-[var(--tinta)]/20">
            {{ $stub }}
        </div>
    @else
        <p class="{{ $isLg ? 'mt-7 pt-4 text-xs sm:text-sm' : 'mt-5 pt-3 text-xs' }} border-t border-dashed border-[var(--tinta)]/20 text-[var(--tinta-70)] leading-relaxed">
            Tunjukkan di kasir. Karcis hangus 15 menit setelah jam pesan.
        </p>
    @endif
</div>
