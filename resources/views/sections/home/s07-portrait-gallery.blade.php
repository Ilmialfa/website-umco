@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Galeri Portrait';
    $heading = $section?->heading ?: 'Ruang untuk melambat sebentar';
    $subheading = $section?->subheading ?: 'Potret suasana kedai dan cangkir harian yang terus bergulir. Geser kiri atau kanan sesukamu.';

    $items = ($portraitGallery ?? collect())->toBase()->values();
    if ($items->count() < 5) {
        $fallback = collect([
            (object) ['image_path' => '/storage/menu/aren-coffee-800.webp', 'caption' => 'Aren Coffee andalan meja pagi'],
            (object) ['image_path' => '/storage/menu/matcha-800.webp', 'caption' => 'Matcha dingin di sudut terang'],
            (object) ['image_path' => '/storage/menu/v60-800.webp', 'caption' => 'Seduhan V60 manual tiap pagi'],
            (object) ['image_path' => '/storage/menu/bliss-butter-cream-800.webp', 'caption' => 'Bliss Butter Cream favorit sore'],
            (object) ['image_path' => '/storage/menu/vietnam-drip-800.webp', 'caption' => 'Tetesan pelan Vietnam Drip'],
            (object) ['image_path' => '/storage/menu/nasi-ayam-sambal-matah-800.webp', 'caption' => 'Makan siang kampus tanpa repot'],
        ]);
        $items = $items->merge($fallback)->unique('image_path')->take(8)->values();
    }

    $resolveImg = function (?string $p): string {
        if (! $p) return asset('/storage/menu/aren-coffee-800.webp');
        if (str_starts_with($p, 'http') || str_starts_with($p, '/')) return asset($p);
        return asset('storage/' . ltrim($p, '/'));
    };
@endphp

<section
    id="galeri"
    data-nav-theme="light"
    data-module="gallery-infinite"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-8 sm:space-y-10">
        <div class="max-w-2xl mx-auto text-center space-y-3">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                {{ $eyebrow }}
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $heading }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-lg mx-auto">
                {{ $subheading }}
            </p>
            <div data-reveal="fade" class="pt-1 flex items-center justify-center gap-3">
                <button
                    type="button"
                    data-infinite-prev
                    aria-label="Geser galeri ke kiri"
                    class="w-10 h-10 rounded-full bg-[var(--batu)] text-[var(--tinta)] inline-flex items-center justify-center hover:bg-[var(--mentega)] hover:text-white transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4"><use href="#icon-arrow-left"/></svg>
                </button>
                <button
                    type="button"
                    data-infinite-next
                    aria-label="Geser galeri ke kanan"
                    class="w-10 h-10 rounded-full bg-[var(--batu)] text-[var(--tinta)] inline-flex items-center justify-center hover:bg-[var(--mentega)] hover:text-white transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Strip Portrait Horizontal Tak Terbatas (Duplikat mulus 2x, bisa diseret & klik lightbox) --}}
    <div data-reveal-media data-infinite-viewport class="mt-8 sm:mt-10 overflow-hidden cursor-grab active:cursor-grabbing select-none">
        <div data-infinite-strip class="flex w-max will-change-transform">
            @for ($dup = 0; $dup < 2; $dup++)
                <div class="flex gap-4 sm:gap-5 pr-4 sm:pr-5" @if ($dup === 1) aria-hidden="true" @endif>
                    @foreach ($items as $g)
                        @php $imgUrl = $resolveImg($g->image_path); @endphp
                        <button
                            type="button"
                            data-gallery-trigger
                            data-src="{{ $imgUrl }}"
                            data-caption="{{ $g->caption ?: 'UMCO Kampus UMRI' }}"
                            class="group relative w-[62vw] sm:w-[34vw] lg:w-[22vw] max-w-[320px] shrink-0 rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[3/4] text-left cursor-pointer"
                        >
                            <img
                                src="{{ $imgUrl }}"
                                alt="{{ $g->caption ?: 'Foto portrait kedai UMCO' }}"
                                loading="lazy"
                                decoding="async"
                                draggable="false"
                                class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                            >
                            @if ($g->caption)
                                <span class="absolute left-3 bottom-3 max-w-[88%] truncate rounded-lg bg-[var(--kertas-terang)]/95 px-3 py-1 text-xs font-medium text-[var(--tinta)]">
                                    {{ $g->caption }}
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endfor
        </div>
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
