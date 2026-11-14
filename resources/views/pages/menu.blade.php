<x-layouts.public
    page="menu"
    initial-nav-theme="light"
    title="Menu — UMCO"
    description="Daftar lengkap 39 menu kopi hitam, kopi susu, susu, teh, mocktail, makanan, camilan, dan roti bakar UMCO di Kampus 2 UMRI Pekanbaru."
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

            addToNota() {
                if (!this.selectedItem || !this.selectedItem.is_available) return;
                const variantLabel = this.activeVariantLabel();
                const temp = this.selectedItem.has_temperature ? this.selectedTemp : null;
                const price = this.activeUnitPrice();
                const key = [this.selectedItem.slug, variantLabel, temp].filter(Boolean).join('|');

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
                if (!this.nota.length) return '{{ url('/reservasi') }}';
                const encoded = this.nota.map(r => r.slug + ':' + r.qty).join(',');
                return '{{ url('/reservasi') }}?items=' + encodeURIComponent(encoded);
            }
        }"
        @keydown.escape.window="selectedItem = null; notaOpen = false"
    >
        {{-- Hero pendek --}}
        <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-10 sm:pb-14">
            <div class="umco-container">
                <div class="flex flex-wrap items-baseline gap-4">
                    <h1 data-reveal="chars-wdth" class="type-display font-display font-extrabold">
                        {{ $hero?->heading ?: 'Menu' }}
                    </h1>
                    <span class="font-led text-base sm:text-xl text-[var(--cabai)]">{{ $itemsCount }} item</span>
                </div>
                <p data-reveal="fade" class="type-body text-[var(--tinta-70)] mt-3 max-w-xl">
                    {{ $hero?->subheading ?: 'Harga sudah termasuk senyum kasir.' }}
                </p>
            </div>
        </section>

        {{-- Bar kontrol sticky --}}
        <section class="sticky top-14 sm:top-16 z-30 bg-[var(--kertas-terang)] border-y border-[var(--garis)] py-3.5">
            <div class="umco-container space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="relative flex-1 sm:max-w-xs">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Cari kopi, nasi, camilan..."
                            class="w-full h-11 pl-10 pr-9 bg-[var(--kertas)] border border-[var(--garis)] text-sm text-[var(--tinta)] placeholder:text-[var(--tinta-40)] focus:outline-none focus:border-[var(--tinta)]"
                        >
                        <svg class="w-4 h-4 text-[var(--tinta-40)] absolute left-3.5 top-3.5 pointer-events-none"><use href="#icon-search"/></svg>
                        <button
                            type="button"
                            x-show="search.length > 0"
                            @click="search = ''"
                            style="display: none;"
                            aria-label="Hapus pencarian"
                            class="absolute right-2.5 top-2.5 w-6 h-6 flex items-center justify-center text-[var(--tinta-70)] cursor-pointer"
                        >
                            <svg class="w-4 h-4"><use href="#icon-x"/></svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <span class="text-xs text-[var(--tinta-70)] sm:hidden">Tampilan:</span>
                        <div class="inline-flex border border-[var(--garis)] bg-[var(--kertas)]">
                            <button
                                type="button"
                                @click="viewMode = 'list'"
                                class="inline-flex items-center gap-1.5 px-3.5 h-10 text-xs sm:text-sm font-medium cursor-pointer"
                                :class="viewMode === 'list' ? 'bg-[var(--tinta)] text-[var(--kertas)]' : 'text-[var(--tinta)]'"
                            >
                                <svg class="w-4 h-4"><use href="#icon-list"/></svg>
                                <span>Daftar</span>
                            </button>
                            <button
                                type="button"
                                @click="viewMode = 'photo'"
                                class="inline-flex items-center gap-1.5 px-3.5 h-10 text-xs sm:text-sm font-medium cursor-pointer"
                                :class="viewMode === 'photo' ? 'bg-[var(--tinta)] text-[var(--kertas)]' : 'text-[var(--tinta)]'"
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
                        class="px-4 h-9 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap cursor-pointer border"
                        :class="activeCategory === 'all' ? 'bg-[var(--tinta)] text-[var(--kertas)] border-[var(--tinta)]' : 'bg-transparent text-[var(--tinta)] border-[var(--garis)] hover:border-[var(--tinta)]'"
                    >
                        Semua
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            type="button"
                            @click="selectCategory('{{ $cat->slug }}')"
                            class="px-4 h-9 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap cursor-pointer border"
                            :class="activeCategory === '{{ $cat->slug }}' ? 'bg-[var(--tinta)] text-[var(--kertas)] border-[var(--tinta)]' : 'bg-transparent text-[var(--tinta)] border-[var(--garis)] hover:border-[var(--tinta)]'"
                        >
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Katalog Daftar & Foto --}}
        <section class="umco-section bg-[var(--kertas)] text-[var(--tinta)]">
            <div class="umco-container space-y-14">
                <template x-for="group in groupedCategories()" :key="group.slug">
                    <div :id="'kat-' + group.slug" class="space-y-6">
                        <div class="flex items-baseline justify-between border-b border-[var(--tinta)]/25 pb-3">
                            <h2 class="type-h2 font-display font-bold" x-text="group.name"></h2>
                            <span class="font-led text-xs text-[var(--tinta-70)]" x-text="group.items.length + ' pilihan'"></span>
                        </div>

                        {{-- Tampilan Daftar (nota) --}}
                        <div x-show="viewMode === 'list'" class="divide-y divide-[var(--garis)]">
                            <template x-for="item in group.items" :key="item.id">
                                <button
                                    type="button"
                                    @click="openDetail(item)"
                                    class="menu-row-trigger w-full py-4 text-left flex flex-col gap-1 cursor-pointer group"
                                >
                                    <div class="flex items-baseline gap-3 w-full">
                                        <span
                                            class="menu-row-name text-2xl sm:text-3xl leading-tight"
                                            :class="!item.is_available ? 'line-through decoration-[var(--cabai)] decoration-2 text-[var(--tinta-70)]' : ''"
                                            x-text="item.name"
                                        ></span>
                                        <span x-show="item.is_new" class="px-2 py-0.5 text-[11px] font-semibold bg-[var(--mentega)] text-[var(--tinta)]">Baru</span>
                                        <span x-show="!item.is_available" class="px-2 py-0.5 text-[11px] font-semibold bg-[var(--blush)] text-[var(--cabai)]">Habis</span>
                                        <span class="hidden sm:block flex-1 border-b border-dotted border-[var(--tinta)]/30"></span>
                                        <span class="font-sans font-semibold text-base sm:text-lg tabular-nums ml-auto shrink-0" x-text="item.price_ribu"></span>
                                    </div>
                                    <p class="text-sm text-[var(--tinta-70)] max-w-2xl" x-text="item.ingredients || item.description"></p>
                                </button>
                            </template>
                        </div>

                        {{-- Tampilan Foto (Grid 4/3/2) --}}
                        <div x-show="viewMode === 'photo'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                            <template x-for="item in group.items" :key="item.id">
                                <button
                                    type="button"
                                    @click="openDetail(item)"
                                    class="text-left group cursor-pointer flex flex-col justify-between space-y-3 focus:outline-none"
                                >
                                    <div>
                                        <div class="relative overflow-hidden">
                                            <template x-if="!item.is_placeholder && item.image_800">
                                                <picture class="block w-full aspect-[4/5] overflow-hidden bg-[var(--garis)]/30">
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
                                                    <p class="text-xs text-[var(--tinta)]/70">foto menyusul</p>
                                                </div>
                                            </template>

                                            <span x-show="item.is_new" class="absolute top-2.5 left-2.5 px-2.5 py-1 text-[11px] font-semibold bg-[var(--mentega)] text-[var(--tinta)]">Baru</span>
                                            <span x-show="!item.is_available" class="absolute top-2.5 right-2.5 px-2.5 py-1 text-[11px] font-semibold bg-[var(--blush)] text-[var(--cabai)]">Habis</span>
                                        </div>

                                        <div class="pt-3 space-y-1">
                                            <div class="flex items-baseline justify-between gap-2">
                                                <h3
                                                    class="font-display font-bold text-base sm:text-lg leading-tight"
                                                    :class="!item.is_available ? 'line-through decoration-[var(--cabai)] decoration-2 text-[var(--tinta-70)]' : ''"
                                                    x-text="item.name"
                                                ></h3>
                                                <span class="font-sans font-semibold text-sm tabular-nums shrink-0" x-text="item.price_ribu"></span>
                                            </div>
                                            <p class="text-xs sm:text-sm text-[var(--tinta-70)] line-clamp-2" x-text="item.ingredients || item.description"></p>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="groupedCategories().length === 0" style="display: none;" class="py-16 text-center space-y-4 border border-[var(--garis)] bg-[var(--kertas-terang)] p-8">
                    <p class="type-h3 font-display font-bold">Menu tidak ditemukan</p>
                    <p class="text-sm text-[var(--tinta-70)]">Coba kata lain atau tampilkan lagi semua kategori.</p>
                    <button type="button" @click="search = ''; activeCategory = 'all'" class="btn-umco btn-umco-secondary">
                        Tampilkan semua menu
                    </button>
                </div>
            </div>
        </section>

        {{-- Bar nota lengket bawah bila sudah memilih menu --}}
        <div
            x-show="notaQty() > 0"
            style="display: none;"
            class="fixed bottom-4 left-4 right-4 lg:left-auto lg:right-6 lg:w-[380px] z-40"
        >
            <div class="bg-[var(--tinta)] text-[var(--kertas)] px-4 py-3 flex items-center justify-between gap-3">
                <button type="button" @click="notaOpen = true" class="text-left flex-1 cursor-pointer">
                    <p class="text-sm font-semibold tabular-nums">
                        <span x-text="notaQty() + ' item di nota'"></span> · <span x-text="formatRupiah(notaTotal())"></span>
                    </p>
                    <p class="text-xs text-[var(--kertas)]/75 underline underline-offset-4">Ubah isi nota</p>
                </button>
                <a :href="reserveUrl()" wire:navigate class="btn-umco btn-umco-primary min-h-0 h-10 px-4 text-sm shrink-0">
                    Pesan meja
                </a>
            </div>
        </div>

        {{-- Side sheet (Desktop) / Bottom sheet (HP) Detail Menu --}}
        <div
            x-show="selectedItem !== null"
            @click.self="selectedItem = null"
            style="display: none;"
            class="fixed inset-0 z-[60] bg-[var(--tinta)]/60 flex items-end lg:items-stretch lg:justify-end"
            role="dialog"
            aria-modal="true"
        >
            <div
                x-show="selectedItem !== null"
                class="w-full lg:w-[440px] bg-[var(--kertas-terang)] text-[var(--tinta)] border-t lg:border-t-0 lg:border-l border-[var(--tinta)] p-6 sm:p-7 max-h-[88svh] lg:max-h-none overflow-y-auto flex flex-col justify-between gap-6"
            >
                <template x-if="selectedItem">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-[var(--tinta-70)]" x-text="selectedItem.category_name"></span>
                            <button type="button" @click="selectedItem = null" class="text-sm font-semibold underline underline-offset-4 cursor-pointer">
                                Tutup
                            </button>
                        </div>

                        <template x-if="!selectedItem.is_placeholder && selectedItem.image_800">
                            <img :src="selectedItem.image_800" :alt="selectedItem.name" class="w-full aspect-[4/3] object-cover bg-[var(--garis)]/30">
                        </template>

                        <div class="space-y-2">
                            <div class="flex items-baseline justify-between gap-3">
                                <h3 class="type-h2 font-display font-bold" x-text="selectedItem.name"></h3>
                                <span class="font-display font-bold text-xl tabular-nums shrink-0" x-text="formatRupiah(activeUnitPrice())"></span>
                            </div>
                            <p class="text-sm text-[var(--tinta-70)]" x-text="selectedItem.ingredients || selectedItem.description"></p>
                        </div>

                        <template x-if="selectedItem.variants && selectedItem.variants.length">
                            <div class="space-y-2 pt-2 border-t border-[var(--garis)]">
                                <p class="text-xs font-semibold">Pilih varian</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="(v, idx) in selectedItem.variants" :key="v.label">
                                        <button
                                            type="button"
                                            @click="selectedVariantIdx = idx"
                                            class="px-3.5 py-2 border text-xs font-semibold cursor-pointer"
                                            :class="selectedVariantIdx === idx ? 'bg-[var(--tinta)] text-[var(--kertas)] border-[var(--tinta)]' : 'bg-[var(--kertas)] border-[var(--garis)]'"
                                            x-text="v.label + ' · ' + formatRupiah(v.price)"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedItem.has_temperature">
                            <div class="space-y-2 pt-2 border-t border-[var(--garis)]">
                                <p class="text-xs font-semibold">Penyajian</p>
                                <div class="flex gap-2">
                                    <template x-for="t in ['Es', 'Panas']" :key="t">
                                        <button
                                            type="button"
                                            @click="selectedTemp = t"
                                            class="px-4 h-10 border text-sm font-semibold cursor-pointer"
                                            :class="selectedTemp === t ? 'bg-[var(--tinta)] text-[var(--kertas)] border-[var(--tinta)]' : 'bg-[var(--kertas)] border-[var(--garis)]'"
                                            x-text="t"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="selectedItem">
                    <div class="pt-4 border-t border-[var(--garis)] flex items-center gap-3">
                        <div class="inline-flex items-center border border-[var(--tinta)]">
                            <button type="button" @click="selectedQty = Math.max(1, selectedQty - 1)" class="w-10 h-11 font-bold cursor-pointer">-</button>
                            <span class="w-9 text-center font-semibold tabular-nums" x-text="selectedQty"></span>
                            <button type="button" @click="selectedQty = selectedQty + 1" class="w-10 h-11 font-bold cursor-pointer">+</button>
                        </div>

                        <button
                            type="button"
                            @click="addToNota()"
                            :disabled="!selectedItem.is_available"
                            class="btn-umco btn-umco-primary flex-1"
                        >
                            <span x-text="selectedItem.is_available ? 'Tambah ke nota' : 'Menu sedang habis'"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Sheet ubah isi nota --}}
        <div
            x-show="notaOpen"
            @click.self="notaOpen = false"
            style="display: none;"
            class="fixed inset-0 z-[60] bg-[var(--tinta)]/60 flex items-end sm:items-center justify-center p-3"
        >
            <div class="w-full max-w-md bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--tinta)] p-6 space-y-4 max-h-[82svh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="font-display font-bold text-xl">Nota pilihanmu</h3>
                    <button type="button" @click="notaOpen = false" class="text-sm font-semibold underline underline-offset-4 cursor-pointer">Tutup</button>
                </div>
                <div class="divide-y divide-[var(--garis)]">
                    <template x-for="(row, idx) in nota" :key="(row.key || row.slug) + idx">
                        <div class="py-3 flex items-center justify-between gap-3 text-sm">
                            <div>
                                <p class="font-semibold" x-text="row.name"></p>
                                <p class="text-xs text-[var(--tinta-70)]" x-text="[row.variant_label, row.temperature, formatRupiah(row.price)].filter(Boolean).join(' · ')"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="decNota(idx)" class="w-8 h-8 border border-[var(--tinta)] cursor-pointer">-</button>
                                <span class="w-6 text-center tabular-nums font-semibold" x-text="row.qty"></span>
                                <button type="button" @click="incNota(idx)" class="w-8 h-8 border border-[var(--tinta)] cursor-pointer">+</button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="pt-3 border-t border-[var(--tinta)] flex justify-between items-baseline">
                    <span class="font-semibold">Total sementara</span>
                    <span class="font-bold text-lg tabular-nums" x-text="formatRupiah(notaTotal())"></span>
                </div>
                <a :href="reserveUrl()" wire:navigate class="btn-umco btn-umco-primary w-full">
                    Lanjut pesan meja →
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
