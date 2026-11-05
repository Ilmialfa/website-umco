<x-layouts.public>
    <x-slot:title>Katalog Menu & Seduhan — UM.CO House Kampus UMRI</x-slot:title>
    <x-slot:description>Daftar lengkap kopi nusantara, espresso bar, artisan tea, rice bowl kampus, dan jajanan street food UM.CO House di Kampus UMRI Pekanbaru. Harga ramah mahasiswa.</x-slot:description>

    <!-- SECTION 1: EDITORIAL HERO -->
    <section class="pt-32 pb-16 md:pt-40 md:pb-20 bg-forest text-alabaster relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none opacity-10">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-crema blur-3xl"></div>
            <div class="absolute bottom-0 left-10 w-80 h-80 rounded-full bg-moss blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold tracking-widest uppercase bg-white/10 text-crema border border-white/15 mb-4">
                Katalog Resmi &amp; Harga Terbuka
            </span>
            <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-alabaster max-w-3xl mx-auto leading-tight">
                Seduhan Kopi Nusantara &amp; Santapan Hangat Kampus
            </h1>
            <p class="mt-4 text-base sm:text-lg text-sage max-w-2xl mx-auto leading-relaxed">
                Dari racikan espresso kalibrasi harian hingga street food malam hari di courtyard terbuka UMRI. Disajikan cepat, higienis, dan ramah di kantong.
            </p>

            <!-- Quick Info Bar -->
            <div class="mt-8 inline-flex flex-wrap items-center justify-center gap-4 sm:gap-8 px-6 py-3 rounded-full bg-white/5 border border-white/10 text-xs sm:text-sm text-alabaster/90">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-crema" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    100% Halal Certified
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-crema" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Oat Milk Available (+5k)
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-crema" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Takeaway &amp; Dine-In Ready
                </span>
            </div>
        </div>
    </section>

    <!-- SECTION 2: INTERACTIVE MENU CATALOG (ALPINE.JS 0-MS FILTERING) -->
    <section
        x-data="menuCatalog()"
        x-init="initCatalog()"
        class="py-12 lg:py-20 bg-oat relative min-h-screen"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- FILTER & SEARCH CONTROL BAR (STICKY DESKTOP) -->
            <div class="sticky top-20 z-30 bg-oat/95 backdrop-blur-md py-4 border-b border-forest/10 mb-10 transition">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">

                    <!-- Search Input -->
                    <div class="relative w-full lg:w-96 flex-shrink-0">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-forest/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Cari kopi, makanan, camilan..."
                            class="w-full pl-10 pr-10 py-2.5 bg-white border border-forest/15 rounded-full text-sm text-forest placeholder:text-forest/40 focus:outline-none focus:ring-2 focus:ring-moss/30 focus:border-moss transition"
                        />
                        <button
                            type="button"
                            x-show="search.length > 0"
                            @click="search = ''"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-forest/40 hover:text-forest"
                            style="display: none;"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Category Pills Horizontal Scroll -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs font-semibold">
                        <button
                            type="button"
                            @click="selectCategory('all')"
                            class="px-4 py-2 rounded-full whitespace-nowrap transition cursor-pointer"
                            :class="activeCategory === 'all' ? 'bg-forest text-alabaster shadow-sm' : 'bg-white text-forest/80 hover:bg-forest/5 border border-forest/10'"
                        >
                            Semua Menu ({{ count($itemsPayload) }})
                        </button>
                        @foreach ($categories as $cat)
                            <button
                                type="button"
                                @click="selectCategory('{{ $cat->slug }}')"
                                class="px-4 py-2 rounded-full whitespace-nowrap transition cursor-pointer"
                                :class="activeCategory === '{{ $cat->slug }}' ? 'bg-forest text-alabaster shadow-sm' : 'bg-white text-forest/80 hover:bg-forest/5 border border-forest/10'"
                            >
                                {{ $cat->name }}
                                <span class="opacity-60 text-[10px]">({{ $cat->items->count() }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- BEST SELLERS SPOTLIGHT (VISIBLE WHEN ALL & NO SEARCH) -->
            <div x-show="activeCategory === 'all' && search === ''" class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <span class="text-xs uppercase font-bold tracking-widest text-moss">Menu Favorit Mahasiswa &amp; Dosen</span>
                        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-forest mt-0.5">Paling Sering Dipesan</h2>
                    </div>
                    <span class="text-xs text-forest/60 hidden sm:inline-block">Kurasi resep terbaik barista UM.CO</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($bestSellers as $item)
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-forest/10 flex flex-col justify-between hover:shadow-md transition group">
                            <div>
                                <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-oat/50 mb-4">
                                    <img
                                        src="{{ $item->image_url }}"
                                        alt="{{ $item->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                        loading="lazy"
                                    />
                                    <span class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase bg-crema text-forest shadow-sm">
                                        Best Seller
                                    </span>
                                    @if (!$item->is_available)
                                        <span class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase bg-rose-600 text-white shadow-sm">
                                            Habis
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-moss">
                                    {{ $item->category?->name }}
                                </span>
                                <h3 class="font-serif text-lg font-bold text-forest mt-0.5">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-xs text-forest/70 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $item->description }}
                                </p>
                            </div>

                            <div class="pt-4 mt-4 border-t border-forest/10 flex items-center justify-between">
                                <span class="font-serif font-bold text-forest text-base">
                                    {{ $item->formatted_price }}
                                </span>
                                @if ($item->is_available)
                                    <a
                                        href="/reservasi?menu={{ $item->slug }}"
                                        wire:navigate
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-forest text-alabaster hover:bg-forest-deep transition"
                                    >
                                        <span>Pesan</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-xs text-rose-500 font-medium">Stok Habis</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- DETAILED MENU LIST WITH CLASSIC DOT-LEADER STYLING -->
            <div class="space-y-12">
                <!-- Loop dynamically through visible categorized items via Alpine -->
                <template x-for="catGroup in categorizedItems()" :key="catGroup.slug">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 shadow-sm border border-forest/10">
                        <!-- Category Header -->
                        <div class="border-b border-forest/10 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-forest" x-text="catGroup.name"></h2>
                                <p class="text-xs text-forest/60 mt-0.5" x-text="catGroup.items.length + ' pilihan menu'"></p>
                            </div>
                            <span class="text-xs font-medium text-moss uppercase tracking-wider hidden sm:inline-block">UM.CO Fresh Selection</span>
                        </div>

                        <!-- Dot-Leader Menu Items Grid (2 cols on large screens) -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                            <template x-for="item in catGroup.items" :key="item.id">
                                <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-oat/40 transition group">
                                    <!-- Mini Thumbnail -->
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-oat overflow-hidden flex-shrink-0 relative">
                                        <img
                                            :src="item.image_url"
                                            :alt="item.name"
                                            class="w-full h-full object-cover group-hover:scale-105 transition"
                                            loading="lazy"
                                        />
                                        <template x-if="!item.is_available">
                                            <div class="absolute inset-0 bg-charcoal/60 flex items-center justify-center">
                                                <span class="text-[9px] font-bold uppercase text-white bg-rose-600 px-1 py-0.5 rounded">Habis</span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Details & Dot Leader -->
                                    <div class="flex-grow min-w-0">
                                        <div class="flex items-baseline justify-between gap-2">
                                            <!-- Title & Badges -->
                                            <div class="flex items-center gap-2 truncate">
                                                <h3
                                                    class="font-serif font-bold text-forest text-base truncate group-hover:text-moss transition"
                                                    :class="!item.is_available ? 'line-through opacity-60' : ''"
                                                    x-text="item.name"
                                                ></h3>
                                                <template x-if="item.is_favorite">
                                                    <span class="flex-shrink-0 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-crema/80 text-forest">Fav</span>
                                                </template>
                                            </div>

                                            <!-- Dot Leader Line -->
                                            <div class="flex-grow border-b border-dotted border-forest/30 mx-2 hidden sm:block"></div>

                                            <!-- Price -->
                                            <div class="flex-shrink-0 text-right">
                                                <span class="font-serif font-bold text-forest text-sm sm:text-base whitespace-nowrap" x-text="item.price_formatted"></span>
                                            </div>
                                        </div>

                                        <!-- Description & Meta -->
                                        <p class="text-xs text-forest/70 mt-1 line-clamp-2 leading-relaxed" x-text="item.description"></p>

                                        <!-- Tags & CTA Action -->
                                        <div class="mt-2 flex items-center justify-between text-[11px]">
                                            <span class="text-moss font-medium" x-text="item.allergens ? 'Catatan: ' + item.allergens : ''"></span>
                                            <template x-if="item.is_available">
                                                <a
                                                    :href="'/reservasi?menu=' + item.slug"
                                                    wire:navigate
                                                    class="inline-flex items-center gap-1 font-semibold text-moss hover:text-forest transition"
                                                >
                                                    <span>Pesan dengan Meja</span>
                                                    <span>&rarr;</span>
                                                </a>
                                            </template>
                                            <template x-if="!item.is_available">
                                                <span class="text-rose-500 font-semibold">Sedang Tidak Tersedia</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- EMPTY STATE SEARCH -->
                <div x-show="categorizedItems().length === 0" class="text-center py-20 bg-white rounded-3xl border border-forest/10 p-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center text-forest/40">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-forest">Menu tidak ditemukan</h3>
                    <p class="text-sm text-forest/60 mt-1 max-w-sm mx-auto">
                        Tidak ada menu yang sesuai dengan kata kunci "<span class="font-semibold text-forest" x-text="search"></span>".
                    </p>
                    <button
                        type="button"
                        @click="search = ''; activeCategory = 'all'"
                        class="mt-6 px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider bg-forest text-alabaster hover:bg-forest-deep transition cursor-pointer"
                    >
                        Tampilkan Semua Menu
                    </button>
                </div>
            </div>

            <!-- BOTTOM CTA BANNER: RESERVASI MEJA & PRE-ORDER -->
            <div class="mt-16 bg-forest text-alabaster rounded-3xl p-8 sm:p-12 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-xl">
                    <span class="text-xs uppercase tracking-widest text-crema font-bold">Hemat Waktu Saat Istirahat Kuliah</span>
                    <h3 class="font-serif text-2xl sm:text-3xl font-bold mt-1 text-alabaster">
                        Pesan Menu Sekaligus Amankan Meja Sebelum Tiba
                    </h3>
                    <p class="text-sm text-sage mt-2 leading-relaxed">
                        Pilih Mode Reservasi B untuk memesan minuman dan makanan terlebih dahulu dengan DP 30%. Pesanan siap tersaji saat Anda sampai di lokasi.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                    <a
                        href="/reservasi?mode=b"
                        wire:navigate
                        class="w-full sm:w-auto text-center px-6 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-crema text-forest hover:bg-crema-dark transition shadow-md"
                    >
                        Reservasi Meja + Pre-Order Menu
                    </a>
                    @php
                        $waOrderLink = \App\Support\PhoneNumber::waLink(
                            \App\Support\Settings::get('contact.phone_wa', '082248001571'),
                            'Halo UM.CO House, saya ingin menanyakan ketersediaan menu untuk pesan antar di area Kampus UMRI.'
                        );
                    @endphp
                    <a
                        href="{{ $waOrderLink }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full sm:w-auto text-center px-6 py-3.5 rounded-full text-xs font-bold tracking-wide border border-white/30 text-alabaster hover:bg-white/10 transition"
                    >
                        Chat Kasir WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- CLIENT SCRIPT FOR ZERO-LATENCY SEARCH & FILTER -->
    <script>
    function menuCatalog() {
        return {
            items: @json($itemsPayload),
            categories: @json($categories),
            activeCategory: 'all',
            search: '',

            initCatalog() {
                // Read category from URL hash if present (e.g. #signature-coffee)
                const hash = window.location.hash.replace('#', '');
                if (hash) {
                    const match = this.categories.find(c => c.slug === hash);
                    if (match) {
                        this.activeCategory = match.slug;
                    }
                }
            },

            selectCategory(slug) {
                this.activeCategory = slug;
                if (slug === 'all') {
                    history.replaceState(null, null, ' ');
                } else {
                    window.location.hash = slug;
                }
            },

            categorizedItems() {
                const s = this.search.toLowerCase().trim();
                const currentCat = this.activeCategory;

                // Group items by category
                return this.categories.map(cat => {
                    // Filter items matching active category and search
                    const catItems = this.items.filter(item => {
                        const inCategory = (currentCat === 'all' || item.category_slug === currentCat);
                        const matchCatId = (item.category_id === cat.id);
                        if (!matchCatId || !inCategory) return false;

                        if (!s) return true;
                        const matchName = item.name.toLowerCase().includes(s);
                        const matchDesc = (item.description || '').toLowerCase().includes(s);
                        const matchTags = (item.tags || []).some(t => t.toLowerCase().includes(s));
                        return matchName || matchDesc || matchTags;
                    });

                    return {
                        name: cat.name,
                        slug: cat.slug,
                        items: catItems
                    };
                }).filter(group => group.items.length > 0);
            }
        };
    }
    </script>
</x-layouts.public>
