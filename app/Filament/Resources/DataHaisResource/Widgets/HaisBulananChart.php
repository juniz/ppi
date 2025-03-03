<?php

namespace App\Filament\Resources\DataHaisResource\Widgets;

use App\Models\DataHais;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;

class HaisBulananChart extends ApexChartWidget
{
    protected static ?string $chartId = 'haisBulananChart';
    protected static ?string $heading = 'Grafik HAIs Bulanan';
    
    // Mengatur lebar chart agar full width
    protected int | string | array $columnSpan = 'full';

    protected function getOptions(): array
    {
        $data = DataHais::query()
            ->selectRaw('DATE_FORMAT(tanggal, "%M %Y") as bulan')
            ->selectRaw('SUM(ETT) as total_ett')
            ->selectRaw('SUM(CVL) as total_cvl')
            ->selectRaw('SUM(IVL) as total_ivl')
            ->selectRaw('SUM(UC) as total_uc')
            ->selectRaw('SUM(VAP) as total_vap')
            ->selectRaw('SUM(IAD) as total_iad')
            ->selectRaw('SUM(PLEB) as total_pleb')
            ->selectRaw('SUM(ISK) as total_isk')
            ->selectRaw('SUM(ILO) as total_ilo')
            ->selectRaw('SUM(HAP) as total_hap')
            ->selectRaw('SUM(Tinea) as total_tinea')
            ->selectRaw('SUM(Scabies) as total_scabies')
            ->selectRaw('SUM(CASE WHEN DEKU = "IYA" THEN 1 ELSE 0 END) as total_deku')
            ->groupBy('bulan')
            ->orderBy('tanggal', 'ASC')
            ->limit(12)
            ->get();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
                'toolbar' => [
                    'show' => true,
                ],
                'zoom' => [
                    'enabled' => true,
                ],
                'animations' => [
                    'enabled' => true,
                    'speed' => 500,
                ],
            ],
            'series' => [
                [
                    'name' => 'ETT',
                    'data' => $data->pluck('total_ett')->toArray(),
                    'color' => '#2563eb',
                    'dashArray' => 5,
                ],
                [
                    'name' => 'CVL',
                    'data' => $data->pluck('total_cvl')->toArray(),
                    'color' => '#dc2626',
                    'dashArray' => 5,
                ],
                [
                    'name' => 'IVL',
                    'data' => $data->pluck('total_ivl')->toArray(),
                    'color' => '#16a34a',
                    'dashArray' => 5,
                ],
                [
                    'name' => 'UC',
                    'data' => $data->pluck('total_uc')->toArray(),
                    'color' => '#9333ea',
                    'dashArray' => 5,
                ],
                [
                    'name' => 'VAP',
                    'data' => $data->pluck('total_vap')->toArray(),
                    'color' => '#ea580c',
                ],
                [
                    'name' => 'IAD',
                    'data' => $data->pluck('total_iad')->toArray(),
                    'color' => '#4f46e5',
                ],
                [
                    'name' => 'PLEB',
                    'data' => $data->pluck('total_pleb')->toArray(),
                    'color' => '#0891b2',
                ],
                [
                    'name' => 'ISK',
                    'data' => $data->pluck('total_isk')->toArray(),
                    'color' => '#be123c',
                ],
                [
                    'name' => 'ILO',
                    'data' => $data->pluck('total_ilo')->toArray(),
                    'color' => '#854d0e',
                ],
                [
                    'name' => 'HAP',
                    'data' => $data->pluck('total_hap')->toArray(),
                    'color' => '#166534',
                ],
                [
                    'name' => 'Tinea',
                    'data' => $data->pluck('total_tinea')->toArray(),
                    'color' => '#7e22ce',
                ],
                [
                    'name' => 'Scabies',
                    'data' => $data->pluck('total_scabies')->toArray(),
                    'color' => '#0f766e',
                ],
                [
                    'name' => 'DEKU',
                    'data' => $data->pluck('total_deku')->toArray(),
                    'color' => '#475569',
                ],
            ],
            'xaxis' => [
                'categories' => $data->pluck('bulan')->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'cssClass' => 'text-sm font-medium',
                    ],
                    'rotate' => -45,
                    'trim' => true,
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Kasus',
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 600,
                    ],
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'cssClass' => 'text-sm font-medium',
                    ],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'center',
                'floating' => false,
                'fontSize' => '13px',
                'fontFamily' => 'inherit',
                'offsetY' => -5,
                'itemMargin' => [
                    'horizontal' => 8,
                    'vertical' => 8,
                ],
            ],
            'grid' => [
                'borderColor' => '#e5e7eb',
                'strokeDashArray' => 4,
                'padding' => [
                    'left' => 10,
                    'right' => 10,
                ],
            ],
            'responsive' => [
                [
                    'breakpoint' => 1024,
                    'options' => [
                        'chart' => [
                            'height' => 350,
                        ],
                    ],
                ],
                [
                    'breakpoint' => 768,
                    'options' => [
                        'chart' => [
                            'height' => 300,
                        ],
                    ],
                ],
            ],
        ];
    }
} 