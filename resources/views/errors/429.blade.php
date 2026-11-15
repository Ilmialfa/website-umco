<x-layouts.public
    page="too-many-requests"
    initial-nav-theme="light"
    title="Terlalu banyak permintaan — UMCO"
>
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-20">
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <p class="font-hand text-3xl text-[var(--mentega-tua)]">429</p>
                    <h1 class="type-h1 font-display font-semibold tracking-tight">
                        Pelan-pelan dulu, bar masih menyeduh.
                    </h1>
                    <p class="type-body text-[var(--tinta-muted)] max-w-lg">
                        Terlalu banyak permintaan dikirim dalam waktu singkat. Tunggu sekitar satu menit, lalu coba lagi.
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
                    <div class="karcis-serrated bg-[var(--kertas-terang)] text-[var(--tinta)] p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between font-display font-semibold">
                            <span>UMCO</span>
                            <span class="tabular-nums text-sm text-[var(--mentega-tua)]">No. 0429</span>
                        </div>
                        <p class="type-h2 font-display font-semibold">Antre sebentar</p>
                        <div class="space-y-2.5 text-sm border-t border-b border-[var(--garis)] py-4 font-hand text-xl">
                            <div class="flex justify-between">
                                <span>Batas akses</span>
                                <span>Sedang dijeda</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Saran</span>
                                <span>Coba 1 menit lagi</span>
                            </div>
                        </div>
                        <p class="text-xs text-[var(--tinta-70)]">
                            Pembatasan ini menjaga antrean reservasi dan pengecekan karcis tetap aman untuk semua pengunjung.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
