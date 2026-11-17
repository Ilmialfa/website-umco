@php
    $brandShort = \App\Support\Settings::get('brand.brand_short') ?: 'UMCO';
    $eyebrow = $hero?->data['eyebrow'] ?? 'Galeri Foto';
    $heading = $hero?->heading ?: 'Potret keseharian di UMCO';
    $subheading = $hero?->subheading ?: 'Sudut meja kerja, racikan hangat pagi hari, dan momen kumpul di tepi Kampus 2 UMRI.';

    $photosPayload = $items->map(function ($g) {
        $p = (string) ($g->image_path ?? '');
        $src = str_starts_with($p, 'http') || str_starts_with($p, '/')
            ? asset($p)
            : asset('storage/' . ltrim($p, '/'));

        $groupLabel = match ($g->group) {
            'kopi' => 'Kopi & Minuman',
            'makanan' => 'Makanan & Camilan',
            'suasana' => 'Suasana Cafe',
            default => ucfirst((string) ($g->group ?: 'Umum')),
        };

        return [
            'id' => $g->id,
            'src' => $src,
            'caption' => $g->caption ?: 'Momen di UMCO House',
            'group' => $g->group ?: 'suasana',
            'group_label' => $groupLabel,
            'orientation' => $g->orientation ?: 'mixed',
        ];
    })->values()->all();

    $filterTabs = $groups->map(function ($grp) {
        return [
            'slug' => $grp,
            'label' => match ($grp) {
                'kopi' => 'Kopi & Minuman',
                'makanan' => 'Makanan & Camilan',
                'suasana' => 'Suasana Cafe',
                default => ucfirst((string) $grp),
            },
        ];
    })->values()->all();
@endphp

<x-layouts.public
    page="galeri"
    initial-nav-theme="light"
    :title="$hero?->data['meta_title'] ?? ('Galeri — ' . $brandShort)"
    :description="$hero?->data['meta_description'] ?? $subheading"
>
    <div
        x-data="{
            photos: @js($photosPayload),
            tabs: @js($filterTabs),
            activeGroup: 'all',
            lightbox: null,
            filtered() {
                if (this.activeGroup === 'all') return this.photos;
                return this.photos.filter(p => p.group === this.activeGroup);
            }
        }"
        class="bg-[var(--kertas)] text-[var(--tinta)] min-h-[70svh]"
    >
        {{-- Pembuka Galeri --}}
        <section data-nav-theme="light" class="pt-28 sm:pt-36 pb-8 sm:pb-12">
            <div class="umco-container">
                <div class="max-w-2xl space-y-3">
                    <p data-reveal="fade" class="font-hand text-2xl sm:text-3xl text-[var(--mentega)]">
                        {{ $eyebrow }}
                    </p>
                    <h1 data-reveal="rise" class="type-display font-display font-bold">
                        {{ $heading }}
                    </h1>
                    <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-xl">
                        {{ $subheading }}
                    </p>
                </div>

                {{-- Filter Kelompok Foto --}}
                <div data-reveal="fade" class="mt-8 flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="activeGroup = 'all'"
                        class="px-4 h-9 rounded-full text-xs sm:text-sm font-semibold transition-colors cursor-pointer"
                        :class="activeGroup === 'all' ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--batu)]'"
                    >
                        Semua (<span x-text="photos.length"></span>)
                    </button>
                    <template x-for="tab in tabs" :key="tab.slug">
                        <button
                            type="button"
                            @click="activeGroup = tab.slug"
                            class="px-4 h-9 rounded-full text-xs sm:text-sm font-semibold transition-colors cursor-pointer"
                            :class="activeGroup === tab.slug ? 'bg-[var(--mentega)] text-white' : 'bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--batu)]'"
                            x-text="tab.label"
                        ></button>
                    </template>
                </div>
            </div>
        </section>

        {{-- Grid Galeri Bebas Rasio --}}
        <section data-nav-theme="light" class="pb-16 sm:pb-24">
            <div class="umco-container">
                <div data-reveal-stagger class="columns-1 sm:columns-2 lg:columns-3 gap-5 sm:gap-6 space-y-5 sm:space-y-6">
                    <template x-for="item in filtered()" :key="item.id + '-' + activeGroup">
                        <figure class="break-inside-avoid group space-y-2.5">
                            <button
                                type="button"
                                @click="lightbox = item"
                                class="block w-full rounded-2xl overflow-hidden bg-[var(--batu)] cursor-zoom-in text-left focus:outline-none"
                            >
                                <img
                                    :src="item.src"
                                    :alt="item.caption"
                                    loading="lazy"
                                    decoding="async"
                                    class="w-full h-auto block object-cover group-hover:scale-[1.03] transition-transform duration-500"
                                >
                            </button>
                            <figcaption class="flex items-baseline justify-between gap-3 px-1">
                                <span class="text-sm font-medium text-[var(--tinta)]" x-text="item.caption"></span>
                                <span class="font-hand text-xl text-[var(--mentega-tua)] shrink-0" x-text="item.group_label"></span>
                            </figcaption>
                        </figure>
                    </template>
                </div>

                <div x-show="filtered().length === 0" style="display: none;" class="rounded-2xl bg-[var(--kertas-terang)] p-10 text-center space-y-3">
                    <p class="type-h3 font-display font-bold">Belum ada foto di kategori ini</p>
                    <button type="button" @click="activeGroup = 'all'" class="btn-umco bg-[var(--batu)] text-[var(--tinta)]">
                        Lihat semua foto
                    </button>
                </div>
            </div>
        </section>

        {{-- Ajakan Singgah & Reservasi --}}
        <section data-nav-theme="light" class="bg-[var(--batu)]/65 py-14 sm:py-18">
            <div class="umco-container flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="space-y-2 max-w-xl">
                    <p class="font-hand text-2xl text-[var(--mentega)]">Mau duduk di sudut favoritmu?</p>
                    <h2 class="type-h2 font-display font-bold">Pesan meja sebelum datang ke cafe.</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/reservasi') }}" wire:navigate.hover class="btn-umco btn-umco-primary">
                        <span>Pesan meja</span>
                    </a>
                    <a href="{{ url('/menu') }}" wire:navigate.hover class="btn-umco bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--tinta)] hover:text-[var(--kertas)]">
                        <span>Lihat daftar menu</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- Lightbox Preview --}}
        <div
            x-show="lightbox !== null"
            @click.self="lightbox = null"
            @keydown.escape.window="lightbox = null"
            style="display: none;"
            class="fixed inset-0 z-[70] bg-[#141916]/85 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <template x-if="lightbox">
                <div class="max-w-3xl w-full rounded-2xl overflow-hidden bg-[var(--kertas-terang)] text-[var(--tinta)]">
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div>
                            <p class="font-hand text-2xl leading-none text-[var(--mentega)]" x-text="lightbox.group_label"></p>
                            <p class="text-sm font-semibold mt-0.5" x-text="lightbox.caption"></p>
                        </div>
                        <button type="button" @click="lightbox = null" class="rounded-full bg-[var(--batu)] px-4 h-9 text-xs font-semibold cursor-pointer">
                            Tutup
                        </button>
                    </div>
                    <img :src="lightbox.src" :alt="lightbox.caption" class="w-full max-h-[75svh] object-contain bg-[var(--kertas)]">
                </div>
            </template>
        </div>
    </div>
</x-layouts.public>
