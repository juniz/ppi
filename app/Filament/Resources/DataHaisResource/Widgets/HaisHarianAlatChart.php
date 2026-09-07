<?php

namespace App\Filament\Resources\DataHaisResource\Widgets;

use App\Models\DataHais;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Carbon;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Cache;

class HaisHarianAlatChart extends ApexChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Grafik Pemasangan Alat';
    protected int | string | array $columnSpan = ['md' => 1, 'xl' => 1];

    public function updatedFilters(): void
    {
        $this->updateOptions();
    }

    protected function getOptions(): array
    {
        $dari = $this->filters['dari_tanggal'] ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai = $this->filters['sampai_tanggal'] ?? Carbon::now()->toDateString();
        $bangsal = $this->filters['kd_bangsal'] ?? null;
        $search = $this->filters['search'] ?? null;
        
        $dateRange = Carbon::parse($dari)->format('d M Y') . ' - ' . Carbon::parse($sampai)->format('d M Y');

        $cacheKey = 'hais_alat_chart_' . md5(json_encode([$dari, $sampai, $bangsal, $search]));
        $data = Cache::remember($cacheKey, 60, function () use ($dari, $sampai, $bangsal, $search) {
            return DataHais::query()
                ->when($dari, fn($q) => $q->where('data_HAIs.tanggal', '>=', $dari))
                ->when($sampai, fn($q) => $q->where('data_HAIs.tanggal', '<=', $sampai))
                ->when($bangsal, fn($q) => $q->whereHas('kamar', fn($k) => $k->where('kd_bangsal', $bangsal)))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->whereHas('regPeriksa.pasien', fn($p) => $p->where('nm_pasien', 'like', "%{$search}%"))
                              ->orWhereHas('regPeriksa', fn($r) => $r->where('no_rkm_medis', 'like', "%{$search}%"))
                              ->orWhere('data_HAIs.no_rawat', 'like', "%{$search}%")
                              ->orWhereHas('kamar', fn($k) => $k->where('kd_kamar', 'like', "%{$search}%"));
                    });
                })
                ->selectRaw('
                    SUM(ETT) as ett,
                    SUM(CVL) as cvl,
                    SUM(IVL) as ivl,
                    SUM(UC) as uc
                ')
                ->first();
        });

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
                'text' => 'Grafik Pemasangan Alat',
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
                    'name' => 'Jumlah Pemasangan',
                    'data' => [
                        $data->ett ?? 0,
                        $data->cvl ?? 0,
                        $data->ivl ?? 0,
                        $data->uc ?? 0,
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
                'categories' => ['ETT', 'CVL', 'IVL', 'UC'],
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Pemasangan',
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
            'colors' => ['#047857'],
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
                        return val + " pemasangan"
                    }'
                ]
            ],
        ];
    }
} 