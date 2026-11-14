<section
    id="signature-reel"
    data-nav-theme="light"
    data-module="signature-reel"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)] overflow-hidden border-t border-[var(--garis)]"
>
    <div class="umco-container space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="space-y-2 max-w-xl">
                <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                    {{ $section?->heading ?: 'Yang sering balik dipesan' }}
                </h2>
                <p class="type-body text-[var(--tinta-70)]">
                    {{ $section?->subheading ?: 'Menu yang hampir selalu ada di meja pengunjung.' }}
                </p>
            </div>
            <div class="w-32 h-[3px] bg-[var(--garis)] overflow-hidden">
                <div data-reel-progress class="h-full bg-[var(--tinta)] origin-left" style="transform: scaleX(0.2);"></div>
            </div>
        </div>

        <div
            data-reel-track
            class="flex gap-5 sm:gap-7 overflow-x-auto lg:overflow-visible snap-x snap-mandatory lg:snap-none scrollbar-none pb-2"
        >
            @foreach ($favoriteMenuItems as $index => $item)
                <article
                    data-reel-card
                    class="w-[78vw] sm:w-[46vw] lg:w-[27vw] shrink-0 snap-start space-y-3 transition-opacity duration-300"
                >
                    <a href="{{ url('/menu#' . ($item->category?->slug ?? '')) }}" wire:navigate class="block group">
                        <div class="overflow-hidden">
                            <x-umco.menu-picture
                                :item="$item"
                                sizes="(min-width: 1024px) 28vw, (min-width: 640px) 46vw, 78vw"
                                imgClass="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                            />
                        </div>
                        <div class="pt-3 flex items-baseline justify-between gap-3">
                            <div class="space-y-0.5 min-w-0">
                                <span class="font-led text-xs text-[var(--cabai)]">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="font-display font-bold text-xl sm:text-2xl truncate">{{ $item->name }}</h3>
                                <p class="text-sm text-[var(--tinta-70)] truncate">{{ $item->ingredients ?: $item->description }}</p>
                            </div>
                            <span class="font-sans font-semibold text-sm sm:text-base tabular-nums shrink-0">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
