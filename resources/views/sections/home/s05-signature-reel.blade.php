<section
    id="signature-reel"
    data-nav-theme="light"
    data-module="signature-reel"
    class="umco-section bg-[var(--kertas)] text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="space-y-2 max-w-xl">
                <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                    {{ $section?->heading ?: 'Yang sering balik dipesan' }}
                </h2>
                <p class="type-body text-[var(--tinta-70)]">
                    {{ $section?->subheading ?: 'Menu yang hampir selalu ada di meja pengunjung.' }}
                </p>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-28 sm:w-36 h-[3px] rounded-full bg-[var(--batu)] overflow-hidden">
                    <div data-reel-progress class="h-full bg-[var(--mentega)] origin-left transition-transform duration-200" style="transform: scaleX(0.22);"></div>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <button
                        type="button"
                        data-reel-prev
                        aria-label="Geser menu ke kiri"
                        class="w-10 h-10 rounded-full border border-[var(--garis-kuwat)] inline-flex items-center justify-center text-[var(--tinta)] hover:bg-[var(--mentega)] hover:border-[var(--mentega)] hover:text-white disabled:opacity-35 transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4"><use href="#icon-arrow-left"/></svg>
                    </button>
                    <button
                        type="button"
                        data-reel-next
                        aria-label="Geser menu ke kanan"
                        class="w-10 h-10 rounded-full border border-[var(--garis-kuwat)] inline-flex items-center justify-center text-[var(--tinta)] hover:bg-[var(--mentega)] hover:border-[var(--mentega)] hover:text-white disabled:opacity-35 transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div
            data-reel-track
            data-reveal="fade"
            class="flex gap-5 sm:gap-7 overflow-x-auto snap-x snap-mandatory scrollbar-none pb-2 cursor-grab"
        >
            @foreach ($favoriteMenuItems as $item)
                <article
                    data-reel-card
                    class="w-[78vw] sm:w-[44vw] lg:w-[27vw] max-w-[360px] shrink-0 snap-start space-y-3"
                >
                    <a href="{{ url('/menu#' . ($item->category?->slug ?? '')) }}" wire:navigate class="block group">
                        <div class="rounded-2xl overflow-hidden bg-[var(--batu)] aspect-[4/5]">
                            <x-umco.menu-picture
                                :item="$item"
                                sizes="(min-width: 1024px) 28vw, (min-width: 640px) 44vw, 78vw"
                                imgClass="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                            />
                        </div>
                        <div class="pt-3 flex items-baseline justify-between gap-3">
                            <div class="space-y-0.5 min-w-0">
                                <h3 class="font-display font-semibold text-lg sm:text-xl truncate group-hover:text-[var(--mentega)] transition-colors">{{ $item->name }}</h3>
                                <p class="text-sm text-[var(--tinta-70)] truncate">{{ $item->ingredients ?: $item->description }}</p>
                            </div>
                            <span class="font-sans font-semibold text-sm sm:text-base text-[var(--mentega-tua)] tabular-nums shrink-0">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
