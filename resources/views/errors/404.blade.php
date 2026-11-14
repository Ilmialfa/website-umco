<x-layouts.public
    page="not-found"
    initial-nav-theme="light"
    title="Halaman tidak ditemukan — UMCO"
>
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-20">
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <p class="font-hand text-3xl text-[var(--cabai)]">404</p>
                    <h1 class="type-h1 font-display font-extrabold tracking-tight">
                        Halaman ini sudah hangus.
                    </h1>
                    <p class="type-body text-[var(--tinta-muted)] max-w-lg">
                        Tautan yang kamu buka tidak ketemu atau sudah pindah tempat. Balik saja ke depan, masih ada kursi kosong yang bisa dipilih.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="{{ url('/') }}" wire:navigate class="btn-umco btn-umco-primary">
                            <span class="btn-roll">
                                <span>Ke beranda</span>
                                <span aria-hidden="true">Ke beranda</span>
                            </span>
                        </a>
                        <a href="{{ url('/menu') }}" wire:navigate class="btn-umco btn-umco-secondary">
                            Lihat daftar menu
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="karcis-serrated bg-[var(--blush)] text-[var(--tinta)] p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between font-display font-bold">
                            <span>UMCO</span>
                            <span class="tabular-nums text-sm">No. 0000</span>
                        </div>
                        <p class="type-h2 font-display font-extrabold">Karcis kosong</p>
                        <div class="space-y-2.5 text-sm border-t border-b border-[var(--tinta)]/20 py-4 font-hand text-xl">
                            <div class="flex justify-between">
                                <span>Halaman</span>
                                <span>Tidak ditemukan</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Status</span>
                                <span>Hangus</span>
                            </div>
                        </div>
                        <p class="text-xs text-[var(--tinta)]/75">
                            Tunjukkan niatmu di halaman utama untuk memilih meja dan minuman.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
