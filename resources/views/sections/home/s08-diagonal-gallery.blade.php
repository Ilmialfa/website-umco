@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Galeri Bebas Rasio';
    $heading = $section?->heading ?: 'Momen dari berbagai sudut';
    $subheading = $section?->subheading ?: 'Tampilan bebas semua rasio foto dalam alur diagonal yang bergerak mengikuti guliranmu.';

    $items = ($diagonalGallery ?? collect())->toBase()->values();
    if ($items->count() < 6) {
        $fallback = collect([
            (object) ['image_path' => '/images/bistro-patio-table.jpg', 'caption' => 'Meja sore paling sering dipilih'],
            (object) ['image_path' => '/storage/menu/americano-800.webp', 'caption' => 'Americano dingin sebelum kelas'],
            (object) ['image_path' => '/images/drinks-trio-banner.jpg', 'caption' => 'Tiga gelas meja diskusi'],
            (object) ['image_path' => '/storage/menu/mix-platter-800.webp', 'caption' => 'Mix platter teman nugas'],
            (object) ['image_path' => '/images/hero-banner-full.jpg', 'caption' => 'Halaman teduh depan kedai'],
            (object) ['image_path' => '/storage/menu/caramel-latte-800.webp', 'caption' => 'Caramel Latte di jam istirahat'],
            (object) ['image_path' => '/images/hero-iced-coffee-splash.jpg', 'caption' => 'Susu segar bertemu espresso'],
            (object) ['image_path' => '/storage/menu/japanese-800.webp', 'caption' => 'Japanese iced filter brew'],
        ]);
        $items = $items->merge($fallback)->unique('image_path')->values();
    }

    $rowOne = $items->filter(fn ($_, $k) => $k % 2 === 0)->values();
    $rowTwo = $items->filter(fn ($_, $k) => $k % 2 === 1)->values();
    if ($rowOne->count() < 4) $rowOne = $rowOne->merge($rowOne)->values();
    if ($rowTwo->count() < 4) $rowTwo = $rowTwo->merge($rowTwo)->values();

    $resolveImg = function (?string $p): string {
        if (! $p) return asset('/images/bistro-patio-table.jpg');
        if (str_starts_with($p, 'http') || str_starts_with($p, '/')) return asset($p);
        return asset('storage/' . ltrim($p, '/'));
    };
@endphp

<section
    id="galeri-diagonal"
    data-nav-theme="light"
    data-module="gallery-diagonal"
    class="umco-section bg-[var(--batu)] text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-10 sm:space-y-12">
        <div class="max-w-5xl mx-auto text-center space-y-3">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                {{ $eyebrow }}
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $heading }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-lg mx-auto">
                {{ $subheading }}
            </p>
            <div data-reveal="fade" class="pt-1">
                <a href="{{ url($section?->cta_url ?: '/galeri') }}" wire:navigate class="btn-umco bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--mentega)] hover:text-white">
                    <span>{{ $section?->cta_label ?: 'Buka halaman galeri' }}</span>
                    <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Alur Diagonal Bebas Semua Rasio Foto (portrait, kotak, maupun landscape tampil penuh apa adanya) --}}
    <div data-reveal-media class="mt-10 sm:mt-14 -mx-6 sm:-mx-12 py-4 -rotate-[3deg] origin-center space-y-4 sm:space-y-6 select-none">
        @foreach ([$rowOne, $rowTwo] as $rIdx => $lane)
            <div
                data-diag-row
                class="flex items-center gap-4 sm:gap-6 px-4 {{ $rIdx === 1 ? '-ml-16 sm:-ml-24' : '' }}"
            >
                @for ($dup = 0; $dup < 2; $dup++)
                    @foreach ($lane as $g)
                        @php $imgUrl = $resolveImg($g->image_path); @endphp
                        <button
                            type="button"
                            data-gallery-trigger
                            data-src="{{ $imgUrl }}"
                            data-caption="{{ $g->caption ?: 'Suasana UMCO' }}"
                            @if ($dup === 1) aria-hidden="true" tabindex="-1" @endif
                            class="group relative w-[46vw] sm:w-[28vw] lg:w-[20vw] max-w-[300px] shrink-0 rounded-2xl overflow-hidden bg-[var(--kertas)] cursor-pointer text-left"
                        >
                            <img
                                src="{{ $imgUrl }}"
                                alt="{{ $g->caption ?: 'Momen di UMCO' }}"
                                loading="lazy"
                                decoding="async"
                                class="w-full h-auto block object-cover group-hover:scale-[1.03] transition-transform duration-500"
                            >
                        </button>
                    @endforeach
                @endfor
            </div>
        @endforeach
    </div>

    <dialog
        data-gallery-dialog
        class="m-auto max-w-3xl w-[92vw] bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--garis-kuwat)] rounded-2xl p-0 backdrop:bg-[#141916]/75"
    >
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-[var(--garis)]">
            <p data-gallery-dialog-caption class="font-hand text-2xl"></p>
            <button type="button" data-gallery-dialog-close class="text-sm font-semibold underline underline-offset-4 cursor-pointer">
                Tutup
            </button>
        </div>
        <img data-gallery-dialog-img src="" alt="" class="w-full max-h-[75svh] object-contain bg-[var(--kertas)]">
    </dialog>
</section>
