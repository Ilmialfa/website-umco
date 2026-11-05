<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Bar -->
        <x-filament::section>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Mulai Tanggal</label>
                    <input type="date" wire:model.live="startDate" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-white px-3 py-2" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                    <input type="date" wire:model.live="endDate" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-white px-3 py-2" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Status Meja</label>
                    <select wire:model.live="statusFilter" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-white px-3 py-2">
                        <option value="all">Semua Status</option>
                        <option value="confirmed">Dikonfirmasi Saja</option>
                        <option value="completed">Selesai</option>
                        <option value="pending">Menunggu</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tipe Reservasi</label>
                    <select wire:model.live="modeFilter" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-white px-3 py-2">
                        <option value="all">Semua Tipe</option>
                        <option value="table_menu">Meja + Menu (DP)</option>
                        <option value="table_only">Hanya Meja</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <x-filament::button wire:click="exportCsv" color="success" icon="heroicon-o-arrow-down-tray" class="w-full">
                        Ekspor CSV
                    </x-filament::button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300" title="Cetak / Print Laporan">
                        <x-heroicon-o-printer class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </x-filament::section>

        <!-- Summary Cards -->
        @php $data = $this->reportData; @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Reservasi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $data['total_reservations'] }} Meja</p>
            </div>
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Kursi (Pax)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $data['total_pax'] }} Orang</p>
            </div>
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Nilai Pesanan Menu</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($data['total_subtotal'], 0, ',', '.') }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total DP Masuk</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">Rp {{ number_format($data['total_dp'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Printable Report Table -->
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400 divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 text-xs text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Kode Booking</th>
                            <th class="py-3 px-4">Nama Tamu</th>
                            <th class="py-3 px-4">WhatsApp</th>
                            <th class="py-3 px-4">Tanggal & Jam</th>
                            <th class="py-3 px-4 text-center">Pax</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">DP Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($data['records'] as $r)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20">
                                <td class="py-3 px-4 font-mono font-bold text-gray-900 dark:text-white">{{ $r->code }}</td>
                                <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $r->name }}</td>
                                <td class="py-3 px-4 font-mono">{{ $r->phone_wa }}</td>
                                <td class="py-3 px-4">{{ $r->date->format('d/m/Y') }} {{ $r->time }} WIB</td>
                                <td class="py-3 px-4 text-center">{{ $r->party_size }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $r->mode === 'table_menu' ? 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                        {{ $r->mode === 'table_menu' ? 'Meja + Menu' : 'Meja Saja' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ match($r->status) {
                                        'confirmed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        default => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                                    } }}">
                                        {{ $r->status_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($r->dp_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">
                                    Tidak ada data reservasi untuk rentang tanggal yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
