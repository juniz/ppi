<?php

namespace App\Filament\Resources\DataHaisResource\Widgets;

use App\Models\DataHais;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HaisHarianChart extends ApexChartWidget
{
    protected static ?string $chartId = 'haisHarianChart';
    protected static ?string $heading = 'Grafik HAIs Harian';
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'semua';

    protected function getFilters(): ?array
    {
        return [
            'semua' => 'Semua',
            'pemasangan' => 'Hari Pemasangan',
            'infeksi' => 'Infeksi',
        ];
    }

    protected function getOptions(): array
    {
        $bulanIni = Carbon::now()->format('Y-m');
        
        $data = DataHais::query()
            ->selectRaw('DATE_FORMAT(tanggal, "%d") as hari')
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
            ->whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$bulanIni])
            ->groupBy('hari')
            ->orderBy('tanggal', 'ASC')
            ->get();

        // Data Hari Pemasangan
        $pemasanganSeries = [
            [
                'name' => 'ETT',
                'data' => $data->pluck('total_ett')->toArray(),
                'color' => '#0ea5e9',
                'dashArray' => 5,
            ],
            [
                'name' => 'CVL',
                'data' => $data->pluck('total_cvl')->toArray(),
                'color' => '#2563eb',
                'dashArray' => 5,
            ],
            [
                'name' => 'IVL',
                'data' => $data->pluck('total_ivl')->toArray(),
                'color' => '#1d4ed8',
                'dashArray' => 5,
            ],
            [
                'name' => 'UC',
                'data' => $data->pluck('total_uc')->toArray(),
                'color' => '#3b82f6',
                'dashArray' => 5,
            ],
        ];

        // Data Infeksi
        $infeksiSeries = [
            [
                'name' => 'VAP',
                'data' => $data->pluck('total_vap')->toArray(),
                'color' => '#ef4444',
            ],
            [
                'name' => 'IAD',
                'data' => $data->pluck('total_iad')->toArray(),
                'color' => '#dc2626',
            ],
            [
                'name' => 'PLEB',
                'data' => $data->pluck('total_pleb')->toArray(),
                'color' => '#b91c1c',
            ],
            [
                'name' => 'ISK',
                'data' => $data->pluck('total_isk')->toArray(),
                'color' => '#991b1b',
            ],
            [
                'name' => 'ILO',
                'data' => $data->pluck('total_ilo')->toArray(),
                'color' => '#f87171',
            ],
            [
                'name' => 'HAP',
                'data' => $data->pluck('total_hap')->toArray(),
                'color' => '#fca5a5',
            ],
            [
                'name' => 'Tinea',
                'data' => $data->pluck('total_tinea')->toArray(),
                'color' => '#fee2e2',
            ],
            [
                'name' => 'Scabies',
                'data' => $data->pluck('total_scabies')->toArray(),
                'color' => '#fecaca',
            ],
        ];

        // Filter series berdasarkan pilihan
        $series = match($this->filter) {
            'pemasangan' => $pemasanganSeries,
            'infeksi' => $infeksiSeries,
            default => array_merge($pemasanganSeries, $infeksiSeries),
        };

        return [
            'chart' => [
                'type' => 'line',
                'height' => 500,
                'toolbar' => ['show' => true],
                'zoom' => ['enabled' => true],
                'animations' => [
                    'enabled' => true,
                    'speed' => 500,
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $data->pluck('hari')->toArray(),
                'title' => [
                    'text' => 'Tanggal',
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
                'width' => array_fill(0, count($series), $this->filter === 'pemasangan' ? 4 : 2),
            ],
            'legend' => [
                'show' => true,
                'position' => 'bottom',
                'horizontalAlign' => 'center',
                'floating' => false,
                'fontSize' => '12px',
                'fontFamily' => 'inherit',
                'itemMargin' => [
                    'horizontal' => 8,
                    'vertical' => 5,
                ],
                'markers' => [
                    'width' => 12,
                    'height' => 12,
                    'strokeWidth' => 0,
                    'strokeColor' => '#fff',
                    'radius' => 12,
                ],
            ],
            'grid' => [
                'borderColor' => '#e5e7eb',
                'strokeDashArray' => 4,
                'padding' => [
                    'left' => 20,
                    'right' => 20,
                    'bottom' => 15,
                ],
            ],
            'responsive' => [
                [
                    'breakpoint' => 1024,
                    'options' => [
                        'chart' => [
                            'height' => 580,
                        ],
                        'legend' => [
                            'fontSize' => '11px',
                            'itemMargin' => [
                                'horizontal' => 6,
                                'vertical' => 4,
                            ],
                        ],
                    ],
                ],
                [
                    'breakpoint' => 768,
                    'options' => [
                        'chart' => [
                            'height' => 640,
                        ],
                        'legend' => [
                            'fontSize' => '10px',
                            'itemMargin' => [
                                'horizontal' => 4,
                                'vertical' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HaisHarianChart::class,
        ];
    }
} 