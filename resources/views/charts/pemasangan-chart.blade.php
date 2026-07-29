<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Grafik Garis Pemasangan Alat</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: white; }
        .chart-container { width: 100%; max-width: 1200px; margin: 0 auto; aspect-ratio: 16/9; }
    </style>
</head>
<body>
    <div id="chart" class="chart-container"></div>

    <script>
        const chartData = @json($data);
        const analisaId = @json($analisaId ?? null);

        // Ubah format object menjadi array of objects yang dibutuhkan ApexCharts
        const seriesData = Object.keys(chartData.series).map(key => {
            return {
                name: key,
                data: chartData.series[key]
            };
        });

        const options = {
            series: seriesData,
            chart: {
                type: 'line',
                zoom: { enabled: false },
                animations: { enabled: false }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight',
                width: 3
            },
            title: {
                text: 'Grafik Garis Pemasangan Alat',
                align: 'center',
                style: {
                    fontSize: '18px',
                    fontWeight: 'bold'
                }
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: chartData.categories,
                title: {
                    text: 'Periode'
                }
            },
            yaxis: {
                title: {
                    text: 'Hari Pemasangan'
                }
            },
            colors: ['#10b981', '#06b6d4', '#8b5cf6', '#f59e0b'],
            legend: {
                position: 'bottom'
            }
        };

        const chartEl = document.querySelector("#chart");

        function computeHeight() {
            const w = chartEl.clientWidth || 800;
            return Math.max(300, Math.round(w * 0.5));
        }

        options.chart.height = computeHeight();

        const chart = new ApexCharts(chartEl, options);
        chart.render().then(() => {
            setTimeout(() => saveChartAsImage(), 800);
        });

        // responsive resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                chart.updateOptions({ chart: { height: computeHeight() } }, false, true);
            }, 150);
        });

        function saveChartAsImage() {
            if (!analisaId) {
                console.log('No analisa ID provided, skipping save');
                return;
            }

            chart.dataURI().then(({ imgURI }) => {
                fetch('/chart/save-image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        image: imgURI,
                        chart_type: 'pemasangan',
                        analisa_id: analisaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Chart pemasangan berhasil disimpan:', data.path);
                        // Kirim pesan ke parent window jika ada
                        if (window.parent && window.parent !== window) {
                            window.parent.postMessage({
                                type: 'chart-saved',
                                chartType: 'pemasangan',
                                success: true,
                                path: data.path
                            }, '*');
                        }
                    } else {
                        console.error('Gagal menyimpan chart pemasangan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    </script>
</body>
</html>