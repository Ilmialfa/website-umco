@php
    $tiles = $galleryItems->take(6)->values();
    $fallbackPool = [
        '/storage/menu/aren-coffee-800.webp',
        '/storage/menu/matcha-800.webp',
        '/storage/menu/nasi-ayam-sambal-matah-800.webp',
        '/images/hero-iced-coffee-splash.webp',
        '/images/drinks-trio-banner.webp',
        '/images/bistro-patio-table.webp',
    ];
    $seenSources = [];
@endphp

<section
    id="galeri"
    data-nav-theme="light"
    data-module="gallery-mosaic"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)]"
>
    <div class="umco-container space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                {{ $section?->heading ?: 'Potret ruang dan cangkir' }}
            </h2>
            <p class="text-sm text-[var(--tinta-70)]">Klik foto untuk melihat lebih besar.</p>
        </div>

        {{-- Bento editorial 6 tile = tepat 12 sel grid (4 kolom x 3 baris), nol celah kosong --}}
        <div data-reveal-stagger class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 auto-rows-[160px] sm:auto-rows-[210px] lg:auto-rows-[230px]">
            @foreach ($tiles as $idx => $g)
                @php
                    $spanClass = match ($idx) {
                        0 => 'col-span-2 row-span-2',
                        1 => 'col-span-2',
                        4 => 'col-span-2',
                        5 => 'col-span-2',
                        default => '',
                    };
                    $hiddenMobile = $idx >= 4 ? 'hidden sm:block' : '';
                    $imgSrc = str_starts_with($g->image_path, 'http') || str_starts_with($g->image_path, '/')
                        ? $g->image_path
                        : asset('storage/' . ltrim($g->image_path, '/'));

                    // Hindari duplikasi foto identik berulang jika isi DB masih memakai sample yang sama
                    if (in_array($imgSrc, $seenSources, true)) {
                        foreach ($fallbackPool as $altSrc) {
                            if (! in_array($altSrc, $seenSources, true) && is_file(public_path(ltrim($altSrc, '/')))) {
                                $imgSrc = $altSrc;
                                break;
                            }
                        }
                    }
                    $seenSources[] = $imgSrc;
                @endphp
                <button
                    type="button"
                    data-reveal="fade"
                    data-gallery-trigger
                    @if ($idx >= 4) data-gallery-extra @endif
                    data-src="{{ $imgSrc }}"
                    data-caption="{{ $g->caption ?: 'UMCO Kampus UMRI' }}"
                    class="relative rounded-2xl overflow-hidden bg-[var(--batu)] group cursor-pointer text-left {{ $spanClass }} {{ $hiddenMobile }}"
                >
                    <img
                        src="{{ $imgSrc }}"
                        alt="{{ $g->caption ?: 'Foto kedai kopi UMCO' }}"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                    >
                    @if ($g->caption && in_array($idx, [0, 1, 4]))
                        <span class="absolute left-3 bottom-3 rounded-lg bg-[var(--kertas-terang)]/95 px-3 py-1 text-xs sm:text-sm font-medium text-[var(--tinta)]">
                            {{ $g->caption }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        @if ($tiles->count() > 4)
            <div class="sm:hidden">
                <button type="button" data-gallery-more class="btn-umco btn-umco-secondary w-full">
                    Lihat lebih banyak
                </button>
            </div>
        @endif
    </div>

    <dialog
        data-gallery-dialog
        class="m-auto max-w-3xl w-[92vw] bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--garis-kuwat)] rounded-2xl p-0 backdrop:bg-[#171C19]/75"
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
