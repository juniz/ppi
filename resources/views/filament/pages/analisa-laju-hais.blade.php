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
    </div>

    {{-- Save Section --}}
    <div class="mb-8 p-6 bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 border-l-green-600">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Simpan Analisis dan Rekomendasi</h3>
                    <p class="text-sm text-gray-600">Pastikan analisis dan rekomendasi sudah lengkap sebelum menyimpan</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button 
                    wire:click.prevent="saveAnalisaRekomendasi"
                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    <x-heroicon-o-check class="w-5 h-5 mr-2"/>
                    Simpan Analisis dan Rekomendasi
                </button>
            </div>
        </div>
    </div>
    
    {{-- Modal Preview --}}
    @if($showPreviewModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-heroicon-o-eye class="h-6 w-6 text-blue-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Preview Data yang Akan Disimpan
                            </h3>
                            <div class="mt-4 space-y-3">
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <h4 class="font-medium text-gray-700 mb-2">Informasi Filter:</h4>
                                    <p class="text-sm text-gray-600">Tanggal Mulai: {{ $tanggal_mulai }}</p>
                                    <p class="text-sm text-gray-600">Tanggal Selesai: {{ $tanggal_selesai }}</p>
                                    <p class="text-sm text-gray-600">Ruangan: {{ $ruangan ? \App\Models\Bangsal::where('kd_bangsal', $ruangan)->first()?->nm_bangsal : 'Semua Ruangan' }}</p>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-md">
                                    <h4 class="font-medium text-gray-700 mb-2">Analisis:</h4>
                                    <p class="text-sm text-gray-600">{{ Str::limit($analisa, 100) }}</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-md">
                                    <h4 class="font-medium text-gray-700 mb-2">Rekomendasi:</h4>
                                    <p class="text-sm text-gray-600">{{ Str::limit($rekomendasi, 100) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        wire:click="confirmSave"
                        type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Konfirmasi Simpan
                    </button>
                    <button 
                        wire:click="cancelPreview"
                        type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

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