<x-filament-panels::page>
    <div>
        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-4">Grafik Infeksi HAIs Per Pasien</h2>
            
            <div class="flex justify-end mb-4">
                <select class="rounded-lg border-gray-300">
                    <option>Hari Ini</option>
                    <option>Minggu Ini</option>
                    <option>Bulan Ini</option>
                    <option>Tahun Ini</option>
                </select>
            </div>

            <div class="mt-4">
                <!-- Grafik akan ditampilkan disini -->
                <div id="chart"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Konfigurasi grafik ApexCharts
        var options = {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Jumlah Kasus',
                data: [1, 0, 0, 0, 0, 0]
            }],
            xaxis: {
                categories: ['VAP', 'IAD', 'PLEB', 'ISK', 'ILO', 'HAP']
            },
            yaxis: {
                title: {
                    text: 'Jumlah Kasus'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
    @endpush
</x-filament-panels::page> 