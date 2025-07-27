<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Grafik Garis Pemasangan Alat</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
    <div id="chart" style="width: 1200px; height: 600px;"></div>

    <script>
        const chartData = @json($data);
        const analisaId = @json($analisaId ?? null);

        const options = {
            series: chartData.series,
            chart: {
                height: 600,
                type: 'line',
                zoom: {
                    enabled: false
                },
                animations: {
                    enabled: false
                }
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

        const chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render().then(() => {
            // Tunggu sebentar untuk memastikan chart sudah ter-render sempurna
            setTimeout(() => {
                saveChartAsImage();
            }, 1000);
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