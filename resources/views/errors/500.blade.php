<x-layouts.public
    page="server-error"
    initial-nav-theme="light"
    title="Kendala teknis — UMCO"
>
    <section data-nav-theme="light" class="bg-[var(--kertas)] text-[var(--tinta)] pt-28 sm:pt-36 pb-20">
        <div class="umco-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7 space-y-5">
                    <p class="font-hand text-3xl text-[var(--mentega-tua)]">500</p>
                    <h1 class="type-h1 font-display font-semibold tracking-tight">
                        Mesin di balik meja sedang tersendat.
                    </h1>
                    <p class="type-body text-[var(--tinta-muted)] max-w-lg">
                        Terjadi gangguan singkat di server kami. Coba muat ulang sebentar lagi, atau langsung ke beranda dan halaman menu.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="{{ url('/') }}" class="btn-umco btn-umco-primary">
                            <span class="btn-roll">
                                <span>Ke beranda</span>
                                <span aria-hidden="true">Ke beranda</span>
                            </span>
                        </a>
                        <a href="{{ url('/reservasi') }}" class="btn-umco btn-umco-secondary">
                            Coba pesan meja
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="karcis-serrated bg-[var(--kertas-terang)] text-[var(--tinta)] p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between font-display font-semibold">
                            <span>UMCO</span>
                            <span class="tabular-nums text-sm text-[var(--mentega-tua)]">No. 0500</span>
                        </div>
                        <p class="type-h2 font-display font-semibold">Gangguan sementara</p>
                        <div class="space-y-2.5 text-sm border-t border-b border-[var(--garis)] py-4 font-hand text-xl">
                            <div class="flex justify-between">
                                <span>Status server</span>
                                <span>Perlu muat ulang</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Lokasi kedai</span>
                                <span>Tetap buka normal</span>
                            </div>
                        </div>
                        <p class="text-xs text-[var(--tinta-70)]">
                            Kampus 2 UMRI, Pekanbaru. Kamu tetap bisa memesan lewat WhatsApp kasir jika kendala berlanjut.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
