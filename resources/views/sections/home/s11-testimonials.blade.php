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

    // Bagi menjadi grup tepat 3 kolom agar tampilan horizontal simetris (Pt 7)
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
        fading: false,
        paused: false,
        timer: null,
        init() {
            if (this.groups.length > 1) {
                this.timer = setInterval(() => {
                    if (!this.paused) this.next();
                }, 5200);
            }
        },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        },
        switchTo(idx) {
            if (idx === this.activeGroup || this.fading) return;
            this.fading = true;
            setTimeout(() => {
                this.activeGroup = idx;
                this.fading = false;
            }, 220);
        },
        next() {
            this.switchTo((this.activeGroup + 1) % this.groups.length);
        },
        prev() {
            this.switchTo((this.activeGroup - 1 + this.groups.length) % this.groups.length);
        }
    }"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    class="umco-section bg-[var(--batu)]/55 text-[var(--tinta)] overflow-hidden"
>
    <div class="umco-container space-y-12 sm:space-y-14">
        {{-- Header Tengah Editorial Gaya Contoh --}}
        <div class="max-w-2xl mx-auto text-center space-y-2.5">
            <p data-reveal="fade" class="font-hand text-2xl sm:text-[1.75rem] leading-none text-[var(--mentega)]">
                {{ $eyebrow }} · {{ $googleRating }}/5 ({{ $reviewsCount }} ulasan)
            </p>
            <h2 data-reveal="rise" class="type-display-l font-display font-bold">
                {{ $heading }}
            </h2>
            <p data-reveal="fade" class="type-body text-[var(--tinta-70)] max-w-lg mx-auto">
                {{ $subheading }}
            </p>
        </div>

        {{-- 3 Kolom Horizontal dengan Garis Vertikal Kiri + Bintang + Avatar + Transisi Smooth --}}
        <div
            data-reveal-media
            class="transition-all duration-500 ease-out"
            :class="fading ? 'opacity-0 translate-y-2' : 'opacity-100 translate-y-0'"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-9 md:gap-8 lg:gap-12">
                <template x-for="(t, idx) in groups[activeGroup]" :key="activeGroup + '-' + idx + '-' + t.name">
                    <article class="border-l border-[var(--tinta)]/20 pl-6 sm:pl-7 flex flex-col justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-1 text-[#C88A1E]" :aria-label="t.rating + ' bintang'">
                                <template x-for="s in t.rating" :key="s">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.2 1 5.9L10 15l-5.2 2.8 1-5.9-4.3-4.2 5.9-.9L10 1.5z"/></svg>
                                </template>
                            </div>

                            <p class="text-[15px] sm:text-base leading-relaxed text-[var(--tinta)]" x-text="t.body"></p>
                        </div>

                        <footer class="flex items-center gap-3.5 pt-1">
                            <template x-if="t.avatar">
                                <img :src="t.avatar" :alt="t.name" class="w-11 h-11 rounded-full object-cover bg-[var(--kertas)] shrink-0">
                            </template>
                            <template x-if="!t.avatar">
                                <span
                                    class="w-11 h-11 rounded-full bg-[var(--mentega)] text-white font-display font-bold text-base inline-flex items-center justify-center shrink-0"
                                    x-text="t.initials"
                                ></span>
                            </template>
                            <div class="min-w-0">
                                <p class="font-display font-bold text-base leading-tight text-[var(--tinta)] truncate" x-text="t.name"></p>
                                <p class="text-xs text-[var(--tinta-70)] truncate" x-text="t.role"></p>
                            </div>
                        </footer>
                    </article>
                </template>
            </div>
        </div>

        {{-- Navigasi Titik Halus bila terdapat lebih dari 1 set (3 ulasan/set) --}}
        <div x-show="groups.length > 1" class="flex items-center justify-center gap-3">
            <button
                type="button"
                @click="prev()"
                aria-label="Ulasan sebelumnya"
                class="w-9 h-9 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--mentega)] hover:text-white inline-flex items-center justify-center transition-colors cursor-pointer"
            >
                <svg class="w-3.5 h-3.5"><use href="#icon-arrow-left"/></svg>
            </button>
            <div class="flex items-center gap-2">
                <template x-for="(_, gIdx) in groups" :key="'tg-' + gIdx">
                    <button
                        type="button"
                        @click="switchTo(gIdx)"
                        :aria-label="'Tampilkan kelompok ulasan ' + (gIdx + 1)"
                        class="h-2 rounded-full transition-all cursor-pointer"
                        :class="activeGroup === gIdx ? 'w-7 bg-[var(--tinta)]' : 'w-2 bg-[var(--tinta)]/25 hover:bg-[var(--tinta)]/50'"
                    ></button>
                </template>
            </div>
            <button
                type="button"
                @click="next()"
                aria-label="Ulasan berikutnya"
                class="w-9 h-9 rounded-full bg-[var(--kertas-terang)] text-[var(--tinta)] hover:bg-[var(--mentega)] hover:text-white inline-flex items-center justify-center transition-colors cursor-pointer"
            >
                <svg class="w-3.5 h-3.5"><use href="#icon-arrow-right"/></svg>
            </button>
        </div>
    </div>
</section>
