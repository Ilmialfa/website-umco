<x-filament-panels::page>
    <div class="umco-bo-stack">
        <div class="umco-crowd-header">
            <p class="umco-crowd-desc">
                Isi tingkat keramaian kedai 0–100 untuk tiap hari (Senin–Minggu) pada jam 07.00–22.00. Grafik ini tampil di beranda.
            </p>
            <div class="umco-crowd-actions">
                <x-filament::button color="gray" wire:click="fillExample">
                    Isi contoh
                </x-filament::button>
                <x-filament::button wire:click="save">
                    Simpan perubahan
                </x-filament::button>
            </div>
        </div>

        <div class="umco-crowd-card">
            <table class="umco-crowd-table">
                <thead>
                    <tr>
                        <th>Hari / Jam</th>
                        @for ($h = 7; $h <= 22; $h++)
                            <th>{{ sprintf('%02d', $h) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Filament\Pages\CrowdPatternPage::dayLabels() as $dayNum => $dayName)
                        <tr>
                            <td class="umco-crowd-day">{{ $dayName }}</td>
                            @for ($h = 7; $h <= 22; $h++)
                                <td>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        aria-label="{{ $dayName }} jam {{ sprintf('%02d', $h) }}"
                                        wire:model="grid.{{ $dayNum }}.{{ $h }}"
                                        class="umco-crowd-input"
                                    >
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
