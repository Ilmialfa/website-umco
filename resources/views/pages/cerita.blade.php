<x-layouts.public
    page="cerita"
    initial-nav-theme="light"
    title="Cerita Kami — UMCO"
>
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-16 sm:pb-24">
        <div class="umco-container space-y-12">
            <div class="max-w-3xl space-y-5">
                <p class="font-hand text-2xl sm:text-3xl text-[var(--cabai)]">Dari cangkir ke meja kampus</p>
                <h1 data-reveal="rise" class="type-h1 font-display font-extrabold tracking-tight">
                    Kedai kopi kecil di lingkungan Kampus 2 UMRI.
                </h1>
                <p data-reveal="fade" class="type-body text-[var(--tinta-muted)] max-w-2xl">
                    Kami buka setiap hari untuk mahasiswa, dosen, dan siapa saja yang singgah di Jalan Tuanku Tambusai Ujung, Delima, Pekanbaru.
                </p>
            </div>

            @if ($storyBlocks->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-[var(--garis)]" data-reveal-stagger>
                    @foreach ($storyBlocks as $block)
                        <article class="space-y-3 py-4">
                            <h2 class="type-h3 font-display font-bold">{{ $block->title }}</h2>
                            @if ($block->body)
                                <p class="type-body text-[var(--tinta-muted)]">{{ $block->body }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="pt-4">
                <a href="{{ url('/reservasi') }}" wire:navigate class="btn-umco btn-umco-primary">
                    <span class="btn-roll">
                        <span>Pesan meja</span>
                        <span aria-hidden="true">Pesan meja</span>
                    </span>
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>
