@props([
    'item',
    'categorySlug' => null,
])

@php
    $slug = $categorySlug ?? ($item->category?->slug ?? '');
    $toneClass = match ($slug) {
        'kopi-hitam', 'kopi-susu' => 'bg-[var(--aksen-soft)]',
        'susu', 'teh', 'mocktail' => 'bg-[var(--batu)]',
        default => 'bg-[var(--kertas-terang)] rule-notebook border border-[var(--garis)]',
    };
@endphp

<div {{ $attributes->class(['relative w-full aspect-[4/5] rounded-2xl overflow-hidden p-5 flex flex-col justify-between select-none text-[var(--tinta)]', $toneClass]) }}>
    <p class="font-display font-semibold text-[clamp(1.35rem,2.5vw,1.95rem)] leading-[1.1] tracking-[-0.02em] break-words">
        {{ $item->name }}
    </p>
    <p class="text-xs text-[var(--tinta-70)] font-medium">
        foto menyusul
    </p>
</div>
