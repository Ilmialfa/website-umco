<div class="min-h-screen bg-oat pt-28 pb-20 lg:pt-36 lg:pb-28">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        @if ($step === 'form')
            <!-- HEADER -->
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-widest uppercase bg-forest/10 text-forest border border-forest/15 mb-3">
                    Sistem Reservasi Meja Kampus UMRI
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest leading-tight">
                    Pilih Cara Kunjungan Anda
                </h1>
                <p class="mt-3 text-sm sm:text-base text-forest/70 leading-relaxed">
                    Amankan meja untuk kelompok diskusi, bimbingan dosen, atau nugas mandiri. Pilih tanpa DP atau pesan menu di awal.
                </p>
            </div>

            <!-- MODE SWITCHER TABS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mx-auto mb-12">
                <!-- Mode A Card -->
                <button
                    type="button"
                    wire:click="setMode('a')"
                    class="p-6 rounded-3xl text-left border-2 transition-all cursor-pointer relative flex flex-col justify-between {{ $mode === 'a' ? 'bg-forest text-alabaster border-forest shadow-md scale-[1.01]' : 'bg-white text-forest border-forest/15 hover:border-forest/40' }}"
                >
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $mode === 'a' ? 'bg-crema text-forest' : 'bg-forest/10 text-forest' }}">
                                Tanpa DP
                            </span>
                            @if ($mode === 'a')
                                <span class="w-2.5 h-2.5 rounded-full bg-crema"></span>
                            @endif
                        </div>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold mb-1.5">
                            Mode A: Meja Saja
                        </h2>
                        <p class="text-xs sm:text-sm leading-relaxed {{ $mode === 'a' ? 'text-sage' : 'text-forest/70' }}">
                            Pesan meja terlebih dahulu tanpa biaya apapun. Menu kopi dan makanan dipilih dan dibayar langsung saat Anda tiba di kasir.
                        </p>
                    </div>
                    <span class="mt-4 text-xs font-semibold {{ $mode === 'a' ? 'text-crema' : 'text-forest' }}">
                        Cocok untuk nugas santai &amp; kelompok kecil &rarr;
                    </span>
                </button>

                <!-- Mode B Card -->
                <button
                    type="button"
                    wire:click="setMode('b')"
                    class="p-6 rounded-3xl text-left border-2 transition-all cursor-pointer relative flex flex-col justify-between {{ $mode === 'b' ? 'bg-forest text-alabaster border-forest shadow-md scale-[1.01]' : 'bg-white text-forest border-forest/15 hover:border-forest/40' }}"
                >
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $mode === 'b' ? 'bg-crema text-forest' : 'bg-forest/10 text-forest' }}">
                                DP 30% (Min Rp 25k)
                            </span>
                            @if ($mode === 'b')
                                <span class="w-2.5 h-2.5 rounded-full bg-crema"></span>
                            @endif
                        </div>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold mb-1.5">
                            Mode B: Meja + Pesan Menu
                        </h2>
                        <p class="text-xs sm:text-sm leading-relaxed {{ $mode === 'b' ? 'text-sage' : 'text-forest/70' }}">
                            Pilih makanan dan minuman sekarang dengan DP 30%. Pesanan siap disajikan saat Anda tiba, tanpa antre di kasir.
                        </p>
                    </div>
                    <span class="mt-4 text-xs font-semibold {{ $mode === 'b' ? 'text-crema' : 'text-forest' }}">
                        Prioritas penyajian &amp; hemat waktu &rarr;
                    </span>
                </button>
            </div>

            <!-- MAIN FORM WRAPPER -->
            <form wire:submit="submit" class="space-y-10">

                <!-- STEP 1: INFORMASI DATA & WAKTU (APPLIES TO BOTH MODES) -->
                <div class="bg-white rounded-3xl p-6 sm:p-10 border border-forest/10 shadow-sm">
                    <div class="border-b border-forest/10 pb-4 mb-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-moss">Langkah 1</span>
                        <h3 class="font-serif text-2xl font-bold text-forest">Data Pemesan &amp; Waktu Kedatangan</h3>
                    </div>

                    @if ($errors->has('general'))
                        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                            {{ $errors->first('general') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Nama Pemesan -->
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-forest">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                wire:model="name"
                                placeholder="Contoh: Muhammad Ihsan"
                                class="w-full px-4 py-3 bg-oat/40 border border-forest/20 rounded-xl text-sm text-forest placeholder:text-forest/40 focus:outline-none focus:ring-2 focus:ring-moss/30 focus:border-moss transition"
                            />
                            @error('name')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div class="space-y-1.5">
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-forest">
                                Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="tel"
                                id="phone"
                                wire:model.live.debounce.400ms="phone"
                                placeholder="Contoh: 0812 3456 7890"
                                class="w-full px-4 py-3 bg-oat/40 border border-forest/20 rounded-xl text-sm text-forest placeholder:text-forest/40 focus:outline-none focus:ring-2 focus:ring-moss/30 focus:border-moss transition"
                            />
                            <p class="text-[11px] text-forest/60">
                                Konfirmasi booking dan barcode meja akan dikirimkan ke nomor ini.
                            </p>
                            @error('phone')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Reservasi -->
                        <div class="space-y-1.5">
                            <label for="date" class="block text-xs font-bold uppercase tracking-wider text-forest">
                                Tanggal Kedatangan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="date"
                                wire:model.live="date"
                                min="{{ date('Y-m-d') }}"
                                max="{{ now()->addDays(14)->format('Y-m-d') }}"
                                class="w-full px-4 py-3 bg-oat/40 border border-forest/20 rounded-xl text-sm text-forest focus:outline-none focus:ring-2 focus:ring-moss/30 focus:border-moss transition"
                            />
                            @if ($closedDate)
                                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                    <strong>Pemberitahuan:</strong> UM.CO House tutup pada tanggal ini ({{ $closedDate->reason ?: 'Operasional Libur' }}).
                                </div>
                            @endif
                            @error('date')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jumlah Orang (Party Size) -->
                        <div class="space-y-1.5">
                            <label for="party_size" class="block text-xs font-bold uppercase tracking-wider text-forest">
                                Jumlah Orang (Pax) <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input
                                    type="number"
                                    id="party_size"
                                    wire:model.live="party_size"
                                    min="1"
                                    max="12"
                                    class="w-24 px-4 py-3 bg-oat/40 border border-forest/20 rounded-xl text-sm font-bold text-forest text-center focus:outline-none focus:ring-2 focus:ring-moss/30 focus:border-moss transition"
                                />
                                <!-- Quick Select Pax Buttons -->
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @foreach ([2, 4, 6, 8, 10] as $paxOption)
                                        <button
                                            type="button"
                                            wire:click="$set('party_size', {{ $paxOption }})"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer {{ $party_size === $paxOption ? 'bg-forest text-alabaster' : 'bg-oat/60 text-forest hover:bg-forest/10' }}"
                                        >
                                            {{ $paxOption }} orang
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <p class="text-[11px] text-forest/60">
                                Untuk reservasi di atas 12 orang (agenda UKM / rapat akbar), silakan hubungi kasir via WhatsApp.
                            </p>
                            @error('party_size')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Jam Kedatangan (Pills Grid with Capacity) -->
                    <div class="mt-8 pt-6 border-t border-forest/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-forest">
                                Pilih Jam Kedatangan (Slot 30 Menit) <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-xs text-forest/60">Maksimal kapasitas 40 kursi per slot</span>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2.5">
                            @foreach ($availableSlots as $slot)
                                <button
                                    type="button"
                                    @if ($slot['is_full']) disabled @else wire:click="$set('time', '{{ $slot['time'] }}')" @endif
                                    class="p-2.5 rounded-xl text-center border transition flex flex-col items-center justify-center cursor-pointer {{ $slot['is_full'] ? 'opacity-40 bg-zinc-100 border-zinc-200 cursor-not-allowed text-zinc-400' : ($time === $slot['time'] ? 'bg-forest text-alabaster border-forest shadow-sm' : 'bg-oat/30 border-forest/15 hover:border-forest/40 text-forest') }}"
                                >
                                    <span class="text-sm font-bold">{{ $slot['time'] }}</span>
                                    <span class="text-[10px] {{ $slot['is_full'] ? 'text-rose-500' : ($time === $slot['time'] ? 'text-crema' : 'text-forest/60') }}">
                                        {{ $slot['is_full'] ? 'Penuh' : $slot['remaining'] . ' kursi' }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                        @error('time')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan / Permintaan Khusus -->
                    <div class="mt-8 pt-6 border-t border-forest/10 space-y-1.5">
                        <label for="note" class="block text-xs font-bold uppercase tracking-wider text-forest">
                            Catatan atau Preferensi Tempat Duduk (Opsional)
                        </label>
                        <textarea
                            id="note"
                            wire:model="note"
                            rows="2"
                            placeholder="Contoh: Butuh meja dekat colokan listrik di Wing Barat, atau meja area outdoor courtyard."
                            class="w-full px-4 py-3 bg-oat/40 border border-forest/20 rounded-xl text-sm text-forest placeholder:text-forest/40 focus:outline-none focus:ring-2 focus:ring-moss/30 focus:border-moss transition"
                        ></textarea>
                        @error('note')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- STEP 2: PILIH MENU & PEMBAYARAN DP (ONLY FOR MODE B) -->
                @if ($mode === 'b')
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                        <!-- Left: Menu Item Catalog Selector (8 cols) -->
                        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-forest/10 shadow-sm space-y-8">
                            <div class="border-b border-forest/10 pb-4">
                                <span class="text-xs font-bold uppercase tracking-widest text-moss">Langkah 2</span>
                                <h3 class="font-serif text-2xl font-bold text-forest">Pilih Menu Pre-Order</h3>
                                <p class="text-xs text-forest/70 mt-1">
                                    Pilih minuman atau makanan yang ingin disiapkan sebelum Anda sampai.
                                </p>
                            </div>

                            @error('selected_items')
                                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                                    {{ $message }}
                                </div>
                            @enderror

                            <!-- Categories & Menu Items -->
                            <div class="space-y-8">
                                @foreach ($menuCategories as $catName => $items)
                                    <div>
                                        <h4 class="font-serif text-lg font-bold text-forest pb-2 mb-4 border-b border-forest/10 flex items-center justify-between">
                                            <span>{{ $catName }}</span>
                                            <span class="text-xs font-normal text-forest/60">{{ count($items) }} pilihan</span>
                                        </h4>

                                        <div class="space-y-3">
                                            @foreach ($items as $item)
                                                @php
                                                    $qty = $selected_items[$item->id] ?? 0;
                                                @endphp
                                                <div class="flex items-center justify-between p-3 rounded-2xl border transition {{ $qty > 0 ? 'bg-forest/5 border-moss/40' : 'bg-oat/20 border-forest/10 hover:border-forest/20' }}">
                                                    <!-- Item Info -->
                                                    <div class="flex items-center gap-3 min-w-0 pr-3">
                                                        <div class="w-12 h-12 rounded-xl bg-oat overflow-hidden flex-shrink-0">
                                                            <img
                                                                src="{{ $item->image_url }}"
                                                                alt="{{ $item->name }}"
                                                                class="w-full h-full object-cover"
                                                                loading="lazy"
                                                            />
                                                        </div>
                                                        <div class="truncate">
                                                            <h5 class="text-sm font-bold text-forest truncate">{{ $item->name }}</h5>
                                                            <span class="text-xs font-semibold text-moss">{{ $item->formatted_price }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Quantity Counter Buttons -->
                                                    <div class="flex items-center gap-2 flex-shrink-0">
                                                        @if ($qty > 0)
                                                            <button
                                                                type="button"
                                                                wire:click="removeItem({{ $item->id }})"
                                                                class="w-8 h-8 rounded-full bg-forest/10 text-forest hover:bg-forest hover:text-white transition flex items-center justify-center font-bold text-sm cursor-pointer"
                                                                aria-label="Kurangi 1"
                                                            >
                                                                &minus;
                                                            </button>
                                                            <span class="w-6 text-center text-sm font-bold text-forest">
                                                                {{ $qty }}
                                                            </span>
                                                        @endif

                                                        <button
                                                            type="button"
                                                            wire:click="addItem({{ $item->id }})"
                                                            class="w-8 h-8 rounded-full bg-forest text-alabaster hover:bg-forest-deep transition flex items-center justify-center font-bold text-sm cursor-pointer shadow-sm"
                                                            aria-label="Tambah 1"
                                                        >
                                                            +
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right: Sticky Order Summary & Payment Transfer (5 cols) -->
                        <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">

                            <!-- Order Financial Summary Card -->
                            <div class="bg-forest text-alabaster rounded-3xl p-6 sm:p-8 border border-forest shadow-lg space-y-6">
                                <div class="border-b border-white/15 pb-4">
                                    <span class="text-xs font-bold uppercase tracking-widest text-crema">Ringkasan Pre-Order</span>
                                    <h3 class="font-serif text-xl font-bold mt-0.5">Rincian Pembayaran DP</h3>
                                </div>

                                <!-- Selected items list -->
                                @if (!empty($selected_items))
                                    <div class="space-y-2.5 max-h-56 overflow-y-auto pr-1 text-xs">
                                        @foreach ($menuCategories->flatten() as $item)
                                            @if (isset($selected_items[$item->id]) && $selected_items[$item->id] > 0)
                                                @php $q = $selected_items[$item->id]; @endphp
                                                <div class="flex items-center justify-between py-1 border-b border-white/10">
                                                    <div class="truncate pr-2">
                                                        <span class="font-semibold text-alabaster">{{ $item->name }}</span>
                                                        <span class="text-sage block text-[11px]">{{ $q }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                                    </div>
                                                    <span class="font-bold text-crema flex-shrink-0">
                                                        Rp {{ number_format($item->price * $q, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6 text-xs text-sage italic">
                                        Belum ada menu yang dipilih. Klik tombol "+" pada daftar menu di samping.
                                    </div>
                                @endif

                                <!-- Financial totals -->
                                <div class="pt-4 border-t border-white/15 space-y-3">
                                    <div class="flex items-center justify-between text-sm text-sage">
                                        <span>Total Pesanan Menu</span>
                                        <span class="font-bold text-alabaster">
                                            Rp {{ number_format($this->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-base font-bold bg-white/10 p-3.5 rounded-2xl border border-white/15">
                                        <div>
                                            <span class="block text-crema text-xs uppercase tracking-wider">Wajib DP 30% (Min 25k)</span>
                                            <span class="text-xl font-serif text-white">
                                                Rp {{ number_format($this->dpAmount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-sage text-right leading-tight max-w-[130px]">
                                            Sisa dibayar di kasir saat tiba
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Accounts & Proof Upload Card -->
                            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-forest/10 shadow-sm space-y-6" x-data="{ copied: false }">
                                <div class="border-b border-forest/10 pb-3">
                                    <span class="text-xs font-bold uppercase tracking-widest text-moss">Langkah 3</span>
                                    <h4 class="font-serif text-lg font-bold text-forest">Transfer Bank / QRIS</h4>
                                    <p class="text-xs text-forest/70 mt-0.5">
                                        Transfer nominal DP ke rekening resmi UM.CO House:
                                    </p>
                                </div>

                                <!-- Bank Accounts List -->
                                <div class="space-y-3">
                                    @foreach ($bankAccounts as $acc)
                                        <div class="p-3.5 rounded-2xl bg-oat/50 border border-forest/10 flex items-center justify-between">
                                            <div>
                                                <span class="text-[11px] font-bold uppercase tracking-wider text-moss block">{{ $acc['bank'] ?? 'Bank Transfer' }}</span>
                                                <span class="text-sm font-mono font-bold text-forest">{{ $acc['account_number'] ?? '-' }}</span>
                                                <span class="text-[10px] text-forest/60 block">a.n {{ $acc['account_name'] ?? 'UMCO HOUSE' }}</span>
                                            </div>
                                            <button
                                                type="button"
                                                @click="navigator.clipboard.writeText('{{ $acc['account_number'] ?? '' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white border border-forest/20 text-forest hover:bg-forest hover:text-white transition cursor-pointer"
                                            >
                                                Salin
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <div x-show="copied" x-transition class="p-2 rounded-lg bg-emerald-50 text-emerald-700 text-center text-xs font-semibold">
                                    Nomor rekening berhasil disalin!
                                </div>

                                <!-- Upload Payment Proof Input -->
                                <div class="space-y-2 pt-2 border-t border-forest/10">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-forest">
                                        Unggah Bukti Transfer <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        type="file"
                                        wire:model="payment_proof"
                                        accept="image/png,image/jpeg,image/webp,application/pdf"
                                        class="w-full text-xs text-forest/70 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-forest file:text-alabaster hover:file:bg-forest-deep transition cursor-pointer"
                                    />
                                    <div wire:loading wire:target="payment_proof" class="text-xs text-moss font-semibold">
                                        Mengunggah bukti transfer...
                                    </div>
                                    @error('payment_proof')
                                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                                    @enderror
                                    <p class="text-[11px] text-forest/60">
                                        Format JPG, PNG, WEBP, atau PDF. Maksimal 4 MB.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                @endif

                <!-- SUBMIT ACTION BAR -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-forest/10 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-forest/70 max-w-md">
                        Dengan menekan tombol di samping, Anda menyetujui ketentuan jam operasional dan batas toleransi kedatangan meja 15 menit.
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-8 py-4 rounded-full text-xs font-bold uppercase tracking-wider bg-forest text-alabaster hover:bg-forest-deep transition duration-200 shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="submit">
                            @if ($mode === 'b')
                                Konfirmasi &amp; Kirim Bukti DP Rp {{ number_format($this->dpAmount, 0, ',', '.') }} &rarr;
                            @else
                                Konfirmasi Reservasi Meja (Tanpa DP) &rarr;
                            @endif
                        </span>
                        <span wire:loading wire:target="submit">
                            Memproses Reservasi...
                        </span>
                    </button>
                </div>

            </form>

        @else
            <!-- STEP 2: INTERACTIVE DIGITAL SUCCESS TICKET -->
            <div class="max-w-2xl mx-auto" x-data="{ codeCopied: false }">

                <!-- Ticket Card -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-forest/15">

                    <!-- Ticket Header -->
                    <div class="bg-forest text-alabaster p-8 sm:p-10 text-center relative overflow-hidden">
                        <div class="absolute inset-0 pointer-events-none opacity-10">
                            <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-crema blur-2xl"></div>
                        </div>

                        <div class="w-16 h-16 rounded-full bg-white/10 text-crema mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-crema text-forest mb-2 inline-block">
                            Permintaan Reservasi Diterima
                        </span>
                        <h2 class="font-serif text-3xl sm:text-4xl font-bold mt-2">
                            Tiket Reservasi UM.CO
                        </h2>
                        <p class="text-xs sm:text-sm text-sage mt-2">
                            Tunjukkan kode booking ini kepada kasir saat Anda tiba di lokasi.
                        </p>

                        <!-- Big Booking Code Display -->
                        <div class="mt-6 inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm">
                            <span class="font-mono text-2xl sm:text-3xl font-black tracking-widest text-crema">
                                {{ $created_code }}
                            </span>
                            <button
                                type="button"
                                @click="navigator.clipboard.writeText('{{ $created_code }}'); codeCopied = true; setTimeout(() => codeCopied = false, 2500)"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-crema text-forest hover:bg-crema-dark transition cursor-pointer"
                            >
                                Salin
                            </button>
                        </div>
                        <p x-show="codeCopied" x-transition class="text-xs text-crema mt-2 font-medium">
                            Kode berhasil disalin ke clipboard!
                        </p>
                    </div>

                    <!-- Perforated Line Decoration -->
                    <div class="relative h-6 bg-oat flex items-center justify-between px-4 overflow-hidden">
                        <div class="w-6 h-6 rounded-full bg-oat -ml-7 border-r border-forest/15"></div>
                        <div class="flex-grow border-b-2 border-dashed border-forest/20 mx-4"></div>
                        <div class="w-6 h-6 rounded-full bg-oat -mr-7 border-l border-forest/15"></div>
                    </div>

                    <!-- Ticket Details Body -->
                    <div class="p-6 sm:p-10 space-y-6 bg-white">
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-forest/60 uppercase tracking-wider block">Atas Nama</span>
                                <span class="font-bold text-forest text-sm sm:text-base">{{ $created_reservation->name }}</span>
                            </div>
                            <div>
                                <span class="text-forest/60 uppercase tracking-wider block">Nomor WhatsApp</span>
                                <span class="font-bold text-forest text-sm sm:text-base">{{ $created_reservation->phone_input }}</span>
                            </div>
                            <div>
                                <span class="text-forest/60 uppercase tracking-wider block">Tanggal</span>
                                <span class="font-bold text-forest text-sm sm:text-base">{{ $created_reservation->date->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-forest/60 uppercase tracking-wider block">Jam Kedatangan</span>
                                <span class="font-bold text-forest text-sm sm:text-base">{{ $created_reservation->time }} WIB</span>
                            </div>
                            <div>
                                <span class="text-forest/60 uppercase tracking-wider block">Kapasitas Kursi</span>
                                <span class="font-bold text-forest text-sm sm:text-base">{{ $created_reservation->party_size }} Orang</span>
                            </div>
                            <div>
                                <span class="text-forest/60 uppercase tracking-wider block">Mode Reservasi</span>
                                <span class="font-bold text-forest text-sm sm:text-base">
                                    {{ $created_reservation->mode === 'table_menu' ? 'Mode B (Meja + Menu)' : 'Mode A (Meja Saja)' }}
                                </span>
                            </div>
                        </div>

                        <!-- Mode B Items breakdown if any -->
                        @if ($created_reservation->mode === 'table_menu' && $created_reservation->items->count() > 0)
                            <div class="pt-4 border-t border-forest/10 space-y-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-moss block">Pesanan Menu Pre-Order</span>
                                <div class="space-y-2 text-xs">
                                    @foreach ($created_reservation->items as $item)
                                        <div class="flex items-center justify-between text-forest/80">
                                            <span>{{ $item->name_snapshot }} &times; {{ $item->quantity }}</span>
                                            <span class="font-semibold text-forest">Rp {{ number_format($item->line_total, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="pt-3 border-t border-forest/10 flex items-center justify-between text-xs">
                                    <span class="font-semibold text-forest">Nominal DP (30%):</span>
                                    <span class="font-bold text-forest text-sm">{{ $created_reservation->formatted_dp }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Notes if any -->
                        @if ($created_reservation->note)
                            <div class="pt-4 border-t border-forest/10 text-xs">
                                <span class="text-forest/60 uppercase tracking-wider block mb-1">Catatan Khusus:</span>
                                <p class="text-forest/80 italic">&ldquo;{{ $created_reservation->note }}&rdquo;</p>
                            </div>
                        @endif

                        <!-- Important Information -->
                        <div class="p-4 rounded-2xl bg-oat/50 border border-forest/10 text-xs text-forest/70 space-y-1">
                            <p><strong>Ketentuan Hadir:</strong></p>
                            <ul class="list-disc list-inside space-y-0.5">
                                <li>Meja ditahan maksimal 15 menit setelah jam reservasi yang dipilih.</li>
                                <li>Tunjukkan kode booking di kasir untuk klaim meja Anda.</li>
                                <li>Untuk perubahan jadwal mendadak, hubungi kasir via WhatsApp.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-6 sm:p-10 bg-oat/30 border-t border-forest/10 flex flex-col sm:flex-row items-center gap-4">
                        <!-- Calendar Download (.ics) -->
                        <a
                            href="/reservasi/calendar/{{ $created_code }}"
                            class="w-full sm:w-auto flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-white border border-forest/20 text-forest hover:bg-forest/5 transition text-center shadow-sm"
                        >
                            <svg class="w-4 h-4 text-forest" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Simpan ke Kalender (.ics)</span>
                        </a>

                        <!-- WhatsApp Confirmation Link -->
                        @php
                            $waText = "Halo UM.CO House, saya sudah melakukan reservasi via website dengan Kode Booking: *{$created_code}* atas nama *{$created_reservation->name}* untuk tanggal *{$created_reservation->date->format('d/m/Y')}* jam *{$created_reservation->time} WIB* ({$created_reservation->party_size} orang). Mohon konfirmasinya ya. Terima kasih!";
                            $waLink = \App\Support\PhoneNumber::waLink(
                                \App\Support\Settings::get('contact.phone_wa', '082248001571'),
                                $waText
                            );
                        @endphp
                        <a
                            href="{{ $waLink }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full sm:w-auto flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-forest text-alabaster hover:bg-forest-deep transition text-center shadow-md"
                        >
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.666-.699c.974.553 1.838.835 2.802.835 3.18 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.821-5.776-5.821zm0-2.172c4.418 0 8 3.582 8 8 0 1.545-.44 2.99-1.203 4.215l1.172 4.285-4.387-1.151c-1.18.665-2.544 1.051-3.982 1.051-4.418 0-8-3.582-8-8 0-4.418 3.582-8.4 8.4-8.4z" />
                            </svg>
                            <span>Chat Kasir WhatsApp</span>
                        </a>
                    </div>

                    <!-- Reset / New Booking Link -->
                    <div class="px-6 py-4 bg-white border-t border-forest/10 text-center">
                        <button
                            type="button"
                            wire:click="resetBooking"
                            class="text-xs text-forest/60 hover:text-forest underline transition cursor-pointer"
                        >
                            Buat Reservasi Lainnya
                        </button>
                    </div>

                </div>

            </div>
        @endif

    </div>
</div>
