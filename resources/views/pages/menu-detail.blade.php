<x-layouts.public
    page="menu"
    initial-nav-theme="light"
    :title="$item->name . ' — Menu UMCO'"
    :description="$item->description ?: ($item->ingredients ?: 'Detail menu dan informasi rasa dari bar UMCO Kampus 2 UMRI.')"
>
    <div
        x-data="{
            item: @js($itemData),
            variantIdx: 0,
            temp: @js($item->has_temperature ? 'Es' : null),
            qty: 1,
            addedFeedback: false,
            notaCount: 0,

            init() {
                const list = window.umcoFormatters ? window.umcoFormatters.loadNota() : [];
                this.notaCount = list.reduce((s, r) => s + Number(r.qty || 1), 0);
            },

            activeUnitPrice() {
                if (this.item.variants && this.item.variants.length) {
                    return Number(this.item.variants[this.variantIdx]?.price || this.item.price);
                }
                return Number(this.item.price);
            },

            activeVariantLabel() {
                if (this.item.variants && this.item.variants.length) {
                    return this.item.variants[this.variantIdx]?.label || null;
                }
                return null;
            },

            lineTotal() {
                return this.activeUnitPrice() * this.qty;
            },

            formatRupiah(v) {
                return window.umcoFormatters ? window.umcoFormatters.formatRupiah(v) : ('Rp ' + Number(v || 0).toLocaleString('id-ID'));
            },

            commitToNota() {
                if (!this.item.is_available || !window.umcoFormatters) return [];
                const variantLabel = this.activeVariantLabel();
                const temperature = this.item.has_temperature ? this.temp : null;
                const price = this.activeUnitPrice();
                const key = [this.item.slug, variantLabel || '-', temperature || '-'].join('|');

                const nota = window.umcoFormatters.loadNota();
                const existing = nota.find(r => r.key === key || (!r.key && r.slug === this.item.slug));
                if (existing) {
                    existing.qty += this.qty;
                    existing.price = price;
                } else {
                    nota.push({
                        id: this.item.id,
                        key,
                        slug: this.item.slug,
                        name: this.item.name,
                        price,
                        qty: this.qty,
                        variant_label: variantLabel,
                        temperature,
                    });
                }
                window.umcoFormatters.saveNota(nota);
                this.notaCount = nota.reduce((s, r) => s + Number(r.qty || 1), 0);
                return nota;
            },

            addOnly() {
                this.commitToNota();
                this.addedFeedback = true;
                setTimeout(() => { this.addedFeedback = false; }, 2200);
            },

            reserveNow() {
                const nota = this.commitToNota();
                const encoded = nota.map(r => r.slug + ':' + r.qty).join(',');
                const target = encoded
                    ? '{{ url('/reservasi') }}?items=' + encodeURIComponent(encoded)
                    : '{{ url('/reservasi') }}';
                window.location.href = target;
            }
        }"
        class="bg-[var(--kertas)] text-[var(--tinta)]"
    >
        {{-- Bagian atas: Breadcrumb + Detail Menu --}}
        <section data-nav-theme="light" class="pt-24 sm:pt-28 pb-16 sm:pb-20">
            <div class="umco-container space-y-8 sm:space-y-10">
                <div data-reveal="fade" class="flex flex-wrap items-center justify-between gap-3 text-sm text-[var(--tinta-70)]">
                    <nav aria-label="Breadcrumb" class="flex items-center gap-2">
                        <a href="{{ url('/menu') }}" wire:navigate class="hover:text-[var(--tinta)] underline underline-offset-4">
                            &larr; Semua Menu
                        </a>
                        <span aria-hidden="true">/</span>
                        <a href="{{ url('/menu#' . ($item->category?->slug ?? '')) }}" wire:navigate class="hover:text-[var(--tinta)]">
                            {{ $item->category?->name ?? 'Menu UMCO' }}
                        </a>
                    </nav>

                    <a
                        x-show="notaCount > 0"
                        href="{{ url('/reservasi') }}"
                        wire:navigate
                        style="display: none;"
                        class="rounded-full bg-[var(--aksen-soft)] text-[var(--tinta)] px-4 py-1.5 text-xs font-semibold"
                    >
                        <span x-text="notaCount + ' item di nota — Lanjut Reservasi →'"></span>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-start">
                    {{-- Kiri: Foto menu atau placeholder tipografi --}}
                    <div data-reveal-media class="lg:col-span-5">
                        @if ($itemData['is_placeholder'] || empty($itemData['image_800']))
                            <div class="aspect-[4/5] rounded-2xl overflow-hidden p-7 sm:p-9 flex flex-col justify-between {{ $itemData['placeholder_tone'] }}">
                                <div class="space-y-2">
                                    <span class="font-hand text-2xl text-[var(--mentega)]">
                                        {{ $item->category?->name ?? 'Menu UMCO' }}
                                    </span>
                                    <p class="font-display font-extrabold text-[2.4rem] sm:text-[3rem] leading-[0.9] tracking-[-0.04em]">
                                        {{ $item->name }}
                                    </p>
                                </div>
                                <p class="font-hand text-2xl text-[var(--tinta)]/70">foto sedang disiapkan</p>
                            </div>
                        @else
                            <div class="relative rounded-2xl overflow-hidden bg-[var(--batu)]">
                                <x-umco.menu-picture
                                    :item="$item"
                                    sizes="(min-width: 1024px) 42vw, 94vw"
                                    aspect="aspect-[4/5]"
                                    :eager="true"
                                    :decorative="true"
                                />
                                @if ($item->is_new)
                                    <span class="absolute top-3.5 left-3.5 px-3 py-1 rounded-full text-xs font-semibold bg-[var(--mentega)] text-white">Baru di Bar</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Kanan: Deskripsi, catatan rasa, varian, dan aksi reservasi --}}
                    <div class="lg:col-span-7 space-y-6 lg:pt-2">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <span data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                                    {{ $item->category?->name ?? 'Menu UMCO' }}
                                </span>
                                @if (! $item->is_available)
                                    <span class="rounded-full bg-[var(--batu)] text-[var(--tinta-70)] px-3 py-0.5 text-xs font-semibold">Sedang habis hari ini</span>
                                @endif
                            </div>

                            <h1 data-reveal="rise" class="type-display-l font-display font-bold text-[var(--tinta)]">
                                {{ $item->name }}
                            </h1>

                            <p data-reveal="fade" class="font-display font-bold text-2xl sm:text-3xl text-[var(--mentega-tua)] tabular-nums" x-text="formatRupiah(activeUnitPrice())">
                                {{ $itemData['price_formatted'] }}
                            </p>
                        </div>

                        {{-- Deskripsi & Komposisi --}}
                        <div data-reveal="fade" class="rounded-2xl bg-[var(--kertas-terang)] p-6 space-y-4">
                            @if ($item->description)
                                <div class="space-y-1.5">
                                    <h2 class="text-xs font-semibold text-[var(--mentega-tua)]">Tentang racikan ini</h2>
                                    <p class="type-body text-[var(--tinta)] leading-relaxed">
                                        {{ $item->description }}
                                    </p>
                                </div>
                            @endif

                            @if ($item->ingredients)
                                <div class="space-y-1.5">
                                    <h2 class="text-xs font-semibold text-[var(--mentega-tua)]">Komposisi & bahan utama</h2>
                                    <p class="text-sm sm:text-base text-[var(--tinta-70)] leading-relaxed">
                                        {{ $item->ingredients }}
                                    </p>
                                </div>
                            @endif

                            @if ($item->is_favorite)
                                <p class="font-hand text-2xl text-[var(--mentega)] pt-1">
                                    Salah satu yang paling sering diminta balik lagi di kasir.
                                </p>
                            @endif
                        </div>

                        {{-- Varian jika ada --}}
                        <template x-if="item.variants && item.variants.length">
                            <div class="space-y-2.5">
                                <p class="text-xs font-semibold text-[var(--tinta-70)]">Pilih ukuran / varian</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="(v, idx) in item.variants" :key="v.label">
                                        <button
                                            type="button"
                                            @click="variantIdx = idx"
                                            class="px-4 py-2.5 rounded-full text-xs sm:text-sm font-semibold transition-colors cursor-pointer"
                                            :class="variantIdx === idx ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--batu)]'"
                                            x-text="v.label + ' · ' + formatRupiah(v.price)"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Es / Panas jika mendukung --}}
                        @if ($item->has_temperature)
                            <div class="space-y-2.5">
                                <p class="text-xs font-semibold text-[var(--tinta-70)]">Penyajian</p>
                                <div class="flex gap-2">
                                    @foreach (['Es', 'Panas'] as $t)
                                        <button
                                            type="button"
                                            @click="temp = '{{ $t }}'"
                                            class="px-5 h-10 rounded-full text-sm font-semibold transition-colors cursor-pointer"
                                            :class="temp === '{{ $t }}' ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--batu)]'"
                                        >
                                            {{ $t }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Jumlah + Tombol CTA masuk Reservasi --}}
                        <div class="pt-2 space-y-4">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="inline-flex items-center rounded-full bg-[var(--batu)] px-1.5">
                                    <button type="button" @click="qty = Math.max(1, qty - 1)" aria-label="Kurangi jumlah" class="w-10 h-11 font-bold cursor-pointer">-</button>
                                    <span class="w-9 text-center font-semibold tabular-nums" x-text="qty"></span>
                                    <button type="button" @click="qty = qty + 1" aria-label="Tambah jumlah" class="w-10 h-11 font-bold cursor-pointer">+</button>
                                </div>

                                <button
                                    type="button"
                                    @click="reserveNow()"
                                    :disabled="!item.is_available"
                                    class="btn-umco bg-[#134731] text-white hover:bg-[#0E3524] disabled:opacity-50 cursor-pointer flex-1 sm:flex-none sm:min-w-[260px]"
                                >
                                    <span>Pesan &amp; Lanjut Reservasi</span>
                                    <span class="ml-1.5 font-normal opacity-85 tabular-nums" x-text="'(' + formatRupiah(lineTotal()) + ')'"></span>
                                </button>

                                <button
                                    type="button"
                                    @click="addOnly()"
                                    :disabled="!item.is_available"
                                    class="btn-umco bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-[var(--kertas)] disabled:opacity-50 cursor-pointer"
                                >
                                    <span x-text="addedFeedback ? 'Masuk ke Nota!' : '+ Simpan ke Nota dulu'"></span>
                                </button>
                            </div>

                            <p class="text-xs text-[var(--tinta-70)] leading-relaxed">
                                Menu yang kamu pilih langsung terbawa ke formulir reservasi meja (minimal 2 orang, minimal belanja mulai Rp 50.000).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Menu Terkait lainnya --}}
        @if ($related->isNotEmpty())
            <section data-nav-theme="light" class="bg-[var(--batu)]/65 py-16 sm:py-20">
                <div class="umco-container space-y-8">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div class="space-y-2">
                            <p class="font-hand text-2xl text-[var(--mentega)]">Bisa jadi pasangan pas</p>
                            <h2 class="type-h2 font-display font-bold">Menu lain untuk dicoba</h2>
                        </div>
                        <a href="{{ url('/menu') }}" wire:navigate class="text-sm font-semibold underline underline-offset-4 hover:text-[var(--mentega)]">
                            Lihat semua menu &rarr;
                        </a>
                    </div>

                    <div data-reveal-stagger class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        @foreach ($related as $rel)
                            <a href="{{ route('menu.show', $rel->slug) }}" wire:navigate class="group block space-y-3">
                                <div class="rounded-2xl overflow-hidden bg-[var(--kertas-terang)]">
                                    @if ($rel->is_placeholder || empty($rel->imageUrlFor(800, 'webp')))
                                        <div class="aspect-[4/5] p-4 flex flex-col justify-between bg-[var(--kertas-terang)]">
                                            <p class="font-display font-extrabold text-xl leading-none">{{ $rel->name }}</p>
                                            <p class="font-hand text-lg text-[var(--tinta-70)]">foto menyusul</p>
                                        </div>
                                    @else
                                        <x-umco.menu-picture
                                            :item="$rel"
                                            sizes="(min-width: 1024px) 22vw, 45vw"
                                            aspect="aspect-[4/5]"
                                            class="group-hover:scale-[1.03] transition-transform duration-500"
                                            :decorative="true"
                                        />
                                    @endif
                                </div>
                                <div class="flex items-baseline justify-between gap-2">
                                    <h3 class="font-display font-bold text-base sm:text-lg leading-tight group-hover:text-[var(--mentega)] transition-colors">
                                        {{ $rel->name }}
                                    </h3>
                                    <span class="font-sans font-semibold text-sm text-[var(--mentega-tua)] tabular-nums shrink-0">
                                        {{ \App\Support\IndonesianFormat::ribu($rel->price) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
</x-layouts.public>
