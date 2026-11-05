<x-layouts.public>
    <x-slot:title>Katalog Menu & Seduhan — UM.CO House Kampus UMRI</x-slot:title>
    <x-slot:description>Daftar lengkap kopi nusantara, artisan tea, rice bowl, dan jajanan street food UM.CO House di Kampus UMRI Pekanbaru.</x-slot:description>

    <!-- SECTION 1: HERO HEADER WITH EMERALD & ORGANIC CURVE -->
    <section class="pt-32 pb-20 md:pt-40 md:pb-24 bg-[#0D3B2E] text-white relative overflow-hidden text-center">
        <!-- Ambient Warm Glow -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-[#F5B82E] blur-3xl"></div>
            <div class="absolute bottom-0 left-10 w-80 h-80 rounded-full bg-[#145341] blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-4">
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-white/10 text-[#F5B82E]">
                Katalog Menu Resmi
            </span>
            <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                Seduhan Kopi &amp; Santapan Kampus
            </h1>
            <p class="text-sm sm:text-base text-[#9AB4A6] max-w-xl mx-auto leading-relaxed">
                Dari racikan espresso kalibrasi harian hingga santapan hangat dan street food sore hari di pelataran UMRI.
            </p>

            <div class="pt-2 flex flex-wrap items-center justify-center gap-4 text-xs text-white/80">
                <span class="inline-flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full">
                    <span class="text-[#F5B82E]">✓</span> 100% Halal
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full">
                    <span class="text-[#F5B82E]">✓</span> Susu Oat Nabati (+5k)
                </span>
                <span class="inline-flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full">
                    <span class="text-[#F5B82E]">✓</span> Dine-in &amp; Takeaway
                </span>
            </div>
        </div>

        <!-- ORGANIC WAVE DIVIDER -->
        <div class="absolute bottom-0 left-0 right-0 leading-none overflow-hidden select-none pointer-events-none">
            <svg class="relative block w-full h-12 sm:h-16 text-[#FAF7EE]" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0 C150,90 350,-40 500,40 C650,120 900,20 1200,60 L1200,120 L0,120 Z" fill="currentColor"></path>
            </svg>
        </div>
    </section>

    <!-- SECTION 2: INTERACTIVE MENU CATALOG (ALPINE.JS 0-MS FILTERING) -->
    <section
        x-data="menuCatalog()"
        x-init="initCatalog()"
        class="py-12 lg:py-20 bg-[#FAF7EE] relative min-h-screen"
    >
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- FILTER & SEARCH BAR (CLEAN, NO SHADOW, NO HEAVY BORDER) -->
            <div class="sticky top-20 z-30 bg-[#FAF7EE]/95 backdrop-blur-md py-4 mb-10 transition">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">

                    <!-- Search Input -->
                    <div class="relative w-full lg:w-80 flex-shrink-0">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Cari kopi, makanan..."
                            class="w-full pl-10 pr-10 py-2.5 bg-white rounded-full text-xs sm:text-sm text-[#0D3B2E] placeholder:text-[#0D3B2E]/40 focus:outline-none focus:ring-2 focus:ring-[#F5B82E] transition"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#0D3B2E]/40">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button
                            type="button"
                            x-show="search.length > 0"
                            @click="search = ''"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#0D3B2E]/40 hover:text-[#0D3B2E]"
                            style="display: none;"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Category Pills -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs font-semibold">
                        <button
                            type="button"
                            @click="selectCategory('all')"
                            class="px-4 py-2 rounded-full whitespace-nowrap transition cursor-pointer"
                            :class="activeCategory === 'all' ? 'bg-[#0D3B2E] text-white' : 'bg-white text-[#0D3B2E] hover:bg-white/80'"
                        >
                            Semua Menu ({{ count($itemsPayload) }})
                        </button>
                        @foreach ($categories as $cat)
                            <button
                                type="button"
                                @click="selectCategory('{{ $cat->slug }}')"
                                class="px-4 py-2 rounded-full whitespace-nowrap transition cursor-pointer"
                                :class="activeCategory === '{{ $cat->slug }}' ? 'bg-[#0D3B2E] text-white' : 'bg-white text-[#0D3B2E] hover:bg-white/80'"
                            >
                                {{ $cat->name }}
                                <span class="opacity-60 text-[10px]">({{ $cat->items->count() }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- BEST SELLERS SPOTLIGHT (WHEN NO SEARCH & ON 'ALL') -->
            <div x-show="activeCategory === 'all' && search === ''" class="mb-16">
                <div class="mb-6">
                    <span class="text-xs uppercase font-bold tracking-widest text-[#145341]">Pilihan Barista</span>
                    <h2 class="font-serif text-2xl sm:text-3xl font-black text-[#0D3B2E]">Menu Paling Favorit</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($bestSellers as $item)
                        <div class="bg-white rounded-3xl p-4 flex flex-col justify-between hover:bg-[#F4EEDF]/50 transition group">
                            <div>
                                <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-[#FAF7EE] mb-4">
                                    <img
                                        src="{{ $item->image_url }}"
                                        alt="{{ $item->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                        loading="lazy"
                                    />
                                    <span class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C]">
                                        Best Seller
                                    </span>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#145341]">
                                    {{ $item->category?->name }}
                                </span>
                                <h3 class="font-serif text-lg font-bold text-[#0D3B2E] mt-0.5">{{ $item->name }}</h3>
                                <p class="text-xs text-[#122018]/70 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $item->description }}
                                </p>
                            </div>

                            <div class="pt-4 mt-4 border-t border-[#EAE2D0] flex items-center justify-between">
                                <span class="font-serif font-black text-base text-[#0D3B2E]">
                                    {{ $item->formatted_price }}
                                </span>
                                <a
                                    href="/reservasi?menu={{ $item->slug }}"
                                    wire:navigate
                                    class="px-4 py-1.5 rounded-full text-xs font-bold bg-[#0D3B2E] text-white hover:bg-[#07241C] transition"
                                >
                                    Pesan
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- CATEGORIZED MENU ITEMS LIST -->
            <div class="space-y-10">
                <template x-for="catGroup in categorizedItems()" :key="catGroup.slug">
                    <div class="bg-white rounded-3xl p-6 sm:p-8">
                        <!-- Category Header -->
                        <div class="border-b border-[#EAE2D0] pb-4 mb-6 flex items-center justify-between">
                            <div>
                                <h2 class="font-serif text-2xl font-black text-[#0D3B2E]" x-text="catGroup.name"></h2>
                                <span class="text-xs text-[#122018]/50" x-text="catGroup.items.length + ' pilihan'"></span>
                            </div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#145341]">UM.CO Selection</span>
                        </div>

                        <!-- 2 Columns Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-4">
                            <template x-for="item in catGroup.items" :key="item.id">
                                <div class="flex items-center justify-between p-3 rounded-2xl hover:bg-[#FAF7EE] transition group">
                                    <div class="flex items-center gap-3.5 min-w-0 pr-4">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-[#FAF7EE] flex-shrink-0">
                                            <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition" loading="lazy" />
                                        </div>
                                        <div class="truncate">
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-serif font-bold text-[#0D3B2E] text-sm truncate" x-text="item.name"></h3>
                                                <template x-if="item.is_favorite">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#F5B82E] text-[#07241C]">FAV</span>
                                                </template>
                                            </div>
                                            <p class="text-xs text-[#122018]/60 truncate mt-0.5" x-text="item.description"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        <span class="font-serif font-bold text-sm text-[#0D3B2E]" x-text="item.price_formatted"></span>
                                        <a
                                            :href="'/reservasi?menu=' + item.slug"
                                            wire:navigate
                                            class="w-7 h-7 rounded-full bg-[#0D3B2E] text-white flex items-center justify-center font-bold text-xs hover:bg-[#07241C] transition"
                                            title="Pesan dengan Meja"
                                        >
                                            +
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- EMPTY STATE -->
                <div x-show="categorizedItems().length === 0" class="text-center py-16 bg-white rounded-3xl p-6">
                    <p class="font-serif text-lg font-bold text-[#0D3B2E]">Menu tidak ditemukan</p>
                    <p class="text-xs text-[#122018]/60 mt-1">Tidak ada hasil untuk kata kunci "<span x-text="search" class="font-bold"></span>"</p>
                    <button
                        type="button"
                        @click="search = ''; activeCategory = 'all'"
                        class="mt-4 px-5 py-2 rounded-full text-xs font-bold uppercase tracking-wider bg-[#0D3B2E] text-white"
                    >
                        Lihat Semua Menu
                    </button>
                </div>
            </div>

            <!-- BOTTOM QUICK RESERVATION PROMPT -->
            <div class="mt-16 bg-[#0D3B2E] text-white rounded-3xl p-8 sm:p-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#F5B82E]">Nongkrong &amp; Nugas</span>
                    <h3 class="font-serif text-2xl sm:text-3xl font-black mt-1">Sudah Tahu Mau Pesan Apa?</h3>
                    <p class="text-xs sm:text-sm text-[#9AB4A6] mt-1">Amankan mejamu sekarang agar tidak kehabisan tempat saat jam ramai.</p>
                </div>
                <a
                    href="/reservasi"
                    wire:navigate
                    class="px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition flex-shrink-0"
                >
                    Mulai Reservasi &rarr;
                </a>
            </div>

        </div>
    </section>

    <!-- CLIENT SCRIPT -->
    <script>
    function menuCatalog() {
        return {
            items: @json($itemsPayload),
            categories: @json($categories),
            activeCategory: 'all',
            search: '',

            initCatalog() {
                const hash = window.location.hash.replace('#', '');
                if (hash) {
                    const match = this.categories.find(c => c.slug === hash);
                    if (match) this.activeCategory = match.slug;
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

                return this.categories.map(cat => {
                    const catItems = this.items.filter(item => {
                        const inCategory = (currentCat === 'all' || item.category_slug === currentCat);
                        const matchCatId = (item.category_id === cat.id);
                        if (!matchCatId || !inCategory) return false;

                        if (!s) return true;
                        const matchName = item.name.toLowerCase().includes(s);
                        const matchDesc = (item.description || '').toLowerCase().includes(s);
                        return matchName || matchDesc;
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
