<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-600">
                Isi tingkat keramaian kedai 0–100 untuk tiap hari (Senin–Minggu) pada jam 07.00–22.00. Grafik ini tampil di beranda.
            </p>
            <div class="flex items-center gap-3">
                <x-filament::button color="gray" wire:click="fillExample">
                    Isi contoh
                </x-filament::button>
                <x-filament::button wire:click="save">
                    Simpan perubahan
                </x-filament::button>
            </div>
        </div>

        <div class="overflow-x-auto bg-[#FCFBF7] border border-[#DCD7CB] p-4">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="border-b border-[#DCD7CB]">
                        <th class="text-left py-2 pr-3 font-bold">Hari / Jam</th>
                        @for ($h = 7; $h <= 22; $h++)
                            <th class="px-1 py-2 text-center font-semibold">{{ sprintf('%02d', $h) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EBE7DC]">
                    @foreach (\App\Filament\Pages\CrowdPatternPage::dayLabels() as $dayNum => $dayName)
                        <tr>
                            <td class="py-2 pr-3 font-semibold whitespace-nowrap">{{ $dayName }}</td>
                            @for ($h = 7; $h <= 22; $h++)
                                <td class="p-1">
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        wire:model="grid.{{ $dayNum }}.{{ $h }}"
                                        class="w-12 text-center py-1 px-1 text-xs border border-[#DCD7CB] bg-white focus:border-[#22263A] focus:outline-none"
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
