@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Menu Andalan';
    $heading = $section?->heading ?: 'Racikan yang sering balik dipesan';
    $subheading = $section?->subheading ?: 'Geser kiri atau kanan untuk melihat menu andalan dari bar kami, lalu ketuk untuk detail dan pesan.';
    $ctaLabel = $section?->cta_label ?: 'Jelajahi seluruh menu';

    $featured = $favoriteMenuItems->take(8);
    if ($featured->count() < 5) {
        $featured = \App\Models\MenuItem::with('category')
            ->where('is_active', true)
            ->orderByDesc('is_favorite')
            ->orderBy('sort')
            ->limit(8)
            ->get();
    }
@endphp

<section
    id="signature-reel"
    data-nav-theme="light"
    data-module="signature-reel"
    class="umco-section bg-[var(--batu)]/65 text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-8 sm:space-y-10">
        {{-- Header Tengah + Tombol Panah Geser Kiri-Kanan (gaya referensi Screenshot 3) --}}
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
                    aria-label="Geser menu ke kiri"
                    class="w-10 h-10 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] inline-flex items-center justify-center hover:bg-[var(--mentega)] hover:text-white transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4"><use href="#icon-arrow-left"/></svg>
                </button>
                <button
                    type="button"
                    data-infinite-next
                    aria-label="Geser menu ke kanan"
                    class="w-10 h-10 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] inline-flex items-center justify-center hover:bg-[var(--mentega)] hover:text-white transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Strip Portrait Horizontal Tak Terbatas untuk Highlight Menu (bisa seret & klik menuju /menu/{slug}) --}}
    <div data-reveal-media data-infinite-viewport class="mt-8 sm:mt-10 overflow-hidden cursor-grab active:cursor-grabbing select-none">
        <div data-infinite-strip class="flex w-max will-change-transform">
            @for ($dup = 0; $dup < 2; $dup++)
                <div class="flex gap-4 sm:gap-5 pr-4 sm:pr-5" @if ($dup === 1) aria-hidden="true" @endif>
                    @foreach ($featured as $item)
                        <a
                            href="{{ route('menu.show', $item->slug) }}"
                            wire:navigate
                            data-menu-card-link
                            draggable="false"
                            @if ($dup === 1) tabindex="-1" @endif
                            class="group relative w-[64vw] sm:w-[34vw] lg:w-[22vw] max-w-[320px] shrink-0 rounded-2xl overflow-hidden bg-[var(--kertas-terang)] aspect-[3/4] block text-left"
                        >
                            @if ($item->is_placeholder || empty($item->imageUrlFor(800, 'webp')))
                                <div class="w-full h-full p-5 flex flex-col justify-between bg-[var(--kertas-terang)] text-[var(--tinta)]">
                                    <p class="font-display font-extrabold text-2xl leading-none">{{ $item->name }}</p>
                                    <p class="font-hand text-xl text-[var(--tinta-70)]">foto menyusul</p>
                                </div>
                            @else
                                <x-umco.menu-picture
                                    :item="$item"
                                    sizes="(min-width: 1024px) 22vw, (min-width: 640px) 34vw, 64vw"
                                    aspect="aspect-[3/4]"
                                    imgClass="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500"
                                    :decorative="true"
                                />
                            @endif

                            <span class="absolute left-3 right-3 bottom-3 rounded-xl bg-[var(--kertas-terang)]/95 px-3.5 py-2 flex items-center justify-between gap-2 text-[var(--tinta)]">
                                <span class="font-display font-bold text-base sm:text-lg leading-tight truncate group-hover:text-[var(--mentega)] transition-colors">
                                    {{ $item->name }}
                                </span>
                                <span class="text-xs font-semibold text-[var(--mentega-tua)] tabular-nums shrink-0">
                                    {{ (int) round($item->price / 1000) }}rb
                                </span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    <div data-reveal="fade" class="umco-container mt-8 text-center">
        <a href="{{ url('/menu') }}" wire:navigate class="btn-umco bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--mentega)] hover:text-white">
            <span>{{ $ctaLabel }}</span>
            <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
        </a>
    </div>
</section>
