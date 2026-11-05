<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold">
                    !
                </span>
                <span class="font-bold text-base">Aset & Data Perlu Diganti (Placeholder Detector)</span>
            </div>
        </x-slot>
        <x-slot name="description">
            Daftar data dan aset sementara yang perlu dikonfirmasi dari pemilik kafe sebelum peluncuran resmi.
        </x-slot>

        @if(count($placeholderItems) === 0)
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
                <x-heroicon-m-check-circle class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>Semua data dan aset website telah lengkap! Tidak ada placeholder sementara yang terdeteksi.</span>
            </div>
        @else
            <div class="space-y-3">
                @foreach($placeholderItems as $item)
                    <div class="p-4 rounded-xl border flex flex-col md:flex-row md:items-center justify-between gap-3 {{ match($item['type']) {
                        'critical' => 'bg-rose-500/5 border-rose-500/20 dark:bg-rose-950/20',
                        'warning' => 'bg-amber-500/5 border-amber-500/20 dark:bg-amber-950/20',
                        default => 'bg-sky-500/5 border-sky-500/20 dark:bg-sky-950/20',
                    } }}">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase {{ match($item['type']) {
                                    'critical' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300',
                                    'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                                    default => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
                                } }}">
                                    {{ match($item['type']) { 'critical' => 'Penting', 'warning' => 'Perhatian', default => 'Info' } }}
                                </span>
                                <h4 class="font-semibold text-sm text-gray-900 dark:text-white">{{ $item['title'] }}</h4>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $item['description'] }}</p>
                        </div>
                        <a href="{{ $item['action_url'] }}" class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg bg-gray-900 text-white dark:bg-white dark:text-gray-900 text-xs font-semibold hover:opacity-90 transition shrink-0">
                            {{ $item['action_label'] }} &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
