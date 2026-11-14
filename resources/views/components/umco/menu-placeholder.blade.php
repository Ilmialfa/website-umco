@props([
    'item',
    'categorySlug' => null,
])

@php
    $slug = $categorySlug ?? ($item->category?->slug ?? '');
    $toneClass = match ($slug) {
        'kopi-hitam', 'kopi-susu' => 'bg-[var(--mentega)]/40',
        'susu' => 'bg-[var(--blush)]',
        'teh', 'mocktail' => 'bg-[var(--zaitun)]',
        default => 'bg-[var(--kertas-terang)] rule-notebook border border-[var(--garis)]',
    };
@endphp

<div {{ $attributes->class(['relative w-full aspect-[4/5] overflow-hidden p-4 flex flex-col justify-between select-none text-[var(--tinta)]', $toneClass]) }}>
    <p class="font-display font-extrabold text-[clamp(1.75rem,3.4vw,2.6rem)] leading-[0.9] tracking-[-0.04em] break-words -ml-1">
        {{ $item->name }}
    </p>
    <p class="text-xs text-[var(--tinta)]/70 font-medium">
        foto menyusul
    </p>
</div>
