<x-filament-panels::page>
    {{-- Form Filter --}}
    <div class="mb-8 p-6 bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-blue-600">
        {{ $this->form }}
    </div>

    {{-- Grafik Analisis --}}
    <div class="mb-8 p-6 bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-blue-600">
        
        <div class="flex flex-col xl:flex-row gap-6" style="min-height: 500px;">
            {{-- Grafik Infeksi HAIs --}}
            <div class="flex-1" style="min-height: 500px;">
                @livewire(\App\Filament\Widgets\AnalisaInfeksiChart::class, key('infeksi-chart-' . now()->timestamp))
            </div>

            {{-- Grafik Pemasangan Alat --}}
            <div class="flex-1" style="min-height: 500px;">
                @livewire(\App\Filament\Widgets\AnalisaPemasanganAlatChart::class, key('alat-chart-' . now()->timestamp))
            </div>
        </div>
    </div>

    {{-- Data Tables --}}
    <div class="mb-8 p-6 bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-blue-600">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- HAP Card --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
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
    </div>

    {{-- Analisis dan Rekomendasi Cards --}}
    <div class="mb-8 p-6 bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-orange-500">
        <div class="flex items-center space-x-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Buat Analisis dan Rekomendasi</h3>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Analisa Card --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-blue-50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-lg bg-blue-100">
                            <x-heroicon-o-chart-bar class="w-6 h-6 text-blue-600"/>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Analisis</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Analisis Data HAIs</label>
                            <textarea 
                                wire:model="analisa"
                                rows="6" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-none"
                                placeholder="Masukkan analisis berdasarkan data HAIs yang ditampilkan..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rekomendasi Card --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-green-50">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-lg bg-green-100">
                            <x-heroicon-o-light-bulb class="w-6 h-6 text-green-600"/>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Rekomendasi</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rekomendasi Tindakan</label>
                            <textarea 
                                wire:model="rekomendasi"
                                rows="6" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 resize-none"
                                placeholder="Masukkan rekomendasi tindakan untuk mengurangi laju HAIs..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tombol Simpan dan Data Analisa di bagian bawah --}}
        <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
            {{-- Tombol Data Analisa & Rekomendasi --}}
            <button 
                wire:click="redirectToDataAnalisa"
                type="button" 
                class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-sm font-semibold rounded-lg shadow-sm bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 ease-in-out"
            >
                <x-heroicon-o-document-text class="w-5 h-5 mr-2" />
                Data Analisa & Rekomendasi
            </button>
            
            {{-- Tombol Simpan dengan Loading State --}}
            <button 
                wire:click="saveAnalisaRekomendasi"
                type="button" 
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
                wire:target="saveAnalisaRekomendasi"
            >
                <div wire:loading.remove wire:target="saveAnalisaRekomendasi" class="flex items-center">
                    <x-heroicon-o-check class="w-5 h-5 mr-2" />
                    Simpan Analisis dan Rekomendasi
                </div>
                <div wire:loading wire:target="saveAnalisaRekomendasi" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                </div>
            </button>
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

    {{-- JavaScript untuk menyimpan chart sebagai PNG --}}
    <script>
        // Listen untuk event setelah data berhasil disimpan
        document.addEventListener('livewire:init', () => {
            Livewire.on('analisa-saved', (event) => {
                const analisaId = event[0].id;
                generateAndSaveCharts(analisaId);
            });
        });

        function generateAndSaveCharts(analisaId) {
            console.log('Generating charts for analisa ID:', analisaId);
            
            const chartTypes = ['infeksi', 'pemasangan'];
            
            chartTypes.forEach(chartType => {
                console.log('Creating iframe for chart type:', chartType);
                
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.style.width = '1200px';
                iframe.style.height = '600px';
                
                const chartUrl = `/chart/${chartType}?analisa_id=${analisaId}`;
                console.log('Chart URL:', chartUrl);
                
                iframe.src = chartUrl;
                document.body.appendChild(iframe);
                
                // Tunggu lebih lama untuk memastikan chart ter-render dan tersimpan
                setTimeout(() => {
                    console.log('Removing iframe for chart type:', chartType);
                    document.body.removeChild(iframe);
                }, 15000); // Increased from 10 seconds to 15 seconds
            });
        }

        // Function untuk menampilkan notifikasi
        function showNotification(message, type = 'success') {
            // Menggunakan Filament notification system
            window.$wireui.notify({
                title: type === 'success' ? 'Berhasil' : 'Error',
                description: message,
                icon: type === 'success' ? 'success' : 'error'
            });
        }
    </script>
</x-filament-panels::page>