<x-layouts.public
    page="session-expired"
    initial-nav-theme="light"
    title="Sesi kedaluwarsa — UMCO"
>
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-20">
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <p class="font-hand text-3xl text-[var(--mentega-tua)]">419</p>
                    <h1 class="type-h1 font-display font-semibold tracking-tight">
                        Sesi formulirnya keburu habis.
                    </h1>
                    <p class="type-body text-[var(--tinta-muted)] max-w-lg">
                        Halaman dibiarkan terbuka terlalu lama sehingga token keamanan kedaluwarsa. Muat ulang halaman reservasi supaya kamu bisa mengisi ulang dengan aman.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="{{ url('/reservasi') }}" class="btn-umco btn-umco-primary">
                            <span class="btn-roll">
                                <span>Muat ulang reservasi</span>
                                <span aria-hidden="true">Muat ulang reservasi</span>
                            </span>
                        </a>
                        <a href="{{ url('/') }}" wire:navigate class="btn-umco btn-umco-secondary">
                            Ke beranda
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="karcis-serrated bg-[var(--kertas-terang)] text-[var(--tinta)] p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between font-display font-semibold">
                            <span>UMCO</span>
                            <span class="tabular-nums text-sm text-[var(--mentega-tua)]">No. 0419</span>
                        </div>
                        <p class="type-h2 font-display font-semibold">Sesi berakhir</p>
                        <div class="space-y-2.5 text-sm border-t border-b border-[var(--garis)] py-4 font-hand text-xl">
                            <div class="flex justify-between">
                                <span>Keadaan</span>
                                <span>Token tidak berlaku</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Langkah</span>
                                <span>Muat ulang halaman</span>
                            </div>
                        </div>
                        <p class="text-xs text-[var(--tinta-70)]">
                            Untuk keamanan transaksi DP dan data tamu, sesi formulir otomatis berakhir setelah lama tidak aktif.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
