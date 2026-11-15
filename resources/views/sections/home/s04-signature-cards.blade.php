@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Menu Andalan';
    $heading = $section?->heading ?: 'Menu favorit harian';
    $subheading = $section?->subheading ?: 'Geser kursor ke kiri atau kanan untuk melihat menu andalan dari bar, lalu ketuk untuk detail dan pesan.';
    $featuredMenu = $favoriteMenuItems->take(8);
@endphp

<section
    id="signature-reel"
    data-nav-theme="light"
    data-module="signature-reel"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] overflow-hidden"
>
    {{-- Header Tengah Editorial Maksimal 1-2 Baris (Tanpa tombol geser kanan-kiri) --}}
    <div class="umco-container space-y-4 text-center">
        <div class="max-w-5xl mx-auto space-y-3">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                {{ $eyebrow }}
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $heading }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-xl mx-auto">
                {{ $subheading }}
            </p>
        </div>
    </div>

    {{-- Strip Horizontal Infinite Menggunakan Desain Kartu Menu Sebelumnya + Kontrol Kursor Kiri/Kanan --}}
    <div
        data-reveal-media
        data-infinite-viewport
        class="mt-10 sm:mt-12 w-full overflow-hidden cursor-ew-resize select-none"
    >
        <div
            data-infinite-strip
            class="flex items-start gap-5 sm:gap-6 w-max will-change-transform px-4"
        >
            @for ($loopCopy = 0; $loopCopy < 2; $loopCopy++)
                <div class="flex items-start gap-5 sm:gap-6 shrink-0" @if ($loopCopy === 1) aria-hidden="true" @endif>
                    @foreach ($featuredMenu as $item)
                        <article class="w-[64vw] sm:w-[270px] lg:w-[295px] shrink-0 flex flex-col">
                            <a
                                href="{{ route('menu.show', $item->slug) }}"
                                wire:navigate
                                data-menu-card-link
                                class="group block space-y-3.5"
                            >
                                <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[4/5]">
                                    <x-umco.menu-picture
                                        :item="$item"
                                        sizes="(min-width: 1024px) 295px, (min-width: 640px) 270px, 64vw"
                                        imgClass="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-500 pointer-events-none"
                                    />
                                </div>

                                <div class="pt-1 space-y-1.5">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <h3 class="font-display font-bold text-xl sm:text-[1.35rem] leading-tight text-[var(--tinta)] group-hover:text-[var(--mentega)] transition-colors truncate">
                                            {{ $item->name }}
                                        </h3>
                                        <span class="text-xs sm:text-sm font-semibold text-[var(--mentega-tua)] tabular-nums shrink-0">
                                            {{ (int) round($item->price / 1000) }}rb
                                        </span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-[var(--tinta-70)] line-clamp-2 leading-snug">
                                        {{ $item->ingredients ?: $item->description }}
                                    </p>
                                    <span class="inline-block font-hand text-lg text-[var(--mentega)] group-hover:translate-x-1 transition-transform">
                                        lihat info & pesan &rarr;
                                    </span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>

    <div class="umco-container mt-10 sm:mt-12 text-center" data-reveal="fade">
        <a href="{{ url($section?->cta_url ?: '/menu') }}" wire:navigate class="btn-umco btn-umco-secondary">
            <span>{{ $section?->cta_label ?: 'Jelajahi seluruh menu' }}</span>
            <svg class="w-4 h-4 shrink-0"><use href="#icon-arrow-right"/></svg>
        </a>
    </div>
</section>
