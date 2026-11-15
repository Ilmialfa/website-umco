<section
    id="aturan"
    data-nav-theme="light"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)]"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-6">
                <div class="space-y-3">
                    <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                        {{ $section?->heading ?: 'Biar sama-sama enak' }}
                    </h2>
                    <p class="type-body text-[var(--tinta-70)]">
                        {{ $section?->subheading ?: 'Tujuh hal penting sebelum kamu mencetak karcis meja.' }}
                    </p>
                </div>

                <div class="hidden sm:block rounded-2xl overflow-hidden aspect-[4/3] bg-[var(--batu)]">
                    <img
                        src="{{ asset('/images/bistro-patio-table.webp') }}"
                        alt="Suasana meja kedai UMCO untuk nongkrong dan belajar"
                        loading="lazy"
                        decoding="async"
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>

            <div class="lg:col-span-7 divide-y divide-[var(--garis)]" x-data="{ openId: 1 }">
                @foreach ($rules as $idx => $rule)
                    <div class="py-5 sm:py-6">
                        <button
                            type="button"
                            @click="openId = openId === {{ $idx + 1 }} ? null : {{ $idx + 1 }}"
                            :aria-expanded="openId === {{ $idx + 1 }}"
                            class="w-full flex items-baseline gap-4 sm:gap-6 text-left cursor-pointer group"
                        >
                            <span class="font-sans font-semibold text-sm text-[var(--mentega-tua)] tabular-nums shrink-0 w-6">
                                {{ $idx + 1 }}.
                            </span>
                            <span class="flex-1 font-display font-semibold text-lg sm:text-xl leading-snug group-hover:text-[var(--mentega-tua)] transition-colors">
                                {{ $rule->title }}
                            </span>
                            <span class="text-xs sm:text-sm font-medium text-[var(--tinta-70)] shrink-0" x-text="openId === {{ $idx + 1 }} ? 'Tutup' : 'Baca'"></span>
                        </button>

                        <div
                            x-show="openId === {{ $idx + 1 }}"
                            x-transition
                            class="pl-10 sm:pl-12 pt-2.5 text-sm sm:text-base text-[var(--tinta-70)] leading-relaxed max-w-2xl"
                        >
                            {{ $rule->body }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
