@php
    $bodyText = $section?->body ?: 'UMCO itu tempat kopi Nusantara, nasi ayam sambal matah, dan [img:1] meja panjang buat kamu yang betah lama. Datang sendiri atau [img:2] ramean, kursinya cukup.';
    $img1 = $section?->media['inline_1'] ?? '/images/bistro-patio-table.jpg';
    $img2 = $section?->media['inline_2'] ?? '/images/drinks-trio-banner.jpg';

    // Pecah teks per token spasi sambil mempertahankan [img:1] & [img:2]
    $tokens = preg_split('/\s+/', trim($bodyText)) ?: [];
@endphp

<section
    id="story-line"
    data-nav-theme="light"
    data-module="story-line"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] border-t border-[var(--garis)]"
>
    <div class="umco-container space-y-8">
        <p class="type-h1 font-display font-bold leading-[1.1] tracking-[-0.03em] max-w-5xl">
            @foreach ($tokens as $tok)
                @if ($tok === '[img:1]')
                    <span data-story-word class="inline-block align-middle mx-1 w-14 sm:w-20 h-9 sm:h-12 rounded-full overflow-hidden">
                        <img src="{{ asset($img1) }}" alt="Meja panjang UMCO" loading="lazy" class="w-full h-full object-cover">
                    </span>
                @elseif ($tok === '[img:2]')
                    <span data-story-word class="inline-block align-middle mx-1 w-14 sm:w-20 h-9 sm:h-12 rounded-full overflow-hidden">
                        <img src="{{ asset($img2) }}" alt="Segelas minuman UMCO" loading="lazy" class="w-full h-full object-cover">
                    </span>
                @else
                    <span data-story-word class="inline-block mr-[0.22em]">{{ $tok }}</span>
                @endif
            @endforeach
        </p>

        <div>
            <a href="{{ url($section?->cta_url ?: '/cerita') }}" wire:navigate class="inline-flex items-center gap-2 text-base font-semibold underline underline-offset-4 hover:text-[var(--cabai)] transition-colors">
                <span>{{ $section?->cta_label ?: 'Baca ceritanya' }}</span>
                <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
            </a>
        </div>
    </div>
</section>
