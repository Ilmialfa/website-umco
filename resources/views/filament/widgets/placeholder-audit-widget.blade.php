<x-filament-widgets::widget>
    <div class="umco-audit-card">
        <div class="umco-audit-header">
            <div>
                <h3 class="umco-audit-title">Checklist Kesiapan Konten & Pembayaran</h3>
                <p class="umco-audit-subtitle">Daftar data sementara yang perlu dilengkapi pemilik sebelum rilis penuh.</p>
            </div>
            @if (count($placeholderItems) > 0)
                <span class="umco-audit-count">{{ count($placeholderItems) }} hal perlu dicek</span>
            @endif
        </div>

        @if (count($placeholderItems) > 0)
            <div class="umco-audit-list">
                @foreach ($placeholderItems as $item)
                    @php
                        $levelClass = match ($item['type']) {
                            'critical' => 'umco-audit-item--critical',
                            'warning' => 'umco-audit-item--warning',
                            default => '',
                        };
                        $levelLabel = match ($item['type']) {
                            'critical' => 'Perlu segera diisi',
                            'warning' => 'Belum diunggah',
                            default => 'Catatan konten',
                        };
                    @endphp
                    <div class="umco-audit-item {{ $levelClass }}">
                        <div>
                            <div class="umco-audit-item-meta">
                                <span class="umco-audit-dot"></span>
                                <span class="umco-audit-level">{{ $levelLabel }}</span>
                            </div>
                            <h4 class="umco-audit-item-title">{{ $item['title'] }}</h4>
                            <p class="umco-audit-item-desc">{{ $item['description'] }}</p>
                        </div>

                        <a href="{{ $item['action_url'] }}" class="umco-audit-btn">
                            {{ $item['action_label'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="umco-audit-empty">
                Semua aset pembayaran, foto menu, dan ulasan sudah terisi rapi.
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
