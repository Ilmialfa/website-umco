{{-- LANGKAH 3: PILIH MENU --}}
<section
    class="{{ $step !== 3 ? 'hidden' : 'space-y-5 umco-step-enter' }}"
    aria-hidden="{{ $step !== 3 ? 'true' : 'false' }}"
    @if ($step !== 3) inert @endif
>
    <div>
        <h2 class="font-display font-bold text-xl sm:text-2xl">3. Pilih menu</h2>
        <p class="mt-1 text-sm text-[var(--tinta-70)]">
            Total pesananmu harus memenuhi minimal belanja sebelum bayar.
        </p>
    </div>

    {{-- Progress bar minimal belanja --}}
    <div class="p-4 rounded-2xl bg-[var(--kertas-terang)] space-y-2.5">
        <div class="flex flex-wrap items-baseline justify-between gap-2 text-sm">
            @if ($quoteData['meets_min_spend'])
                <span class="font-semibold text-[var(--tinta)]">
                    {{ \App\Support\IndonesianFormat::rupiah($quoteData['subtotal']) }} dari minimal {{ \App\Support\IndonesianFormat::rupiah($quoteData['min_spend']) }}. Sudah cukup.
                </span>
            @else
                <span class="font-semibold text-[var(--tinta)]">
                    {{ \App\Support\IndonesianFormat::rupiah($quoteData['subtotal']) }} dari minimal {{ \App\Support\IndonesianFormat::rupiah($quoteData['min_spend']) }}.
                </span>
                <span class="text-[var(--stempel)] font-semibold">
                    Kurang {{ \App\Support\IndonesianFormat::rupiah($quoteData['shortfall']) }}
                </span>
            @endif
        </div>
        <div class="w-full h-2 rounded-full bg-[var(--batu)] overflow-hidden">
            <div
                class="h-full transition-all duration-300 {{ $quoteData['meets_min_spend'] ? 'bg-[var(--tinta)]' : 'bg-[var(--mentega)]' }}"
                style="width: {{ $minSpendPct }}%"
            ></div>
        </div>
    </div>

    @error('items')
        <p class="text-sm text-[var(--stempel)] font-medium">{{ $message }}</p>
    @enderror

    {{-- Pencarian & filter kategori --}}
    <div class="space-y-3">
        <input
            type="search"
            wire:model.live.debounce.250ms="menuSearch"
            placeholder="Cari kopi, matcha, makanan..."
            class="w-full px-4 py-2.5 text-base rounded-full bg-[var(--kertas-terang)] border border-transparent focus:border-[var(--tinta)]/40 focus:outline-none transition-colors"
        >
        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
            <button
                type="button"
                wire:click="$set('activeCategoryId', 0)"
                class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold border {{ $activeCategoryId === 0
                    ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                    : 'bg-[var(--kertas-terang)] border-[var(--garis)]' }}"
            >Semua</button>
            @foreach ($this->categories as $cat)
                <button
                    type="button"
                    wire:click="$set('activeCategoryId', {{ $cat->id }})"
                    class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold border {{ $activeCategoryId === $cat->id
                        ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                        : 'bg-[var(--kertas-terang)] border-[var(--garis)]' }}"
                >{{ $cat->name }}</button>
            @endforeach
        </div>
    </div>

    {{-- Keranjang aktif saat ini --}}
    @if (! empty($selectedItems))
        <div class="p-4 rounded-2xl bg-[var(--kertas-terang)] space-y-2.5">
            <p class="text-xs font-semibold text-[var(--tinta-70)]">Pesananmu sekarang</p>
            @foreach ($selectedItems as $key => $line)
                <div wire:key="cart-line-{{ $key }}" class="flex items-center justify-between gap-3 text-sm">
                    <div class="min-w-0">
                        <span class="font-semibold">{{ $line['name'] }}</span>
                        @if ($line['variant_label'] || $line['temperature'])
                            <span class="text-xs text-[var(--tinta-70)]">
                                ({{ collect([$line['variant_label'], $line['temperature']])->filter()->implode(' · ') }})
                            </span>
                        @endif
                        <span class="block text-xs text-[var(--tinta-70)] tabular-nums">
                            {{ \App\Support\IndonesianFormat::rupiah($line['unit_price'] * $line['qty']) }}
                        </span>
                    </div>
                    <div class="inline-flex items-center gap-2 shrink-0">
                        <button
                            type="button"
                            wire:click="decrementLine('{{ $key }}')"
                            aria-label="Kurangi"
                            class="w-7 h-7 rounded-full border border-[var(--tinta)] text-sm font-bold flex items-center justify-center"
                        >−</button>
                        <span class="w-5 text-center font-semibold tabular-nums">{{ $line['qty'] }}</span>
                        <button
                            type="button"
                            wire:click="incrementLine('{{ $key }}')"
                            aria-label="Tambah"
                            class="w-7 h-7 rounded-full border border-[var(--tinta)] text-sm font-bold flex items-center justify-center"
                        >+</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Daftar menu ringkas --}}
    <div class="divide-y divide-[var(--garis)] border-y border-[var(--garis)]">
        @forelse ($this->menus as $menu)
            @php
                $variants = is_array($menu->variants) ? $menu->variants : [];
                $activeVariant = $pickedVariants[$menu->id] ?? ($variants[0]['label'] ?? null);
                $activeTemp = $pickedTemps[$menu->id] ?? ($menu->has_temperature ? 'Es' : null);
                $displayPrice = (int) $menu->price;
                foreach ($variants as $v) {
                    if (isset($v['label']) && $v['label'] === $activeVariant) {
                        $displayPrice = (int) ($v['price'] ?? $displayPrice);
                    }
                }
            @endphp
            <div wire:key="menu-pick-{{ $menu->id }}" class="py-3.5 flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1 space-y-1.5">
                    <div class="flex flex-wrap items-baseline gap-x-2">
                        <span class="font-semibold text-base text-[var(--tinta)]">{{ $menu->name }}</span>
                        <span class="text-xs text-[var(--tinta-70)]">{{ $menu->category?->name }}</span>
                    </div>
                    @if ($menu->description)
                        <p class="text-xs text-[var(--tinta-70)] line-clamp-2">{{ $menu->description }}</p>
                    @endif

                    @if ($variants !== [] || $menu->has_temperature)
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            @foreach ($variants as $v)
                                @if (isset($v['label']))
                                    <button
                                        type="button"
                                        wire:click="setMenuOption({{ $menu->id }}, '{{ $v['label'] }}', null)"
                                        class="px-2.5 py-0.5 rounded-full text-xs border {{ $activeVariant === $v['label']
                                            ? 'bg-[var(--tinta)] text-[var(--kertas-terang)] border-[var(--tinta)]'
                                            : 'border-[var(--garis)] text-[var(--tinta-70)]' }}"
                                    >{{ $v['label'] }}</button>
                                @endif
                            @endforeach

                            @if ($menu->has_temperature)
                                @foreach (['Es', 'Panas'] as $tOpt)
                                    <button
                                        type="button"
                                        wire:click="setMenuOption({{ $menu->id }}, null, '{{ $tOpt }}')"
                                        class="px-2.5 py-0.5 rounded-full text-xs border {{ $activeTemp === $tOpt
                                            ? 'bg-[var(--mentega)] text-white border-[var(--mentega)] font-semibold'
                                            : 'border-[var(--garis)] text-[var(--tinta-70)]' }}"
                                    >{{ $tOpt }}</button>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>

                <div class="shrink-0 text-right space-y-1.5">
                    <p class="font-semibold text-sm tabular-nums">{{ \App\Support\IndonesianFormat::rupiah($displayPrice) }}</p>
                    <button
                        type="button"
                        wire:click="addMenu({{ $menu->id }})"
                        class="px-4 py-1.5 rounded-full border border-[var(--tinta)] text-xs font-semibold hover:bg-[var(--tinta)] hover:text-[var(--kertas-terang)] transition-colors"
                    >+ Tambah</button>
                </div>
            </div>
        @empty
            <p class="py-6 text-sm text-[var(--tinta-70)]">Menu tidak ditemukan. Coba kata kunci lain.</p>
        @endforelse
    </div>

    <div class="hidden sm:flex items-center justify-between pt-2">
        <button
            type="button"
            wire:click="prevStep"
            class="px-5 py-2.5 rounded-full border border-[var(--garis)] text-sm font-medium hover:border-[var(--tinta)]"
        >Kembali</button>
        <button
            type="button"
            wire:click="nextStep"
            @disabled($this->menuRequired && ! $quoteData['meets_min_spend'])
            class="px-7 py-3 rounded-full bg-[var(--tinta)] text-[var(--kertas-terang)] font-semibold text-sm disabled:opacity-40 hover:bg-[var(--mentega)] hover:text-white transition-colors"
        >
            Lanjut isi data
        </button>
    </div>
</section>
