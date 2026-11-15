@php
    $bodyText = $section?->body ?: 'UMCO itu tempat kopi Nusantara, nasi ayam sambal matah, dan [img:1] meja panjang buat kamu yang betah lama. Datang sendiri atau [img:2] ramean, kursinya cukup.';
    $img1 = $section?->media['inline_1'] ?? '/images/bistro-patio-table.jpg';
    $img2 = $section?->media['inline_2'] ?? '/images/drinks-trio-banner.jpg';
    $featurePhoto = $section?->media['desktop'] ?? '/images/bistro-patio-table.webp';

    $tokens = preg_split('/\s+/', trim($bodyText)) ?: [];
@endphp

<section
    id="story-line"
    data-nav-theme="light"
    data-module="story-line"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)]"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <p class="type-h1 font-display font-medium leading-[1.18] tracking-[-0.025em]">
                    @foreach ($tokens as $tok)
                        @if ($tok === '[img:1]')
                            <span data-story-word class="inline-block align-middle mx-1 w-14 sm:w-20 h-9 sm:h-11 rounded-full overflow-hidden">
                                <img src="{{ asset($img1) }}" alt="Meja panjang UMCO" loading="lazy" class="w-full h-full object-cover">
                            </span>
                        @elseif ($tok === '[img:2]')
                            <span data-story-word class="inline-block align-middle mx-1 w-14 sm:w-20 h-9 sm:h-11 rounded-full overflow-hidden">
                                <img src="{{ asset($img2) }}" alt="Segelas minuman UMCO" loading="lazy" class="w-full h-full object-cover">
                            </span>
                        @else
                            <span data-story-word class="inline-block mr-[0.22em]">{{ $tok }}</span>
                        @endif
                    @endforeach
                </p>

                <div>
                    <a href="{{ url($section?->cta_url ?: '/cerita') }}" wire:navigate class="btn-umco btn-umco-secondary">
                        <span>{{ $section?->cta_label ?: 'Baca ceritanya' }}</span>
                        <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5 grid grid-cols-2 gap-3 sm:gap-4">
                <div class="aspect-[4/5] rounded-2xl overflow-hidden bg-[var(--batu)]">
                    <img src="{{ asset($featurePhoto) }}" alt="Suasana meja UMCO" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>
                <div class="aspect-[4/5] rounded-2xl overflow-hidden bg-[var(--batu)] mt-6 sm:mt-10">
                    <img src="{{ asset($img2) }}" alt="Tiga sajian minuman dingin UMCO" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>
