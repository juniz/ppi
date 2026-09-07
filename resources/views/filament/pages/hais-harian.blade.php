<x-filament-panels::page>
    <div class="mb-4">
        <form id="applyFilters" wire:submit="applyFilters">
            {{ $this->form }}
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @livewire(\App\Filament\Resources\DataHaisResource\Widgets\HaisHarianInfeksiChart::class, ['filters' => $filters], key('hais-infeksi-chart-' . md5(json_encode($filters))))
        @livewire(\App\Filament\Resources\DataHaisResource\Widgets\HaisHarianAlatChart::class, ['filters' => $filters], key('hais-alat-chart-' . md5(json_encode($filters))))
    </div>

    {{ $this->table }}
</x-filament-panels::page>
