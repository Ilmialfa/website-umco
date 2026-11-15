@php
    $notaCats = $menuCategories->map(fn ($cat) => [
        'slug' => $cat->slug,
        'name' => $cat->name,
        'items' => $cat->items->map(fn ($item) => [
            'id' => $item->id,
            'slug' => $item->slug,
            'name' => $item->name,
            'price' => $item->price,
            'ingredients' => $item->ingredients ?: $item->description,
            'available' => (bool) $item->is_available,
        ])->values()->all(),
    ])->filter(fn ($c) => count($c['items']) > 0)->values()->all();
@endphp

<section
    id="menu-nota"
    data-nav-theme="light"
    x-data="{
        categories: @js($notaCats),
        activeTab: @js($notaCats[0]['slug'] ?? ''),
        openIng: {},
        nota: [],
        sheetOpen: false,
        init() {
            this.nota = window.umcoFormatters.loadNota();
        },
        toggleIng(id) {
            this.openIng[id] = !this.openIng[id];
        },
        add(item) {
            if (!item.available) return;
            const found = this.nota.find(r => r.id === item.id);
            if (found) {
                found.qty += 1;
            } else {
                this.nota.push({ id: item.id, slug: item.slug, name: item.name, price: item.price, qty: 1 });
            }
            window.umcoFormatters.saveNota(this.nota);
        },
        dec(id) {
            const idx = this.nota.findIndex(r => r.id === id);
            if (idx === -1) return;
            if (this.nota[idx].qty > 1) {
                this.nota[idx].qty -= 1;
            } else {
                this.nota.splice(idx, 1);
            }
            window.umcoFormatters.saveNota(this.nota);
        },
        totalQty() {
            return this.nota.reduce((s, r) => s + r.qty, 0);
        },
        totalAmount() {
            return this.nota.reduce((s, r) => s + (r.price * r.qty), 0);
        },
        terbilangText() {
            return window.umcoFormatters.terbilang(this.totalAmount());
        },
        formatRibu(v) {
            return window.umcoFormatters.formatRibu(v);
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
    class="umco-section bg-[var(--batu)] text-[var(--tinta)]"
>
    <div class="umco-container space-y-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="space-y-2 max-w-2xl">
                <h2 data-reveal="rise" class="type-h2 font-display font-semibold">
                    {{ $section?->heading ?: 'Pilih duluan sebelum datang.' }}
                </h2>
                <p class="type-body text-[var(--tinta-70)]">
                    {{ $section?->subheading ?: 'Ketuk nama menu, isiannya turun langsung ke nota.' }}
                </p>
            </div>
            <a href="{{ url('/menu') }}" wire:navigate class="inline-flex items-center gap-2 font-semibold text-[var(--tinta)] underline underline-offset-4 hover:text-[var(--mentega-tua)] shrink-0">
                <span>{{ $section?->cta_label ?: 'Lihat semua menu' }}</span>
                <svg class="w-4 h-4"><use href="#icon-arrow-right"/></svg>
            </a>
        </div>

        {{-- Tab kategori HP (<640px) --}}
        <div class="flex sm:hidden gap-2 overflow-x-auto pb-1 scrollbar-none">
            <template x-for="cat in categories" :key="cat.slug">
                <button
                    type="button"
                    @click="activeTab = cat.slug"
                    class="px-4 h-10 rounded-full text-sm font-semibold whitespace-nowrap transition-colors"
                    :class="activeTab === cat.slug ? 'bg-[var(--tinta)] text-[var(--kertas)]' : 'bg-[var(--kertas-terang)] text-[var(--tinta)]'"
                    x-text="cat.name"
                ></button>
            </template>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            {{-- Kiri: Daftar menu bergaya nota --}}
            <div class="lg:col-span-7 space-y-10">
                <template x-for="cat in categories" :key="cat.slug">
                    <div x-show="window.innerWidth >= 640 || activeTab === cat.slug" class="space-y-3">
                        <p class="text-sm font-semibold text-[var(--tinta)]/75 border-b border-[var(--tinta)]/25 pb-2" x-text="cat.name"></p>

                        <div class="divide-y divide-[var(--tinta)]/15">
                            <template x-for="item in cat.items" :key="item.id">
                                <div class="py-3.5">
                                    <div class="flex items-baseline gap-3">
                                        <button
                                            type="button"
                                            @click="add(item)"
                                            :disabled="!item.available"
                                            class="menu-row-trigger text-left flex-1 flex items-baseline gap-3 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span
                                                class="menu-row-name text-[1.375rem] sm:text-[1.75rem] leading-tight"
                                                :class="!item.available ? 'line-through decoration-[var(--cabai)] decoration-2' : ''"
                                                x-text="item.name"
                                            ></span>
                                            <span class="hidden sm:block flex-1 border-b border-dotted border-[var(--tinta)]/40"></span>
                                        </button>

                                        <span class="font-sans font-semibold text-sm sm:text-base tabular-nums whitespace-nowrap" x-text="formatRibu(item.price)"></span>
                                    </div>

                                    <div class="mt-1 flex items-center gap-4 text-xs text-[var(--tinta)]/75">
                                        <button
                                            type="button"
                                            @click="toggleIng(item.id)"
                                            class="underline underline-offset-4 hover:text-[var(--tinta)] cursor-pointer"
                                            x-text="openIng[item.id] ? 'Tutup' : 'Keterangan'"
                                        ></button>
                                        <span x-show="!item.available" class="text-[var(--cabai)] font-semibold">Habis</span>
                                    </div>

                                    <p
                                        x-show="openIng[item.id]"
                                        x-transition
                                        class="mt-2 text-sm text-[var(--tinta)]/85 max-w-lg"
                                        x-text="item.ingredients"
                                    ></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Kanan Desktop: Nota lembut bergaris halus --}}
            <div class="hidden lg:block lg:col-span-5 lg:sticky lg:top-24 space-y-4">
                <div class="rounded-2xl bg-[var(--kertas-terang)] text-[var(--tinta)] border border-[var(--garis)] p-6 sm:p-7 space-y-4">
                    <div class="flex items-baseline justify-between">
                        <span class="font-display font-semibold text-lg tracking-wide">NOTA PILIHAN</span>
                        <span class="font-sans font-semibold text-xs text-[var(--mentega-tua)] tabular-nums">No. 000127</span>
                    </div>

                    <div class="text-xs space-y-1 border-b border-[var(--tinta)] pb-3">
                        <p>Tanggal: <span class="font-hand text-xl ml-1">{{ now()->format('d/m/y') }}</span></p>
                        <p>Pemesan: <span class="font-hand text-xl ml-1">kamu</span></p>
                    </div>

                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-[var(--tinta)] text-xs text-left">
                                <th class="py-1.5 w-12 font-semibold">Banyak</th>
                                <th class="py-1.5 font-semibold">Nama barang</th>
                                <th class="py-1.5 text-right font-semibold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--tinta)]/20">
                            <template x-for="row in nota" :key="row.id">
                                <tr>
                                    <td class="py-2 font-hand text-2xl leading-none" x-text="row.qty"></td>
                                    <td class="py-2 pr-2">
                                        <span class="font-hand text-2xl leading-none" x-text="row.name"></span>
                                        <button
                                            type="button"
                                            @click="dec(row.id)"
                                            class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full border border-[var(--tinta)]/40 text-xs hover:bg-[var(--tinta)] hover:text-[var(--kertas)] cursor-pointer"
                                            title="Kurangi 1"
                                        >
                                            -
                                        </button>
                                    </td>
                                    <td class="py-2 text-right font-hand text-2xl leading-none tabular-nums" x-text="(row.price * row.qty).toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                            <tr x-show="nota.length === 0">
                                <td colspan="3" class="py-6 text-center text-xs text-[var(--tinta-70)]">
                                    Ketuk menu di sebelah kiri untuk menulis nota.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="pt-2 border-t border-[var(--tinta)] flex items-baseline justify-between font-semibold">
                        <span class="text-sm">Jumlah</span>
                        <span class="text-lg tabular-nums" x-text="formatRupiah(totalAmount())"></span>
                    </div>

                    <div class="pt-1 text-xs text-[var(--tinta-70)]">
                        <span>Terbilang:</span>
                        <p class="font-hand text-xl text-[var(--tinta)] mt-0.5 leading-snug" x-text="terbilangText()"></p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a :href="reserveUrl()" wire:navigate class="btn-umco btn-umco-secondary shrink-0">
                        Sobek nota
                    </a>
                    <p class="text-xs text-[var(--tinta)]/80 leading-relaxed">
                        Isi nota ikut tercetak di karcis mejamu. Pembayaran tetap mengikuti minimal belanja.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablet & HP: Strip nota ringkas --}}
    <div x-show="totalQty() > 0" class="lg:hidden mt-8" style="display: none;">
        <div class="umco-container">
            <div class="rounded-2xl bg-[var(--kertas-terang)] border border-[var(--garis)] px-4 py-3 flex items-center justify-between gap-3">
                <button type="button" @click="sheetOpen = true" class="text-left flex-1">
                    <p class="text-sm font-semibold tabular-nums">
                        <span x-text="totalQty() + ' item'"></span> · <span x-text="formatRupiah(totalAmount())"></span>
                    </p>
                    <p class="text-xs text-[var(--tinta-70)] underline underline-offset-4">Lihat rincian nota</p>
                </button>
                <a :href="reserveUrl()" wire:navigate class="btn-umco btn-umco-secondary min-h-0 h-10 px-4 text-sm">
                    Sobek nota
                </a>
            </div>
        </div>
    </div>

    {{-- Bottom sheet nota HP/Tablet --}}
    <div
        x-show="sheetOpen"
        @click.self="sheetOpen = false"
        style="display: none;"
        class="lg:hidden fixed inset-0 z-50 bg-[var(--tinta)]/60 flex items-end justify-center p-3"
    >
        <div class="w-full max-w-lg rounded-2xl bg-[var(--kertas-terang)] p-5 space-y-4 max-h-[82svh] overflow-y-auto">
            <div class="flex items-center justify-between">
                <span class="font-display font-bold text-xl">Nota pilihanmu</span>
                <button type="button" @click="sheetOpen = false" class="text-sm font-semibold underline underline-offset-4">Tutup</button>
            </div>
            <div class="divide-y divide-[var(--garis)]">
                <template x-for="row in nota" :key="row.id">
                    <div class="py-2.5 flex items-center justify-between gap-3 text-sm">
                        <div>
                            <p class="font-semibold" x-text="row.name"></p>
                            <p class="text-xs text-[var(--tinta-70)]" x-text="formatRupiah(row.price)"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="dec(row.id)" class="w-8 h-8 rounded-full border border-[var(--tinta)]">-</button>
                            <span class="w-6 text-center tabular-nums font-semibold" x-text="row.qty"></span>
                            <button type="button" @click="add({id: row.id, slug: row.slug, name: row.name, price: row.price, available: true})" class="w-8 h-8 rounded-full border border-[var(--tinta)]">+</button>
                        </div>
                    </div>
                </template>
            </div>
            <div class="pt-3 border-t border-[var(--tinta)] flex justify-between items-baseline">
                <span class="font-semibold">Total</span>
                <span class="font-bold text-lg tabular-nums" x-text="formatRupiah(totalAmount())"></span>
            </div>
            <a :href="reserveUrl()" wire:navigate class="btn-umco btn-umco-primary w-full">
                Lanjut pesan meja dengan nota ini
            </a>
        </div>
    </div>
</section>
