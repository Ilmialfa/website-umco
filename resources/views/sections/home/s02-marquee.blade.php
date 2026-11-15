@php
    $phrases = $section?->data['phrases'] ?? [];
    if (empty($phrases)) {
        $phrases = $favoriteMenuItems->pluck('name')->all();
    }
    if (empty($phrases)) {
        $phrases = ['Aren Coffee', 'V60', 'Bliss Butter Cream', 'Nasi Ayam Sambal Matah', 'Coconut Pandan Cold Brew', 'Mix Platter'];
    }
@endphp

<section
    id="marquee"
    data-nav-theme="dark"
    class="bg-[var(--tinta)] text-[var(--kertas)] py-5 sm:py-6 overflow-hidden select-none"
    aria-label="Sorotan menu UMCO"
>
    <div class="umco-marquee-track font-display font-medium text-[1.25rem] sm:text-[1.75rem] lg:text-[2.25rem] leading-none tracking-[-0.015em]">
        @for ($copy = 0; $copy < 2; $copy++)
            <div class="flex items-center gap-7 sm:gap-10 pr-7 sm:pr-10" @if ($copy === 1) aria-hidden="true" @endif>
                @foreach ($phrases as $idx => $phrase)
                    <span class="{{ $idx % 3 === 1 ? 'text-[var(--mentega)]' : 'text-[var(--kertas)]/90' }} whitespace-nowrap">{{ $phrase }}</span>
                    <span class="w-2 h-2 rounded-full bg-[var(--mentega)] shrink-0" aria-hidden="true"></span>
                @endforeach
            </div>
        @endfor
    </div>
</section>
