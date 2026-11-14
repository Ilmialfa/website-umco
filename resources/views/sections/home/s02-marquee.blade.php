@php
    $phrases = $section?->data['phrases'] ?? [];
    if (empty($phrases)) {
        $phrases = $favoriteMenuItems->pluck('name')->all();
    }
    if (empty($phrases)) {
        $phrases = ['Aren Coffee', 'V60', 'Bliss Butter Cream', 'Nasi Ayam Sambal Matah', 'Matcha', 'Mix Platter'];
    }
@endphp

<section
    id="marquee"
    data-nav-theme="dark"
    class="bg-[var(--tinta)] text-[var(--kertas)] edge-torn-top py-6 sm:py-8 overflow-hidden select-none"
    aria-label="Sorotan menu UMCO"
>
    <div class="umco-marquee-track font-display font-extrabold text-[1.5rem] sm:text-[2rem] lg:text-[3rem] leading-none tracking-[-0.03em]">
        @for ($copy = 0; $copy < 2; $copy++)
            <div class="flex items-center gap-8 sm:gap-12 pr-8 sm:pr-12" @if ($copy === 1) aria-hidden="true" @endif>
                @foreach ($phrases as $idx => $phrase)
                    <span class="{{ $idx === 1 ? 'text-[var(--mentega)]' : '' }} whitespace-nowrap">{{ $phrase }}</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[var(--mentega)] shrink-0" aria-hidden="true"></span>
                @endforeach
            </div>
        @endfor
    </div>
</section>
