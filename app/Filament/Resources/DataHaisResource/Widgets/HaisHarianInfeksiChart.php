<?php

namespace App\Filament\Resources\DataHaisResource\Widgets;

use App\Models\DataHais;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Carbon;

use Filament\Widgets\Concerns\InteractsWithPageFilters;

class HaisHarianInfeksiChart extends ApexChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Grafik Infeksi HAIs';
    protected int | string | array $columnSpan = ['md' => 1, 'xl' => 1];
    protected function getOptions(): array
    {
        
        $dari = $this->filters['dari_tanggal'] ?? null;
        $sampai = $this->filters['sampai_tanggal'] ?? null;
        $bangsal = $this->filters['kd_bangsal'] ?? null;
        $search = $this->filters['search'] ?? null;
        
        $dateRange = '';
        if ($dari && $sampai) {
            $dateRange = Carbon::parse($dari)->format('d M Y') . ' - ' . Carbon::parse($sampai)->format('d M Y');
        } elseif ($dari) {
            $dateRange = 'Sejak ' . Carbon::parse($dari)->format('d M Y');
        } elseif ($sampai) {
            $dateRange = 'Hingga ' . Carbon::parse($sampai)->format('d M Y');
        } else {
            $dateRange = 'Semua Waktu';
        }

        $data = DataHais::query()
            ->when($dari, fn($q) => $q->whereDate('data_HAIs.tanggal', '>=', $dari))
            ->when($sampai, fn($q) => $q->whereDate('data_HAIs.tanggal', '<=', $sampai))
            ->when($bangsal, fn($q) => $q->whereHas('kamar', fn($k) => $k->where('kd_bangsal', $bangsal)))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->whereHas('regPeriksa.pasien', fn($p) => $p->where('nm_pasien', 'like', "%{$search}%"))
                          ->orWhere('data_HAIs.no_rawat', 'like', "%{$search}%")
                          ->orWhereHas('kamar', fn($k) => $k->where('kd_kamar', 'like', "%{$search}%"));
                });
            })
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