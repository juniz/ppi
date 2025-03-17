<?php

namespace App\Filament\Resources\DataHaisResource\Widgets;

use App\Models\DataHais;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Carbon;

class HaisHarianInfeksiChart extends ApexChartWidget
{
    protected static ?string $heading = 'Grafik Infeksi HAIs';
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'today';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
        ];
    }

    protected function getOptions(): array
    {
        $dateRange = match ($this->filter) {
            'today' => Carbon::today()->format('d M Y'),
            'week' => Carbon::now()->startOfWeek()->format('d M Y') . ' - ' . Carbon::now()->endOfWeek()->format('d M Y'),
            'month' => Carbon::now()->startOfMonth()->format('d M Y') . ' - ' . Carbon::now()->endOfMonth()->format('d M Y'),
            'year' => Carbon::now()->startOfYear()->format('d M Y') . ' - ' . Carbon::now()->endOfYear()->format('d M Y'),
        };

        $data = DataHais::query()
            ->when($this->filter === 'today', fn($q) => $q->whereDate('tanggal', Carbon::today()))
            ->when($this->filter === 'week', fn($q) => $q->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]))
            ->when($this->filter === 'month', fn($q) => $q->whereMonth('tanggal', Carbon::now()->month))
            ->when($this->filter === 'year', fn($q) => $q->whereYear('tanggal', Carbon::now()->year))
            ->selectRaw('
                SUM(VAP) as vap,
                SUM(IAD) as iad,
                SUM(PLEB) as pleb,
                SUM(ISK) as isk,
                SUM(ILO) as ilo,
                SUM(HAP) as hap
            ')
            ->first();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => true,
                ],
                'zoom' => [
                    'enabled' => true,
                ],
            ],
            'title' => [
                'text' => 'Grafik Infeksi HAIs',
                'align' => 'center',
            ],
            'subtitle' => [
                'text' => "Periode: $dateRange",
                'align' => 'center',
                'style' => [
                    'fontSize' => '12px',
                    'color' => '#666666'
                ]
            ],
            'series' => [
                [
                    'name' => 'Jumlah Kasus',
                    'data' => [
                        $data->vap ?? 0,
                        $data->iad ?? 0,
                        $data->pleb ?? 0,
                        $data->isk ?? 0,
                        $data->ilo ?? 0,
                        $data->hap ?? 0,
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                    'endingShape' => 'rounded',
                    'borderRadius' => 4,
                    'dataLabels' => [
                        'position' => 'top',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'offsetY' => -20,
                'style' => [
                    'fontSize' => '12px',
                    'colors' => ['#304758']
                ],
            ],
            'stroke' => [
                'show' => true,
                'width' => 2,
                'colors' => ['transparent']
            ],
            'xaxis' => [
                'categories' => ['VAP', 'IAD', 'PLEB', 'ISK', 'ILO', 'HAP'],
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Kasus',
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'fill' => [
                'opacity' => 1,
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'light',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.3,
                    'opacityFrom' => 0.9,
                    'opacityTo' => 0.9,
                ],
            ],
            'colors' => ['#1A56DB'],
            'grid' => [
                'borderColor' => '#f1f1f1',
                'row' => [
                    'colors' => ['#f3f4f6', 'transparent'],
                    'opacity' => 0.5
                ],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) {
                        return val + " kasus"
                    }'
                ]
            ],
        ];
    }
} 