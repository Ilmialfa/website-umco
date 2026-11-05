<x-layouts.public>
    <x-slot:title>Katalog Menu & Seduhan — UM.CO House Kampus UMRI</x-slot:title>
    <x-slot:description>Daftar lengkap kopi nusantara, artisan tea, rice bowl, dan jajanan street food UM.CO House di Kampus UMRI Pekanbaru.</x-slot:description>

    <!-- HERO HEADER: MINIMALIST & BOLD SANS (NO SERIFS) -->
    <div class="px-3 sm:px-6 pt-4 pb-6">
        <section class="rounded-[2rem] sm:rounded-[2.5rem] bg-[#07241C] text-white py-20 sm:py-28 px-6 sm:px-12 text-center relative overflow-hidden">
            <div class="max-w-3xl mx-auto space-y-4 relative z-10">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-xs font-black uppercase tracking-widest text-[#F5B82E]">
                    Katalog Resmi
                </span>
                <h1 class="font-display text-3xl sm:text-5xl font-extrabold uppercase tracking-wide leading-tight text-white">
                    Fresh Brews &amp; Bites.
                </h1>
                <p class="text-sm sm:text-base text-[#9AB4A6] max-w-lg mx-auto">
                    Kopi segar kalibrasi harian dan camilan hangat kampus untuk temani tugasmu.
                </p>
            </div>
        </section>
    </div>

    <!-- INTERACTIVE MENU CATALOG -->
    <section
        x-data="menuCatalog()"
        x-init="initCatalog()"
        class="py-10 bg-[#FAF7EE] min-h-screen"
    >
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- FILTER & SEARCH BAR -->
            <div class="sticky top-20 z-30 bg-[#FAF7EE]/95 backdrop-blur-md py-4 mb-10 transition">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-80 flex-shrink-0">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Cari menu kopi, makanan..."
                            class="w-full pl-10 pr-10 py-3 bg-white rounded-full text-xs sm:text-sm text-[#0D3B2E] placeholder:text-[#0D3B2E]/40 focus:outline-none focus:ring-2 focus:ring-[#F5B82E]"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#0D3B2E]/40">
                            🔍
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
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs font-bold uppercase tracking-wider">
                        <button
                            type="button"
                            @click="selectCategory('all')"
                            class="px-5 py-2.5 rounded-full whitespace-nowrap transition cursor-pointer"
                            :class="activeCategory === 'all' ? 'bg-[#0D3B2E] text-white' : 'bg-white text-[#0D3B2E] hover:bg-white/80'"
                        >
                            Semua
                        </button>
                        @foreach ($categories as $cat)
                            <button
                                type="button"
                                @click="selectCategory('{{ $cat->slug }}')"
                                class="px-5 py-2.5 rounded-full whitespace-nowrap transition cursor-pointer"
                                :class="activeCategory === '{{ $cat->slug }}' ? 'bg-[#0D3B2E] text-white' : 'bg-white text-[#0D3B2E] hover:bg-white/80'"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- PRODUCT CARDS (LARGE PHOTOGRAPHIC CARDS, ZERO CLUTTER) -->
            <div class="space-y-12">
                <template x-for="catGroup in categorizedItems()" :key="catGroup.slug">
                    <div>
                        <!-- Category Header -->
                        <div class="mb-6 flex items-baseline justify-between border-b border-[#EAE2D0] pb-3">
                            <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-[#0D3B2E]" x-text="catGroup.name"></h2>
                            <span class="text-xs font-bold text-[#145341]" x-text="catGroup.items.length + ' Pilihan'"></span>
                        </div>

                        <!-- 4 Columns Photographic Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <template x-for="item in catGroup.items" :key="item.id">
                                <div class="bg-white rounded-3xl p-4 flex flex-col justify-between hover:bg-[#F4EEDF]/40 transition group">
                                    <div>
                                        <!-- Big Appetizing Image -->
                                        <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-[#FAF7EE] mb-4">
                                            <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" />
                                            <template x-if="item.is_favorite">
                                                <span class="absolute top-2.5 left-2.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#F5B82E] text-[#07241C]">
                                                    Favorit
                                                </span>
                                            </template>
                                        </div>

                                        <h3 class="font-display font-bold text-base text-[#0D3B2E] truncate" x-text="item.name"></h3>
                                        <p class="text-xs text-[#122018]/60 truncate mt-0.5" x-text="item.description"></p>
                                    </div>

                                    <!-- Bottom Action -->
                                    <div class="pt-4 mt-3 border-t border-[#EAE2D0] flex items-center justify-between">
                                        <span class="font-display font-black text-base text-[#0D3B2E]" x-text="item.price_formatted"></span>
                                        <a
                                            :href="'/reservasi?menu=' + item.slug"
                                            wire:navigate
                                            class="w-8 h-8 rounded-full bg-[#0D3B2E] text-white flex items-center justify-center font-bold text-sm hover:bg-[#F5B82E] hover:text-[#07241C] transition"
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
                <div x-show="categorizedItems().length === 0" class="text-center py-16 bg-white rounded-3xl p-8">
                    <p class="font-display text-xl font-bold text-[#0D3B2E]">Menu tidak ditemukan</p>
                    <p class="text-xs text-[#122018]/60 mt-1">Coba kata kunci lain atau tampilkan semua menu.</p>
                    <button
                        type="button"
                        @click="search = ''; activeCategory = 'all'"
                        class="mt-4 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#0D3B2E] text-white"
                    >
                        Tampilkan Semua
                    </button>
                </div>
            </div>

            <!-- BOTTOM PROMPT -->
            <div class="mt-16 bg-[#07241C] text-white rounded-3xl p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-1">
                    <span class="text-xs font-black uppercase tracking-widest text-[#F5B82E]">Hemat Waktu</span>
                    <h3 class="font-display text-2xl sm:text-3xl font-black uppercase">Pesan Menu Sambil Reservasi Meja</h3>
                    <p class="text-xs sm:text-sm text-[#9AB4A6]">Pesanan siap tersaji saat kamu tiba di kafe.</p>
                </div>
                <a
                    href="/reservasi?mode=b"
                    wire:navigate
                    class="px-8 py-4 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition flex-shrink-0"
                >
                    Reservasi + Pre-Order &rarr;
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
