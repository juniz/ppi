<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Grafik Garis Infeksi HAIs</title>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .chart-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            /* keep a good aspect ratio on mobile */
            aspect-ratio: 16 / 9;
        }
        .chart-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="chart-title">Grafik Garis Infeksi HAIs</div>
    <div id="chart" class="chart-container"></div>
    
    <script>
        const chartData = @json($data);
        const analisaId = @json($analisaId ?? null);

        var options = {
            chart: {
                type: 'line',
                background: '#ffffff',
                toolbar: {
                    show: false
                }
            },
            series: [
                {
                    name: 'VAP',
                    data: @json($data['series']['VAP'])
                },
                {
                    name: 'IAD',
                    data: @json($data['series']['IAD'])
                },
                {
                    name: 'PLEB',
                    data: @json($data['series']['PLEB'])
                },
                {
                    name: 'CAUTI',
                    data: @json($data['series']['ISK'])
                },
                {
                    name: 'ILO',
                    data: @json($data['series']['ILO'])
                },
                {
                    name: 'HAP',
                    data: @json($data['series']['HAP'])
                }
            ],
            xaxis: {
                categories: @json($data['categories'])
            },
            colors: @json($data['colors']),
            stroke: {
                curve: 'smooth',
                width: 2
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                shared: true,
                intersect: false
            },
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '12px',
                offsetY: 0,
                itemMargin: {
                    horizontal: 10,
                    vertical: 30
                }
            },
            grid: {
                borderColor: '#e5e7eb'
            }
        };

        var chartEl = document.querySelector("#chart");

        // compute an initial height based on container width
        function computeHeight() {
            var w = chartEl.clientWidth || 600;
            return Math.max(280, Math.round(w * 0.5));
        }

        options.chart.height = computeHeight();

        var chart = new ApexCharts(chartEl, options);
        chart.render();

        // resize handler to keep chart responsive
        var resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                chart.updateOptions({ chart: { height: computeHeight() } }, false, true);
            }, 150);
        });

        // Function to convert chart to image and save
        function saveChartAsImage() {
            if (!analisaId) {
                console.log('No analisa ID provided, skipping save');
                return;
            }

            chart.dataURI().then((uri) => {
                // Send to server to save
                fetch('/chart/save-image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        image: uri.imgURI,
                        chart_type: 'infeksi',
                        analisa_id: analisaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Chart saved successfully:', data.path);
                        if (window.parent !== window) {
                            window.parent.postMessage({
                                type: 'chart-saved',
                                chartType: 'infeksi',
                                path: data.path,
                                url: data.url
                            }, '*');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error saving chart:', error);
                });
            });
        }

        // Also save after a short delay to ensure chart is rendered
        setTimeout(saveChartAsImage, 1500);
    </script>
</body>
</html>