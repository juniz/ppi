<?php

namespace App\Filament\Resources\AuditBundleIadpResource\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\AuditBundleIadp;
use Filament\Forms\Components\Select;

class IadpChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'iadpChart';

    public ?string $tahun = '';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Audit Bundle IADP Chart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $tahun = $this->filter ?? date('Y');
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 400,
                'width' => '100%',
            ],
            'series' => [
                [
                    'name' => $tahun - 1,
                    'data' => $this->getData($tahun - 1),
                    'color' => 'blue',
                ],
                [
                    'name' => $tahun,
                    'data' => $this->getData($tahun),
                    'color' => 'red',
                ],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Rata-rata Nilai (%)',
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                    'horizontal' => false,
                ],
            ],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            date('Y') => date('Y'),
            date('Y') - 1 => date('Y') - 1,
            date('Y') - 2 => date('Y') - 2,
            date('Y') - 3 => date('Y') - 3,
            date('Y') - 4 => date('Y') - 4,
        ];
    }

    protected function getData(string $year): array
    {
        $data = [];
        foreach (range(1, 12) as $month) {
            $data[] = AuditBundleIadp::rataTtlNilai($month, $year);
        }
        return $data;
    }
}
