@php
    $tiles = $galleryItems->take(7)->values();
@endphp

<section
    id="galeri"
    data-nav-theme="light"
    data-module="gallery-mosaic"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] border-t border-[var(--garis)]"
>
    <div class="umco-container space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                {{ $section?->heading ?: 'Potret ruang dan cangkir' }}
            </h2>
            <p class="text-sm text-[var(--tinta-70)]">Klik foto untuk melihat lebih besar.</p>
        </div>

        {{-- Mosaik editorial: 2 kolom di HP/Tablet (maks 6 dulu di HP), 4 kolom asimetris di desktop --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-2 auto-rows-[160px] sm:auto-rows-[210px] lg:auto-rows-[220px]">
            @foreach ($tiles as $idx => $g)
                @php
                    $spanClass = match ($idx) {
                        0 => 'col-span-2 row-span-2',
                        1 => 'lg:row-span-2',
                        3 => 'lg:col-span-2',
                        default => '',
                    };
                    $hiddenMobile = $idx >= 6 ? 'hidden sm:block' : '';
                    $imgSrc = str_starts_with($g->image_path, 'http') || str_starts_with($g->image_path, '/')
                        ? $g->image_path
                        : asset('storage/' . ltrim($g->image_path, '/'));
                @endphp
                <button
                    type="button"
                    data-gallery-trigger
                    @if ($idx >= 6) data-gallery-extra @endif
                    data-src="{{ $imgSrc }}"
                    data-caption="{{ $g->caption ?: 'UMCO Kampus UMRI' }}"
                    class="relative overflow-hidden bg-[var(--garis)]/40 group cursor-pointer text-left {{ $spanClass }} {{ $hiddenMobile }}"
                >
                    <img
                        src="{{ $imgSrc }}"
                        alt="{{ $g->caption ?: 'Foto kedai kopi UMCO' }}"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                    >
                    @if ($g->caption && in_array($idx, [0, 3, 5]))
                        <span class="absolute left-3 bottom-2.5 bg-[var(--kertas-terang)] px-2.5 py-0.5 font-hand text-xl text-[var(--tinta)]">
                            {{ $g->caption }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        @if ($tiles->count() > 6)
            <div class="sm:hidden">
                <button type="button" data-gallery-more class="btn-umco btn-umco-secondary w-full">
                    Lihat lebih banyak
                </button>
            </div>
        @endif
    </div>

    <dialog
        data-gallery-dialog
        class="m-auto max-w-3xl w-[92vw] bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--tinta)] p-0 backdrop:bg-[var(--tinta)]/70"
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-[var(--garis)]">
            <p data-gallery-dialog-caption class="font-hand text-2xl"></p>
            <button type="button" data-gallery-dialog-close class="text-sm font-semibold underline underline-offset-4 cursor-pointer">
                Tutup
            </button>
        </div>
        <img data-gallery-dialog-img src="" alt="" class="w-full max-h-[75svh] object-contain bg-[var(--kertas)]">
    </dialog>
</section>
