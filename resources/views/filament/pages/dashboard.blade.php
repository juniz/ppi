<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="h-[400px]">
            @livewire('\App\Filament\Resources\DataHaisResource\Widgets\HaisHarianInfeksiChart')
        </div>
        <div class="h-[400px]">
            @livewire('\App\Filament\Resources\DataHaisResource\Widgets\HaisHarianAlatChart')
        </div>
    </div>
    
    <div class="mt-8">
        @livewire('\App\Filament\Widgets\StatusInputHaisTable')
    </div>
</x-filament-panels::page>