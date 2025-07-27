<x-filament-panels::page>
    <div class="mb-8 p-6 bg-white rounded-xl shadow-sm">
        {{ $this->form }}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- HAP Card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-primary-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-primary-100">
                        <x-heroicon-o-heart class="w-6 h-6 text-primary-600"/>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Laju HAP</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                @include('filament.pages.partials.table', ['data' => $dataHAP, 'type' => 'HAP'])
            </div>
        </div>

        {{-- IAD Card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-success-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-success-100">
                        <x-heroicon-o-beaker class="w-6 h-6 text-success-600"/>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Laju IAD</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                @include('filament.pages.partials.table', ['data' => $dataIAD, 'type' => 'IAD'])
            </div>
        </div>

        {{-- ILO Card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-warning-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-warning-100">
                        <x-heroicon-o-scissors class="w-6 h-6 text-warning-600"/>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Laju ILO</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                @include('filament.pages.partials.table', ['data' => $dataILO, 'type' => 'ILO'])
            </div>
        </div>

        {{-- ISK Card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-info-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-info-100">
                        <x-heroicon-o-flag class="w-6 h-6 text-info-600"/>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Laju ISK</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                @include('filament.pages.partials.table', ['data' => $dataISK, 'type' => 'ISK'])
            </div>
        </div>

        {{-- PLEBITIS Card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-danger-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-danger-100">
                        <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-danger-600"/>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Laju PLEBITIS</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                @include('filament.pages.partials.table', ['data' => $dataPLEB, 'type' => 'PLEB'])
            </div>
        </div>

        {{-- VAP Card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-secondary-50">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-lg bg-secondary-100">
                        <x-heroicon-o-variable class="w-6 h-6 text-secondary-600"/>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Laju VAP</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                @include('filament.pages.partials.table', ['data' => $dataVAP, 'type' => 'VAP'])
            </div>
        </div>
    </div>

    {{-- Tambahkan CSS kustom --}}
    <style>
        .filament-tables-table-container {
            @apply rounded-xl shadow-sm border border-gray-200;
        }
        
        .filament-tables-header-cell {
            @apply bg-gray-50 text-gray-600 font-medium;
        }

        .filament-tables-row {
            @apply hover:bg-gray-50 transition-colors;
        }

        .filament-tables-cell {
            @apply p-3;
        }
    </style>
</x-filament-panels::page>