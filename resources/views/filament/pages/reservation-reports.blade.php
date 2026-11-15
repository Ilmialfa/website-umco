<x-filament-panels::page>
    <div class="umco-bo-stack">
        <x-filament::section>
            <div class="umco-report-filters">
                <div class="umco-bo-field">
                    <label for="repStartDate">Mulai Tanggal</label>
                    <input id="repStartDate" type="date" wire:model.live="startDate" class="umco-bo-control" />
                </div>
                <div class="umco-bo-field">
                    <label for="repEndDate">Sampai Tanggal</label>
                    <input id="repEndDate" type="date" wire:model.live="endDate" class="umco-bo-control" />
                </div>
                <div class="umco-bo-field">
                    <label for="repStatusFilter">Status Meja</label>
                    <select id="repStatusFilter" wire:model.live="statusFilter" class="umco-bo-control">
                        <option value="all">Semua Status</option>
                        <option value="confirmed">Dikonfirmasi Saja</option>
                        <option value="completed">Selesai</option>
                        <option value="pending">Menunggu</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="umco-bo-field">
                    <label for="repModeFilter">Tipe Reservasi</label>
                    <select id="repModeFilter" wire:model.live="modeFilter" class="umco-bo-control">
                        <option value="all">Semua Tipe</option>
                        <option value="table_menu">Meja + Menu (DP)</option>
                        <option value="table_only">Hanya Meja</option>
                    </select>
                </div>
                <div class="umco-report-actions">
                    <x-filament::button wire:click="exportCsv" color="primary" icon="heroicon-o-arrow-down-tray" class="w-full">
                        Ekspor CSV
                    </x-filament::button>
                    <button type="button" onclick="window.print()" class="umco-report-print-btn" title="Cetak / Print Laporan" aria-label="Cetak Laporan">
                        <x-heroicon-o-printer class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </x-filament::section>

        @php $data = $this->reportData; @endphp
        <div class="umco-report-summary">
            <div class="umco-stat-card">
                <p class="umco-stat-label">Total Reservasi</p>
                <p class="umco-stat-value">{{ $data['total_reservations'] }} Meja</p>
            </div>
            <div class="umco-stat-card">
                <p class="umco-stat-label">Total Kursi (Pax)</p>
                <p class="umco-stat-value">{{ $data['total_pax'] }} Orang</p>
            </div>
            <div class="umco-stat-card">
                <p class="umco-stat-label">Total Nilai Pesanan Menu</p>
                <p class="umco-stat-value umco-stat-value--accent">Rp {{ number_format($data['total_subtotal'], 0, ',', '.') }}</p>
            </div>
            <div class="umco-stat-card">
                <p class="umco-stat-label">Total DP Masuk</p>
                <p class="umco-stat-value umco-stat-value--accent">Rp {{ number_format($data['total_dp'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="umco-bo-table-wrap">
            <table class="umco-bo-table">
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Nama Tamu</th>
                        <th>WhatsApp</th>
                        <th>Jadwal</th>
                        <th style="text-align: center;">Pax</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th style="text-align: right;">Nominal DP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['records'] as $r)
                        <tr>
                            <td style="font-weight: 700;">{{ $r->code }}</td>
                            <td style="font-weight: 600;">{{ $r->name }}</td>
                            <td style="font-variant-numeric: tabular-nums;">{{ \App\Support\PhoneNumber::format($r->phone_wa) }}</td>
                            <td style="white-space: nowrap;">{{ $r->date->format('d M Y') }} · {{ str_replace(':', '.', substr((string) $r->time, 0, 5)) }} WIB</td>
                            <td style="text-align: center; font-weight: 600;">{{ $r->party_size }}</td>
                            <td>
                                <span class="umco-pill {{ $r->mode === 'table_menu' ? 'umco-pill--info' : 'umco-pill--neutral' }}">
                                    {{ $r->mode === 'table_menu' ? 'Meja + Menu' : 'Meja Saja' }}
                                </span>
                            </td>
                            <td>
                                <span class="umco-pill {{ match($r->status) {
                                    'confirmed' => 'umco-pill--success',
                                    'pending' => 'umco-pill--warning',
                                    'completed' => 'umco-pill--info',
                                    default => 'umco-pill--danger',
                                } }}">
                                    {{ $r->status_label }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700; font-variant-numeric: tabular-nums;">
                                Rp {{ number_format($r->dp_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 32px; color: var(--umco-tinta-70);">
                                Tidak ada data reservasi untuk rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
