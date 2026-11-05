<div class="min-h-screen bg-[#FAF7EE] pt-28 pb-20 lg:pt-36 lg:pb-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- WIZARD STEP INDICATOR (1 -> 2 -> 3) -->
        <div class="flex items-center justify-center gap-3 mb-10 text-xs font-bold uppercase tracking-wider">
            <!-- Step 1 -->
            <div class="flex items-center gap-2 {{ $slide >= 1 ? 'text-[#0D3B2E]' : 'text-zinc-400' }}">
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs {{ $slide === 1 ? 'bg-[#0D3B2E] text-white' : ($slide > 1 ? 'bg-[#F5B82E] text-[#07241C]' : 'bg-zinc-200 text-zinc-600') }}">
                    1
                </span>
                <span>Pilih Meja</span>
            </div>

            <span class="w-8 h-[2px] {{ $slide >= 2 ? 'bg-[#0D3B2E]' : 'bg-zinc-200' }}"></span>

            <!-- Step 2 -->
            <div class="flex items-center gap-2 {{ $slide >= 2 ? 'text-[#0D3B2E]' : 'text-zinc-400' }}">
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs {{ $slide === 2 ? 'bg-[#0D3B2E] text-white' : ($slide > 2 ? 'bg-[#F5B82E] text-[#07241C]' : 'bg-zinc-200 text-zinc-600') }}">
                    2
                </span>
                <span>Pesan Menu (Opsional)</span>
            </div>

            <span class="w-8 h-[2px] {{ $slide === 3 ? 'bg-[#0D3B2E]' : 'bg-zinc-200' }}"></span>

            <!-- Step 3 -->
            <div class="flex items-center gap-2 {{ $slide === 3 ? 'text-[#0D3B2E]' : 'text-zinc-400' }}">
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs {{ $slide === 3 ? 'bg-[#0D3B2E] text-white' : 'bg-zinc-200 text-zinc-600' }}">
                    3
                </span>
                <span>Tiket Selesai</span>
            </div>
        </div>

        <!-- SLIDE 1: FORMULIR DATA MEJA -->
        @if ($slide === 1)
            <div class="bg-white rounded-3xl p-6 sm:p-10 space-y-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Langkah Pertama</span>
                    <h1 class="font-serif text-3xl sm:text-4xl font-black text-[#0D3B2E] mt-1">
                        Reservasi Tempat Duduk
                    </h1>
                    <p class="text-xs sm:text-sm text-[#122018]/70 mt-1">
                        Amankan meja belajarmu di Kampus UMRI. Tanpa biaya muka, menu bisa dipesan saat tiba.
                    </p>
                </div>

                @if ($errors->has('general'))
                    <div class="p-4 rounded-2xl bg-rose-50 text-rose-700 text-xs font-medium">
                        {{ $errors->first('general') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Nama Pemesan -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#0D3B2E]">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            wire:model="name"
                            placeholder="Contoh: Muhammad Ihsan"
                            class="w-full px-4 py-3 bg-[#FAF7EE] rounded-2xl text-sm text-[#0D3B2E] placeholder:text-[#0D3B2E]/40 focus:outline-none focus:ring-2 focus:ring-[#F5B82E] transition"
                        />
                        @error('name')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-[#0D3B2E]">
                            Nomor WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            wire:model.live.debounce.400ms="phone"
                            placeholder="Contoh: 0812 3456 7890"
                            class="w-full px-4 py-3 bg-[#FAF7EE] rounded-2xl text-sm text-[#0D3B2E] placeholder:text-[#0D3B2E]/40 focus:outline-none focus:ring-2 focus:ring-[#F5B82E] transition"
                        />
                        @error('phone')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Kedatangan -->
                    <div class="space-y-1.5">
                        <label for="date" class="block text-xs font-bold uppercase tracking-wider text-[#0D3B2E]">
                            Tanggal Kedatangan <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="date"
                            wire:model.live="date"
                            min="{{ date('Y-m-d') }}"
                            max="{{ now()->addDays(14)->format('Y-m-d') }}"
                            class="w-full px-4 py-3 bg-[#FAF7EE] rounded-2xl text-sm text-[#0D3B2E] focus:outline-none focus:ring-2 focus:ring-[#F5B82E] transition"
                        />
                        @if ($closedDate)
                            <div class="p-3 rounded-xl bg-amber-50 text-amber-800 text-xs">
                                <strong>Tutup:</strong> {{ $closedDate->reason ?: 'Operasional Libur' }}
                            </div>
                        @endif
                        @error('date')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Orang -->
                    <div class="space-y-1.5">
                        <label for="party_size" class="block text-xs font-bold uppercase tracking-wider text-[#0D3B2E]">
                            Jumlah Orang (Pax) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2 flex-wrap">
                            <input
                                type="number"
                                id="party_size"
                                wire:model.live="party_size"
                                min="1"
                                max="12"
                                class="w-20 px-3 py-3 bg-[#FAF7EE] rounded-2xl text-sm font-bold text-center text-[#0D3B2E] focus:outline-none focus:ring-2 focus:ring-[#F5B82E]"
                            />
                            <div class="flex items-center gap-1.5">
                                @foreach ([2, 4, 6, 8] as $pax)
                                    <button
                                        type="button"
                                        wire:click="$set('party_size', {{ $pax }})"
                                        class="px-3 py-2 rounded-xl text-xs font-semibold cursor-pointer transition {{ $party_size === $pax ? 'bg-[#0D3B2E] text-white' : 'bg-[#FAF7EE] text-[#0D3B2E] hover:bg-[#F4EEDF]' }}"
                                    >
                                        {{ $pax }} pax
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('party_size')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Jam Kedatangan (Pills Slot 30 Menit) -->
                <div class="pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#0D3B2E]">
                            Pilih Jam (Slot 30 Menit) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-xs text-[#122018]/50">Kapasitas 40 kursi / slot</span>
                    </div>

                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                        @foreach ($availableSlots as $slot)
                            <button
                                type="button"
                                @if ($slot['is_full']) disabled @else wire:click="$set('time', '{{ $slot['time'] }}')" @endif
                                class="p-2.5 rounded-2xl text-center transition flex flex-col items-center justify-center cursor-pointer {{ $slot['is_full'] ? 'opacity-30 bg-zinc-100 text-zinc-400 cursor-not-allowed' : ($time === $slot['time'] ? 'bg-[#0D3B2E] text-white' : 'bg-[#FAF7EE] hover:bg-[#F4EEDF] text-[#0D3B2E]') }}"
                            >
                                <span class="text-sm font-bold">{{ $slot['time'] }}</span>
                                <span class="text-[10px] {{ $slot['is_full'] ? 'text-rose-500' : ($time === $slot['time'] ? 'text-[#F5B82E]' : 'text-[#122018]/50') }}">
                                    {{ $slot['is_full'] ? 'Penuh' : $slot['remaining'] . ' sisa' }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                    @error('time')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Meja -->
                <div class="space-y-1.5">
                    <label for="note" class="block text-xs font-bold uppercase tracking-wider text-[#0D3B2E]">
                        Catatan Khusus (Opsional)
                    </label>
                    <input
                        type="text"
                        id="note"
                        wire:model="note"
                        placeholder="Misal: Meja dekat stopkontak di Wing Barat"
                        class="w-full px-4 py-3 bg-[#FAF7EE] rounded-2xl text-sm text-[#0D3B2E] placeholder:text-[#0D3B2E]/40 focus:outline-none focus:ring-2 focus:ring-[#F5B82E]"
                    />
                </div>

                <!-- Next Button -->
                <div class="pt-6 border-t border-[#EAE2D0] flex items-center justify-between">
                    <span class="text-xs text-[#122018]/50">Langkah 1 dari 2</span>
                    <button
                        type="button"
                        wire:click="goToSlide(2)"
                        class="px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#0D3B2E] text-white hover:bg-[#07241C] transition cursor-pointer"
                    >
                        Lanjut ke Pilihan Menu &rarr;
                    </button>
                </div>
            </div>

        <!-- SLIDE 2: PILIHAN MENU (MAU SEKALIAN PESAN ATAU LEWATI?) -->
        @elseif ($slide === 2)
            <div class="space-y-8">

                <!-- Header & Skip Option -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#145341]">Langkah Kedua (Opsional)</span>
                        <h2 class="font-serif text-2xl sm:text-3xl font-black text-[#0D3B2E] mt-0.5">
                            Mau Sekalian Pesan Menu?
                        </h2>
                        <p class="text-xs sm:text-sm text-[#122018]/70 mt-1">
                            Pesan sekarang agar kopi &amp; makananmu siap saat kamu sampai di meja.
                        </p>
                    </div>

                    <!-- ONE-CLICK SKIP BUTTON (TABLE ONLY) -->
                    <button
                        type="button"
                        wire:click="confirmTableOnly"
                        class="w-full md:w-auto px-6 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#FAF7EE] text-[#0D3B2E] hover:bg-[#EAE2D0] transition cursor-pointer flex-shrink-0"
                    >
                        Lewati &amp; Reservasi Meja Saja &rarr;
                    </button>
                </div>

                <!-- Menu Item Catalog Grid -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 space-y-8">
                    <div class="flex items-center justify-between border-b border-[#EAE2D0] pb-4">
                        <h3 class="font-serif text-xl font-bold text-[#0D3B2E]">Daftar Menu Siap Saji</h3>
                        <span class="text-xs text-[#122018]/60">Pilih menu dengan menekan tombol (+)</span>
                    </div>

                    @error('selected_items')
                        <div class="p-3.5 rounded-2xl bg-rose-50 text-rose-700 text-xs font-medium">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="space-y-8">
                        @foreach ($menuCategories as $catName => $items)
                            <div>
                                <h4 class="font-serif text-lg font-bold text-[#0D3B2E] mb-4 text-[#145341]">
                                    {{ $catName }}
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($items as $item)
                                        @php $qty = $selected_items[$item->id] ?? 0; @endphp
                                        <div class="p-3.5 rounded-2xl flex items-center justify-between transition {{ $qty > 0 ? 'bg-[#FAF7EE]' : 'bg-[#FAF7EE]/50 hover:bg-[#FAF7EE]' }}">
                                            <div class="flex items-center gap-3 min-w-0 pr-2">
                                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-white flex-shrink-0">
                                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover" loading="lazy" />
                                                </div>
                                                <div class="truncate">
                                                    <h5 class="text-xs sm:text-sm font-bold text-[#0D3B2E] truncate">{{ $item->name }}</h5>
                                                    <span class="text-xs font-mono font-semibold text-[#145341]">{{ $item->formatted_price }}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                @if ($qty > 0)
                                                    <button
                                                        type="button"
                                                        wire:click="removeItem({{ $item->id }})"
                                                        class="w-7 h-7 rounded-full bg-white text-[#0D3B2E] flex items-center justify-center font-bold text-xs cursor-pointer hover:bg-zinc-100"
                                                    >
                                                        &minus;
                                                    </button>
                                                    <span class="w-5 text-center text-xs font-bold text-[#0D3B2E]">{{ $qty }}</span>
                                                @endif
                                                <button
                                                    type="button"
                                                    wire:click="addItem({{ $item->id }})"
                                                    class="w-7 h-7 rounded-full bg-[#0D3B2E] text-white flex items-center justify-center font-bold text-xs cursor-pointer hover:bg-[#07241C]"
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

                <!-- IF ITEMS SELECTED: PAYMENT DETAILS & SUBMIT WITH MENU -->
                @if (!empty($selected_items) && array_sum($selected_items) > 0)
                    <div class="bg-[#0D3B2E] text-white rounded-3xl p-6 sm:p-8 space-y-6" x-data="{ copied: false }">
                        <div class="border-b border-white/10 pb-4">
                            <span class="text-xs font-bold uppercase tracking-widest text-[#F5B82E]">Ringkasan Pembayaran</span>
                            <h3 class="font-serif text-2xl font-bold mt-1">DP 30% Pre-Order</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <!-- Left: Totals -->
                            <div class="space-y-3 text-xs">
                                <div class="flex items-center justify-between text-white/70">
                                    <span>Total Pesanan Menu:</span>
                                    <span class="font-bold text-white text-sm">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="p-4 rounded-2xl bg-white/10 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] uppercase tracking-wider text-[#F5B82E] font-bold block">Wajib DP 30% (Min 25k)</span>
                                        <span class="font-serif text-2xl font-black text-white">Rp {{ number_format($this->dpAmount, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="text-[11px] text-[#9AB4A6] text-right">Sisa dibayar di kasir</span>
                                </div>

                                <!-- Bank Accounts -->
                                <div class="pt-2 space-y-2">
                                    <span class="text-[11px] uppercase tracking-wider text-[#F5B82E] font-bold block">Transfer ke Rekening UM.CO:</span>
                                    @foreach ($bankAccounts as $acc)
                                        <div class="p-3 rounded-xl bg-white/5 flex items-center justify-between">
                                            <div>
                                                <span class="text-[10px] text-[#9AB4A6] block">{{ $acc['bank'] ?? 'Bank' }}</span>
                                                <span class="font-mono font-bold text-xs text-white">{{ $acc['account_number'] ?? '-' }}</span>
                                            </div>
                                            <button
                                                type="button"
                                                @click="navigator.clipboard.writeText('{{ $acc['account_number'] ?? '' }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-[#F5B82E] text-[#07241C] cursor-pointer"
                                            >
                                                Salin
                                            </button>
                                        </div>
                                    @endforeach
                                    <p x-show="copied" x-transition class="text-xs text-[#F5B82E] text-center font-semibold">Nomor rekening berhasil disalin!</p>
                                </div>
                            </div>

                            <!-- Right: Proof Upload -->
                            <div class="space-y-3 text-xs bg-white/5 p-5 rounded-2xl">
                                <label class="block font-bold uppercase tracking-wider text-[#F5B82E]">
                                    Unggah Bukti Transfer DP <span class="text-rose-400">*</span>
                                </label>
                                <input
                                    type="file"
                                    wire:model="payment_proof"
                                    accept="image/*,application/pdf"
                                    class="w-full text-xs text-white/70 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#F5B82E] file:text-[#07241C] cursor-pointer"
                                />
                                <div wire:loading wire:target="payment_proof" class="text-xs text-[#F5B82E]">
                                    Mengunggah bukti...
                                </div>
                                @error('payment_proof')
                                    <p class="text-xs text-rose-400 font-medium">{{ $message }}</p>
                                @enderror
                                <p class="text-[11px] text-[#9AB4A6]">
                                    Format JPG, PNG, atau PDF. Maksimal 4 MB.
                                </p>
                            </div>
                        </div>

                        <!-- Confirm with Menu Button -->
                        <div class="pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <button
                                type="button"
                                wire:click="goToSlide(1)"
                                class="text-xs text-white/60 hover:text-white underline cursor-pointer"
                            >
                                &larr; Ubah Data Meja
                            </button>

                            <button
                                type="button"
                                wire:click="confirmWithMenu"
                                wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition cursor-pointer"
                            >
                                <span wire:loading.remove wire:target="confirmWithMenu">
                                    Konfirmasi &amp; Kirim Bukti DP &rarr;
                                </span>
                                <span wire:loading wire:target="confirmWithMenu">
                                    Menyimpan Reservasi...
                                </span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Navigation Back to Slide 1 -->
                <div class="text-center pt-2">
                    <button
                        type="button"
                        wire:click="goToSlide(1)"
                        class="text-xs text-[#0D3B2E]/70 hover:text-[#0D3B2E] underline cursor-pointer"
                    >
                        &larr; Kembali ke Pemilihan Meja
                    </button>
                </div>

            </div>

        <!-- SLIDE 3: TIKET RESERVASI SELESAI -->
        @elseif ($slide === 3)
            <div class="max-w-2xl mx-auto" x-data="{ codeCopied: false }">
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm">
                    <!-- Ticket Header -->
                    <div class="bg-[#0D3B2E] text-white p-8 text-center relative">
                        <div class="w-14 h-14 rounded-full bg-[#F5B82E] text-[#07241C] mx-auto mb-3 flex items-center justify-center font-bold text-xl">
                            ✓
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full bg-white/10 text-[#F5B82E] inline-block mb-1">
                            Reservasi Berhasil
                        </span>
                        <h2 class="font-serif text-3xl font-black mt-1">Tiket Reservasi UM.CO</h2>
                        <p class="text-xs text-[#9AB4A6] mt-1">Tunjukkan kode ini kepada kasir saat kamu tiba.</p>

                        <!-- Big Code Pill -->
                        <div class="mt-6 inline-flex items-center gap-3 px-6 py-2.5 rounded-full bg-white/10 border border-white/20">
                            <span class="font-mono text-2xl font-black tracking-widest text-[#F5B82E]">
                                {{ $created_code }}
                            </span>
                            <button
                                type="button"
                                @click="navigator.clipboard.writeText('{{ $created_code }}'); codeCopied = true; setTimeout(() => codeCopied = false, 2000)"
                                class="px-3 py-1 rounded-full text-xs font-bold bg-[#F5B82E] text-[#07241C] cursor-pointer"
                            >
                                Salin
                            </button>
                        </div>
                        <p x-show="codeCopied" x-transition class="text-xs text-[#F5B82E] mt-2 font-semibold">Kode disalin ke clipboard!</p>
                    </div>

                    <!-- Ticket Details -->
                    <div class="p-6 sm:p-8 space-y-4 bg-white text-xs">
                        <div class="grid grid-cols-2 gap-4 pb-4 border-b border-[#EAE2D0]">
                            <div>
                                <span class="text-[#122018]/50 uppercase tracking-wider block">Atas Nama</span>
                                <span class="font-bold text-sm text-[#0D3B2E]">{{ $created_reservation->name }}</span>
                            </div>
                            <div>
                                <span class="text-[#122018]/50 uppercase tracking-wider block">No WhatsApp</span>
                                <span class="font-bold text-sm text-[#0D3B2E]">{{ $created_reservation->phone_input }}</span>
                            </div>
                            <div>
                                <span class="text-[#122018]/50 uppercase tracking-wider block">Tanggal</span>
                                <span class="font-bold text-sm text-[#0D3B2E]">{{ $created_reservation->date->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-[#122018]/50 uppercase tracking-wider block">Jam Datang</span>
                                <span class="font-bold text-sm text-[#0D3B2E]">{{ $created_reservation->time }} WIB</span>
                            </div>
                            <div>
                                <span class="text-[#122018]/50 uppercase tracking-wider block">Kapasitas</span>
                                <span class="font-bold text-sm text-[#0D3B2E]">{{ $created_reservation->party_size }} Orang</span>
                            </div>
                            <div>
                                <span class="text-[#122018]/50 uppercase tracking-wider block">Tipe Booking</span>
                                <span class="font-bold text-sm text-[#0D3B2E]">
                                    {{ $created_reservation->mode === 'table_menu' ? 'Meja + Pre-Order Menu' : 'Meja Saja (Tanpa DP)' }}
                                </span>
                            </div>
                        </div>

                        <!-- Ordered items if any -->
                        @if ($created_reservation->mode === 'table_menu' && $created_reservation->items->count() > 0)
                            <div class="py-2 border-b border-[#EAE2D0] space-y-1">
                                <span class="font-bold text-[#145341] block">Pesanan Menu:</span>
                                @foreach ($created_reservation->items as $item)
                                    <div class="flex items-center justify-between text-[#122018]/80">
                                        <span>{{ $item->name_snapshot }} &times; {{ $item->quantity }}</span>
                                        <span class="font-mono">Rp {{ number_format($item->line_total, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                                <div class="pt-2 flex items-center justify-between font-bold text-[#0D3B2E]">
                                    <span>DP 30% Terverifikasi:</span>
                                    <span>{{ $created_reservation->formatted_dp }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="p-3.5 rounded-2xl bg-[#FAF7EE] text-[#122018]/70 space-y-1">
                            <p><strong>Batas Toleransi:</strong> Meja disimpan maksimal 15 menit dari jam yang dipilih.</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-6 bg-[#FAF7EE] flex flex-col sm:flex-row items-center gap-3">
                        <a
                            href="/reservasi/calendar/{{ $created_code }}"
                            class="w-full sm:w-auto flex-1 text-center py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-white text-[#0D3B2E] hover:bg-[#F4EEDF] transition"
                        >
                            Simpan ke Kalender (.ics)
                        </a>

                        @php
                            $waText = "Halo UM.CO House, saya sudah reservasi dengan Kode: *{$created_code}* atas nama *{$created_reservation->name}* untuk tanggal *{$created_reservation->date->format('d/m/Y')}* jam *{$created_reservation->time} WIB*. Mohon dicatat ya. Terima kasih!";
                            $waLink = \App\Support\PhoneNumber::waLink(
                                \App\Support\Settings::get('contact.phone_wa', '082248001571'),
                                $waText
                            );
                        @endphp
                        <a
                            href="{{ $waLink }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full sm:w-auto flex-1 text-center py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-[#F5B82E] text-[#07241C] hover:bg-[#D99B16] transition"
                        >
                            Chat Kasir WhatsApp
                        </a>
                    </div>

                    <div class="p-4 text-center bg-white border-t border-[#EAE2D0]">
                        <button
                            type="button"
                            wire:click="resetBooking"
                            class="text-xs text-[#0D3B2E]/60 hover:text-[#0D3B2E] underline cursor-pointer"
                        >
                            Buat Reservasi Baru
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
