@props([
    'item',
    'sizes' => '(min-width: 1024px) 25vw, (min-width: 640px) 33vw, 50vw',
    'imgClass' => 'w-full h-full object-cover',
])

@php
    $fallbackUrl = $item->imageUrlFor(800, 'webp');
    $webpSrcset = $item->srcsetFor('webp');
    $avifSrcset = $item->srcsetFor('avif');
@endphp

@if ($item->is_placeholder || empty($fallbackUrl))
    <x-umco.menu-placeholder :item="$item" {{ $attributes }} />
@else
    <picture {{ $attributes->class(['block w-full aspect-[4/5] overflow-hidden bg-[var(--garis)]/30']) }}>
        @if ($avifSrcset)
            <source type="image/avif" srcset="{{ $avifSrcset }}" sizes="{{ $sizes }}">
        @endif
        @if ($webpSrcset)
            <source type="image/webp" srcset="{{ $webpSrcset }}" sizes="{{ $sizes }}">
        @endif
        <img
            src="{{ $fallbackUrl }}"
            alt="{{ $item->name }}"
            loading="lazy"
            decoding="async"
            width="800"
            height="1000"
            class="{{ $imgClass }}"
        >
    </picture>
@endif
