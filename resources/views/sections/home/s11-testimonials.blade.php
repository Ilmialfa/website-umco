@php
    $eyebrow = $section?->data['eyebrow'] ?? 'Kata Pengunjung';
    $heading = $section?->heading ?: 'Kenapa banyak yang balik lagi.';
    $subheading = $section?->subheading ?: 'Suara nyata mahasiswa, pekerja lepas, dan komunitas yang sering singgah.';
    $googleRating = str_replace('.', ',', \App\Support\Settings::get('rating.google_rating', '4.9'));
    $reviewsCount = \App\Support\Settings::get('rating.google_reviews_count', '86');

    $list = ($testimonials ?? collect())->values();
    if ($list->isEmpty()) {
        $list = collect([
            (object) ['name' => 'Rani', 'role' => 'Mahasiswi UMRI', 'avatar_path' => null, 'rating' => 5, 'body' => 'Aren Coffee-nya konsisten dan Nasi Ayam Sambal Matah-nya bikin betah makan siang di kampus.'],
            (object) ['name' => 'Fajar Ramadhan', 'role' => 'Mahasiswa Teknik', 'avatar_path' => null, 'rating' => 5, 'body' => 'Meja kerjanya luas dan colokannya gampang dijangkau buat diskusi sore bareng teman kelas.'],
            (object) ['name' => 'Sarah Nabilah', 'role' => 'Pekerja lepas', 'avatar_path' => null, 'rating' => 5, 'body' => 'Praktis banget bisa pesan meja sekalian menu langsung dari website sebelum datang.'],
        ]);
    }

    $mapped = $list->map(function ($t) {
        $name = (string) ($t->name ?? 'Tamu UMCO');
        $parts = preg_split('/\s+/', trim($name)) ?: [$name];
        $initials = collect($parts)->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
        $avatarUrl = null;
        if (! empty($t->avatar_path)) {
            $avatarUrl = str_starts_with($t->avatar_path, 'http') || str_starts_with($t->avatar_path, '/')
                ? asset($t->avatar_path)
                : asset('storage/' . ltrim($t->avatar_path, '/'));
        }
        return [
            'name' => $name,
            'initials' => $initials ?: 'U',
            'role' => $t->role ?: 'Pengunjung UMCO',
            'rating' => max(1, min(5, (int) ($t->rating ?? 5))),
            'body' => $t->body,
            'avatar' => $avatarUrl,
        ];
    })->values();

    // Bagi menjadi grup 3 kolom simetris untuk rolling paralel otomatis atas/bawah
    $groups = $mapped->chunk(3)->map(function ($chunk) use ($mapped) {
        $arr = $chunk->values()->all();
        $i = 0;
        while (count($arr) < 3 && $mapped->isNotEmpty()) {
            $arr[] = $mapped[$i % $mapped->count()];
            $i++;
        }
        return $arr;
    })->values()->all();
@endphp

