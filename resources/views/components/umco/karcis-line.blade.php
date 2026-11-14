@props([
    'label',
    'value' => null,
    'bind' => null,
])

<div {{ $attributes->class(['flex items-baseline gap-2 border-b border-dotted border-[var(--tinta)]/35 pb-1']) }}>
    <span class="w-16 shrink-0 text-xs font-medium text-[var(--tinta)]/75">{{ $label }}</span>
    <span
        class="font-hand text-xl sm:text-2xl leading-none text-[var(--tinta)] truncate flex-1"
        @if ($bind) x-text="{{ $bind }}" @endif
    >{{ $value }}</span>
</div>
