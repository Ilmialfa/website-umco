<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6 pb-8">
        {{ $this->form }}

        <div class="umco-sticky-save-bar">
            <p class="text-xs sm:text-sm text-slate-600">
                Perubahan diterapkan ke situs publik segera setelah disimpan.
            </p>
            <x-filament::button type="submit" size="lg" color="primary" icon="heroicon-o-check">
                Simpan Seluruh Pengaturan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