<section
    id="kata-mereka"
    data-nav-theme="light"
    x-data="{
        groups: @js($groups),
        activeGroup: 0,
        prevGroup: null,
        rolling: false,
        paused: false,
        timer: null,
        init() {
            if (this.groups.length > 1) {
                this.timer = setInterval(() => {
                    if (!this.paused) this.rollNext();
                }, 4400);
            }
        },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        },
        rollNext() {
            if (this.rolling || this.groups.length <= 1) return;
            this.prevGroup = this.activeGroup;
            this.activeGroup = (this.activeGroup + 1) % this.groups.length;
            this.rolling = true;
            setTimeout(() => {
                this.rolling = false;
                this.prevGroup = null;
            }, 580);
        }
    }"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    class="umco-section bg-[var(--batu)]/55 text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-12 sm:space-y-14">
        {{-- Header Tengah Editorial Maksimal 1-2 Baris --}}
        <div class="max-w-5xl mx-auto text-center space-y-2.5">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                {{ $eyebrow }} · {{ $googleRating }}/5 ({{ $reviewsCount }} ulasan)
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $heading }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-xl mx-auto">
                {{ $subheading }}
            </p>
        </div>

        {{-- 3 Kolom Paralel Rolling Vertikal Otomatis (Kiri Turun, Tengah Naik, Kanan Turun; tanpa tombol kanan/kiri) --}}
        <div data-reveal-media class="grid grid-cols-1 md:grid-cols-3 gap-9 md:gap-8 lg:gap-12">
            @for ($col = 0; $col < 3; $col++)
                @php
                    $isMiddle = $col === 1;
                    $inClass = $isMiddle ? 'umco-roll-in-up' : 'umco-roll-in-down';
                    $outClass = $isMiddle ? 'umco-roll-out-up' : 'umco-roll-out-down';
                @endphp
                <div class="border-l border-[var(--tinta)]/20 pl-6 sm:pl-7 overflow-hidden relative min-h-[215px] sm:min-h-[225px]">
                    {{-- Item Lama yang Meluncur Keluar Saat Transisi --}}
                    <template x-if="rolling && prevGroup !== null">
                        <article
                            class="absolute inset-0 pl-6 sm:pl-7 flex flex-col justify-between gap-6 pointer-events-none {{ $outClass }}"
                            aria-hidden="true"
                        >
                            <div class="space-y-4">
                                <div class="flex items-center gap-1 text-[#C88A1E]">
                                    <template x-for="s in groups[prevGroup][{{ $col }}].rating" :key="'old-s-' + s">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.2 1 5.9L10 15l-5.2 2.8 1-5.9-4.3-4.2 5.9-.9L10 1.5z"/></svg>
                                    </template>
                                </div>
                                <p class="text-[15px] sm:text-base leading-relaxed text-[var(--tinta)] line-clamp-4" x-text="groups[prevGroup][{{ $col }}].body"></p>
                            </div>

                            <footer class="flex items-center gap-3.5 pt-1">
                                <template x-if="groups[prevGroup][{{ $col }}].avatar">
                                    <img :src="groups[prevGroup][{{ $col }}].avatar" :alt="groups[prevGroup][{{ $col }}].name" class="w-11 h-11 rounded-full object-cover bg-[var(--kertas)] shrink-0">
                                </template>
                                <template x-if="!groups[prevGroup][{{ $col }}].avatar">
                                    <span class="w-11 h-11 rounded-full bg-[var(--mentega)] text-white font-display font-bold text-base inline-flex items-center justify-center shrink-0" x-text="groups[prevGroup][{{ $col }}].initials"></span>
                                </template>
                                <div class="min-w-0">
                                    <p class="font-display font-bold text-base leading-tight text-[var(--tinta)] truncate" x-text="groups[prevGroup][{{ $col }}].name"></p>
                                    <p class="text-xs text-[var(--tinta-70)] truncate" x-text="groups[prevGroup][{{ $col }}].role"></p>
                                </div>
                            </footer>
                        </article>
                    </template>

                    {{-- Item Aktif yang Meluncur Masuk Secara Paralel --}}
                    <article
                        :key="'cur-' + activeGroup + '-c{{ $col }}'"
                        :class="rolling ? '{{ $inClass }}' : ''"
                        class="h-full flex flex-col justify-between gap-6"
                    >
                        <div class="space-y-4">
                            <div class="flex items-center gap-1 text-[#C88A1E]" :aria-label="groups[activeGroup][{{ $col }}].rating + ' bintang'">
                                <template x-for="s in groups[activeGroup][{{ $col }}].rating" :key="'cur-s-' + s">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.2 1 5.9L10 15l-5.2 2.8 1-5.9-4.3-4.2 5.9-.9L10 1.5z"/></svg>
                                </template>
                            </div>
                            <p class="text-[15px] sm:text-base leading-relaxed text-[var(--tinta)] line-clamp-4" x-text="groups[activeGroup][{{ $col }}].body"></p>
                        </div>

                        <footer class="flex items-center gap-3.5 pt-1">
                            <template x-if="groups[activeGroup][{{ $col }}].avatar">
                                <img :src="groups[activeGroup][{{ $col }}].avatar" :alt="groups[activeGroup][{{ $col }}].name" class="w-11 h-11 rounded-full object-cover bg-[var(--kertas)] shrink-0">
                            </template>
                            <template x-if="!groups[activeGroup][{{ $col }}].avatar">
                                <span
                                    class="w-11 h-11 rounded-full bg-[var(--mentega)] text-white font-display font-bold text-base inline-flex items-center justify-center shrink-0"
                                    x-text="groups[activeGroup][{{ $col }}].initials"
                                ></span>
                            </template>
                            <div class="min-w-0">
                                <p class="font-display font-bold text-base leading-tight text-[var(--tinta)] truncate" x-text="groups[activeGroup][{{ $col }}].name"></p>
                                <p class="text-xs text-[var(--tinta-70)] truncate" x-text="groups[activeGroup][{{ $col }}].role"></p>
                            </div>
                        </footer>
                    </article>
                </div>
            @endfor
        </div>
    </div>
</section>
