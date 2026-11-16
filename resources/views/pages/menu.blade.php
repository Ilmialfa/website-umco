@php
    $brandShort = \App\Support\Settings::get('brand.brand_short') ?: 'UMCO';
    $menuTitle = $hero?->data['meta_title'] ?? ('Menu — ' . $brandShort);
    $menuDescription = $hero?->data['meta_description'] ?? "Daftar lengkap {$itemsCount} menu kopi, teh, mocktail, makanan, camilan, dan roti bakar {$brandShort}.";
@endphp

<x-layouts.public
    page="menu"
    initial-nav-theme="light"
    :title="$menuTitle"
    :description="$menuDescription"
>
    <div
        x-data="{
            items: @js($itemsPayload),
            categories: @js($categories),
            activeCategory: 'all',
            search: '',
            viewMode: window.innerWidth < 640 ? 'list' : 'photo',
            selectedItem: null,
            selectedVariantIdx: 0,
            selectedTemp: 'Es',
            selectedQty: 1,
            nota: [],
            notaOpen: false,

            init() {
                this.nota = window.umcoFormatters.loadNota();
                window.addEventListener('umco:nota-updated', () => {
                    this.nota = window.umcoFormatters.loadNota();
                });
                const hash = window.location.hash.replace('#', '');
                if (hash && this.categories.some(c => c.slug === hash)) {
                    this.activeCategory = hash;
                }
            },

            selectCategory(slug) {
                this.activeCategory = slug;
                if (slug === 'all') {
                    history.replaceState(null, '', window.location.pathname);
                } else {
                    history.replaceState(null, '', '#' + slug);
                }
            },

            groupedCategories() {
                const q = this.search.toLowerCase().trim();
                return this.categories
                    .filter(cat => this.activeCategory === 'all' || cat.slug === this.activeCategory)
                    .map(cat => {
                        const filtered = this.items.filter(item => {
                            if (item.category_id !== cat.id) return false;
                            if (!q) return true;
                            return item.name.toLowerCase().includes(q)
                                || (item.ingredients || '').toLowerCase().includes(q)
                                || (item.description || '').toLowerCase().includes(q);
                        });
                        return { ...cat, items: filtered };
                    })
                    .filter(g => g.items.length > 0);
            },

            openDetail(item) {
                this.selectedItem = item;
                this.selectedVariantIdx = 0;
                this.selectedTemp = item.has_temperature ? 'Es' : null;
                this.selectedQty = 1;
            },

            activeUnitPrice() {
                if (!this.selectedItem) return 0;
                if (this.selectedItem.variants && this.selectedItem.variants.length) {
                    return Number(this.selectedItem.variants[this.selectedVariantIdx]?.price || this.selectedItem.price);
                }
                return Number(this.selectedItem.price);
            },

            activeVariantLabel() {
                if (!this.selectedItem || !this.selectedItem.variants || !this.selectedItem.variants.length) return '';
                return this.selectedItem.variants[this.selectedVariantIdx]?.label || '';
            },

            handleNotaClick(item, evt) {
                if ((item.variants && item.variants.length) || item.has_temperature || !item.is_available) {
                    this.openDetail(item);
                    return;
                }

                const key = item.slug;
                const existing = this.nota.find(r => (r.key || r.slug) === key);
                if (existing) {
                    existing.qty += 1;
                } else {
                    this.nota.push({
                        id: item.id,
                        key: key,
                        slug: item.slug,
                        name: item.name,
                        variant_label: null,
                        temperature: null,
                        price: Number(item.price),
                        qty: 1
                    });
                }
                window.umcoFormatters.saveNota(this.nota);
                window.umcoFlyToNota?.(evt?.currentTarget, !item.is_placeholder ? item.image_800 : null);
            },

            addToNota(evt) {
                if (!this.selectedItem || !this.selectedItem.is_available) return;
                const variantLabel = this.activeVariantLabel();
                const temp = this.selectedItem.has_temperature ? this.selectedTemp : null;
                const price = this.activeUnitPrice();
                const key = [this.selectedItem.slug, variantLabel, temp].filter(Boolean).join('|');
                const imgUrl = !this.selectedItem.is_placeholder ? this.selectedItem.image_800 : null;

                const existing = this.nota.find(r => (r.key || r.slug) === key);
                if (existing) {
                    existing.qty += this.selectedQty;
                } else {
                    this.nota.push({
                        id: this.selectedItem.id,
                        key: key,
                        slug: this.selectedItem.slug,
                        name: this.selectedItem.name,
                        variant_label: variantLabel || null,
                        temperature: temp,
                        price: price,
                        qty: this.selectedQty
                    });
                }
                window.umcoFormatters.saveNota(this.nota);
                window.umcoFlyToNota?.(evt?.currentTarget, imgUrl);
                this.selectedItem = null;
            },

            decNota(idx) {
                if (this.nota[idx].qty > 1) {
                    this.nota[idx].qty -= 1;
                } else {
                    this.nota.splice(idx, 1);
                }
                window.umcoFormatters.saveNota(this.nota);
            },

            incNota(idx) {
                this.nota[idx].qty += 1;
                window.umcoFormatters.saveNota(this.nota);
            },

            notaQty() {
                return this.nota.reduce((s, r) => s + r.qty, 0);
            },

            notaTotal() {
                return this.nota.reduce((s, r) => s + (r.price * r.qty), 0);
            },

            formatRupiah(v) {
                return window.umcoFormatters.formatRupiah(v);
            },

            reserveUrl() {
                if (window.umcoFormatters?.buildReserveUrl) {
                    return window.umcoFormatters.buildReserveUrl(this.nota, '{{ url('/reservasi') }}');
                }
                if (!this.nota.length) return '{{ url('/reservasi') }}';
                const encoded = this.nota.map(r => r.slug + ':' + r.qty).join(',');
                return '{{ url('/reservasi') }}?items=' + encodeURIComponent(encoded);
            }
        }"
        x-effect="window.umcoScrollLock?.('menu-sheet', selectedItem !== null)"
        @keydown.escape.window="selectedItem = null; notaOpen = false"
    >
        {{-- Hero pendek --}}
        <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-24 sm:pt-28 pb-6 sm:pb-8">
            <div class="umco-container">
                <div class="flex flex-wrap items-baseline gap-4">
                    <h1 class="type-display font-display font-semibold">
                        {{ $hero?->heading ?: 'Menu' }}
                    </h1>
                    <span class="font-sans font-semibold text-sm sm:text-base text-[var(--mentega-tua)] tabular-nums">{{ $itemsCount }} item</span>
                </div>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] mt-3 max-w-xl">
                    {{ $hero?->subheading ?: 'Harga sudah termasuk senyum kasir.' }}
                </p>
            </div>
        </section>

        {{-- Bar kontrol sticky menyatu sewarna dengan latar halaman (Pt 8) --}}
        <section class="sticky top-14 sm:top-16 lg:top-[72px] z-30 bg-[var(--kertas)] py-4 sm:py-5">
            <div class="umco-container space-y-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="relative flex-1 sm:max-w-sm">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Cari kopi, nasi, camilan..."
                            class="w-full h-11 pl-11 pr-9 rounded-full bg-[var(--kertas-terang)] text-sm text-[var(--tinta)] placeholder:text-[var(--tinta-40)] focus:outline-none focus:ring-2 focus:ring-[var(--mentega)]/35"
                        >
                        <svg class="w-4 h-4 text-[var(--tinta-40)] absolute left-4 top-3.5 pointer-events-none"><use href="#icon-search"/></svg>
                        <button
                            type="button"
                            x-show="search.length > 0"
                            @click="search = ''"
                            style="display: none;"
                            aria-label="Hapus pencarian"
                            class="absolute right-3 top-2.5 w-6 h-6 flex items-center justify-center text-[var(--tinta-70)] cursor-pointer"
                        >
                            <svg class="w-4 h-4"><use href="#icon-x"/></svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <span class="text-xs text-[var(--tinta-70)] sm:hidden">Tampilan:</span>
                        <div class="inline-flex rounded-full bg-[var(--kertas-terang)] p-1 gap-1">
                            <button
                                type="button"
                                @click="viewMode = 'list'"
                                class="inline-flex items-center gap-1.5 px-4 h-9 rounded-full text-xs sm:text-sm font-medium transition-colors cursor-pointer"
                                :class="viewMode === 'list' ? 'bg-[var(--tinta)] text-[var(--kertas)]' : 'text-[var(--tinta-70)] hover:text-[var(--tinta)]'"
                            >
                                <svg class="w-4 h-4"><use href="#icon-list"/></svg>
                                <span>Daftar</span>
                            </button>
                            <button
                                type="button"
                                @click="viewMode = 'photo'"
                                class="inline-flex items-center gap-1.5 px-4 h-9 rounded-full text-xs sm:text-sm font-medium transition-colors cursor-pointer"
                                :class="viewMode === 'photo' ? 'bg-[var(--tinta)] text-[var(--kertas)]' : 'text-[var(--tinta-70)] hover:text-[var(--tinta)]'"
                            >
                                <svg class="w-4 h-4"><use href="#icon-grid"/></svg>
                                <span>Foto</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-0.5 scrollbar-none">
                    <button
                        type="button"
                        @click="selectCategory('all')"
                        class="px-4 h-9 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-colors cursor-pointer"
                        :class="activeCategory === 'all' ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--kertas)]'"
                    >
                        Semua
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            type="button"
                            @click="selectCategory('{{ $cat->slug }}')"
                            class="px-4 h-9 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-colors cursor-pointer"
                            :class="activeCategory === '{{ $cat->slug }}' ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--kertas)]'"
                        >
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Katalog Daftar & Foto --}}
        <section class="pt-10 sm:pt-14 lg:pt-16 pb-28 sm:pb-24 bg-[var(--kertas)] text-[var(--tinta)]">
            <div class="umco-container space-y-14 sm:space-y-16">
                <template x-for="group in groupedCategories()" :key="group.slug">
                    <div :id="'kat-' + group.slug" class="space-y-6">
                        <div class="flex items-baseline justify-between pb-2">
                            <h2 class="type-h2 font-display font-semibold" x-text="group.name"></h2>
                            <span class="font-hand text-2xl text-[var(--mentega-tua)] tabular-nums" x-text="group.items.length + ' pilihan'"></span>
                        </div>

                        {{-- Tampilan Daftar (nota) --}}
                        <div x-show="viewMode === 'list'" class="space-y-2">
                            <template x-for="item in group.items" :key="item.id">
                                <div class="rounded-2xl bg-[var(--kertas-terang)]/75 hover:bg-[var(--kertas-terang)] px-5 py-4 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <a
                                        :href="item.detail_url"
                                        wire:navigate
                                        class="menu-row-trigger flex-1 min-w-0 group"
                                    >
                                        <div class="flex items-baseline gap-3 w-full">
                                            <span
                                                class="menu-row-name text-2xl sm:text-3xl leading-tight group-hover:text-[var(--mentega)] transition-colors"
                                                :class="!item.is_available ? 'line-through decoration-[var(--cabai)] decoration-2 text-[var(--tinta-70)]' : ''"
                                                x-text="item.name"
                                            ></span>
                                            <span x-show="item.is_new" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[var(--aksen-soft)] text-[var(--tinta)]">Baru</span>
                                            <span x-show="!item.is_available" class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[var(--batu)] text-[var(--tinta-70)]">Habis</span>
                                            <span class="hidden sm:block flex-1 border-b border-dotted border-[var(--tinta)]/25"></span>
                                            <span class="font-sans font-semibold text-base sm:text-lg tabular-nums ml-auto shrink-0 text-[var(--mentega-tua)]" x-text="item.price_ribu"></span>
                                        </div>
                                        <p class="mt-1 text-sm text-[var(--tinta-70)] max-w-2xl" x-text="item.ingredients || item.description"></p>
                                    </a>

                                    <button
                                        type="button"
                                        @click="handleNotaClick(item, $event)"
                                        class="self-start sm:self-center shrink-0 rounded-full bg-[var(--batu)] hover:bg-[var(--mentega)] hover:text-white px-4 h-9 text-xs font-semibold transition-colors cursor-pointer"
                                    >
                                        + Nota
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Tampilan Foto (Grid 4/3/2, klik foto/judul -> /menu/{slug}) --}}
                        <div x-show="viewMode === 'photo'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                            <template x-for="item in group.items" :key="item.id">
                                <div class="group flex flex-col justify-between space-y-3">
                                    <div>
                                        <a :href="item.detail_url" wire:navigate class="block space-y-3 focus:outline-none">
                                            <div class="relative rounded-2xl overflow-hidden bg-[var(--batu)]">
                                                <template x-if="!item.is_placeholder && item.image_800">
                                                    <picture class="block w-full aspect-[4/5] overflow-hidden">
                                                        <template x-if="item.srcset_avif">
                                                            <source type="image/avif" :srcset="item.srcset_avif" sizes="(min-width:1024px) 24vw, (min-width:640px) 32vw, 48vw">
                                                        </template>
                                                        <template x-if="item.srcset_webp">
                                                            <source type="image/webp" :srcset="item.srcset_webp" sizes="(min-width:1024px) 24vw, (min-width:640px) 32vw, 48vw">
                                                        </template>
                                                        <img
                                                            :src="item.image_800"
                                                            :alt="item.name"
                                                            loading="lazy"
                                                            decoding="async"
                                                            class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500"
                                                        >
                                                    </picture>
                                                </template>
                                                <template x-if="item.is_placeholder || !item.image_800">
                                                    <div
                                                        class="relative w-full aspect-[4/5] overflow-hidden p-4 flex flex-col justify-between text-[var(--tinta)]"
                                                        :class="item.placeholder_tone"
                                                    >
                                                        <p class="font-display font-extrabold text-[1.5rem] sm:text-[2rem] leading-[0.9] tracking-[-0.04em] -ml-0.5" x-text="item.name"></p>
                                                        <p class="font-hand text-xl text-[var(--tinta)]/70">foto menyusul</p>
                                                    </div>
                                                </template>

                                                <span x-show="item.is_new" class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[var(--mentega)] text-white">Baru</span>
                                                <span x-show="!item.is_available" class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[var(--batu)] text-[var(--tinta-70)]">Habis</span>
                                            </div>

                                            <div class="space-y-1">
                                                <div class="flex items-baseline justify-between gap-2">
                                                    <h3
                                                        class="font-display font-bold text-base sm:text-lg leading-tight group-hover:text-[var(--mentega)] transition-colors"
                                                        :class="!item.is_available ? 'line-through decoration-[var(--cabai)] decoration-2 text-[var(--tinta-70)]' : ''"
                                                        x-text="item.name"
                                                    ></h3>
                                                    <span class="font-sans font-semibold text-sm text-[var(--mentega-tua)] tabular-nums shrink-0" x-text="item.price_ribu"></span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-[var(--tinta-70)] line-clamp-2" x-text="item.ingredients || item.description"></p>
                                            </div>
                                        </a>
                                    </div>

                                    <div class="pt-1 flex items-center justify-between gap-2">
                                        <a :href="item.detail_url" wire:navigate class="text-xs font-semibold text-[var(--tinta-70)] hover:text-[var(--mentega)] transition-colors">
                                            Detail &rarr;
                                        </a>
                                        <button
                                            type="button"
                                            @click="handleNotaClick(item, $event)"
                                            class="rounded-full bg-[var(--batu)] hover:bg-[var(--mentega)] hover:text-white px-3 h-8 text-[11px] font-semibold transition-colors cursor-pointer"
                                        >
                                            + Nota
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="groupedCategories().length === 0" style="display: none;" class="py-16 text-center space-y-4 rounded-2xl bg-[var(--kertas-terang)] p-8">
                    <p class="type-h3 font-display font-bold">Menu tidak ditemukan</p>
                    <p class="text-sm text-[var(--tinta-70)]">Coba kata lain atau tampilkan lagi semua kategori.</p>
                    <button type="button" @click="search = ''; activeCategory = 'all'" class="btn-umco bg-[var(--batu)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-[var(--kertas)]">
                        Tampilkan semua menu
                    </button>
                </div>
            </div>
        </section>

        {{-- Side sheet (Desktop) / Bottom sheet (HP) Tambah Nota Cepat --}}
        <div
            x-show="selectedItem !== null"
            @click.self="selectedItem = null"
            @wheel.self.prevent
            @touchmove.self.prevent
            style="display: none;"
            class="fixed inset-0 z-[60] bg-[var(--tinta)]/60 flex items-end lg:items-stretch lg:justify-end"
            role="dialog"
            aria-modal="true"
        >
            <div
                x-show="selectedItem !== null"
                data-lenis-prevent
                class="w-full lg:w-[440px] rounded-t-3xl lg:rounded-none bg-[var(--kertas-terang)] text-[var(--tinta)] p-6 sm:p-7 max-h-[88svh] lg:max-h-none overflow-y-auto overscroll-contain flex flex-col justify-between gap-6"
            >
                <template x-if="selectedItem">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="font-hand text-xl text-[var(--mentega)]" x-text="selectedItem.category_name"></span>
                            <button type="button" @click="selectedItem = null" class="text-sm font-semibold underline underline-offset-4 cursor-pointer">
                                Tutup
                            </button>
                        </div>

                        <template x-if="!selectedItem.is_placeholder && selectedItem.image_800">
                            <img :src="selectedItem.image_800" :alt="selectedItem.name" class="w-full aspect-[4/3] rounded-2xl object-cover bg-[var(--batu)]">
                        </template>

                        <div class="space-y-2">
                            <div class="flex items-baseline justify-between gap-3">
                                <h3 class="type-h2 font-display font-bold" x-text="selectedItem.name"></h3>
                                <span class="font-display font-bold text-xl tabular-nums shrink-0 text-[var(--mentega-tua)]" x-text="formatRupiah(activeUnitPrice())"></span>
                            </div>
                            <p class="text-sm text-[var(--tinta-70)] leading-relaxed" x-text="selectedItem.ingredients || selectedItem.description"></p>
                            <div class="pt-1">
                                <a
                                    :href="selectedItem.detail_url"
                                    wire:navigate
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--mentega-tua)] underline underline-offset-4 hover:text-[var(--tinta)]"
                                >
                                    <span>Lihat halaman & cerita menu ini</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <template x-if="selectedItem.variants && selectedItem.variants.length">
                            <div class="space-y-2 pt-3">
                                <p class="text-xs font-semibold">Pilih varian</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="(v, idx) in selectedItem.variants" :key="v.label">
                                        <button
                                            type="button"
                                            @click="selectedVariantIdx = idx"
                                            class="px-3.5 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
                                            :class="selectedVariantIdx === idx ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--batu)] text-[var(--tinta)]'"
                                            x-text="v.label + ' · ' + formatRupiah(v.price)"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedItem.has_temperature">
                            <div class="space-y-2 pt-3">
                                <p class="text-xs font-semibold">Penyajian</p>
                                <div class="flex gap-2">
                                    <template x-for="t in ['Es', 'Panas']" :key="t">
                                        <button
                                            type="button"
                                            @click="selectedTemp = t"
                                            class="px-4 h-10 rounded-full text-sm font-semibold transition-colors cursor-pointer"
                                            :class="selectedTemp === t ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--batu)] text-[var(--tinta)]'"
                                            x-text="t"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="selectedItem">
                    <div class="pt-4 flex items-center gap-3">
                        <div class="inline-flex items-center rounded-full bg-[var(--batu)] px-1">
                            <button type="button" @click="selectedQty = Math.max(1, selectedQty - 1)" class="w-10 h-11 font-bold cursor-pointer">-</button>
                            <span class="w-9 text-center font-semibold tabular-nums" x-text="selectedQty"></span>
                            <button type="button" @click="selectedQty = selectedQty + 1" class="w-10 h-11 font-bold cursor-pointer">+</button>
                        </div>

                        <button
                            type="button"
                            @click="addToNota($event)"
                            :disabled="!selectedItem.is_available"
                            class="btn-umco btn-umco-primary flex-1"
                        >
                            <span x-text="selectedItem.is_available ? 'Tambah ke nota' : 'Menu sedang habis'"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-layouts.public>
