<section
    id="aturan"
    data-nav-theme="light"
    class="umco-section bg-[var(--kertas-terang)] text-[var(--tinta)] border-t border-[var(--garis)]"
>
    <div class="umco-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
            <div class="lg:col-span-4 lg:sticky lg:top-28 space-y-3">
                <h2 data-reveal="rise" class="type-h2 font-display font-bold">
                    {{ $section?->heading ?: 'Biar sama-sama enak' }}
                </h2>
                <p class="type-body text-[var(--tinta-70)]">
                    {{ $section?->subheading ?: 'Tujuh hal penting sebelum kamu mencetak karcis meja.' }}
                </p>
            </div>

            <div class="lg:col-span-8 divide-y divide-[var(--garis)]" x-data="{ openId: 1 }">
                @foreach ($rules as $idx => $rule)
                    <div class="py-5 sm:py-6">
                        <button
                            type="button"
                            @click="openId = openId === {{ $idx + 1 }} ? null : {{ $idx + 1 }}"
                            :aria-expanded="openId === {{ $idx + 1 }}"
                            class="w-full flex items-baseline gap-4 sm:gap-7 text-left cursor-pointer group"
                        >
                            <span class="font-display font-extrabold text-[2.5rem] sm:text-[3.5rem] lg:text-[4rem] leading-none text-[var(--tinta)]/25 group-hover:text-[var(--cabai)] transition-colors tabular-nums shrink-0">
                                {{ str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="flex-1 font-display font-bold text-xl sm:text-2xl leading-snug">
                                {{ $rule->title }}
                            </span>
                            <span class="text-sm font-semibold text-[var(--tinta-70)] shrink-0" x-text="openId === {{ $idx + 1 }} ? 'Tutup' : 'Baca'"></span>
                        </button>

                        <div
                            x-show="openId === {{ $idx + 1 }}"
                            x-transition
                            class="pl-14 sm:pl-24 pt-2.5 text-base text-[var(--tinta-70)] leading-relaxed max-w-2xl"
                        >
                            {{ $rule->body }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
