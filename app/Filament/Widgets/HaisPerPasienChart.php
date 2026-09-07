<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\DataHais;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class HaisPerPasienChart extends ApexChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $chartId = 'haisPerPasienChart';
    protected static ?string $heading = 'Grafik Infeksi HAIs Per Pasien';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

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
        $activeFilter = $this->filter;

        $query = DataHais::query();

        if ($activeFilter === 'today') {
            $query->where('tanggal', Carbon::today()->toDateString());
        } elseif ($activeFilter === 'week') {
            $query->whereBetween('tanggal', [Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()]);
        } elseif ($activeFilter === 'month') {
            $query->whereBetween('tanggal', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()]);
        } elseif ($activeFilter === 'year') {
            $query->whereBetween('tanggal', [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->endOfYear()->toDateString()]);
        } else {
            // Default to today
            $query->where('tanggal', Carbon::today()->toDateString());
        }

        $data = $query->selectRaw('
            SUM(VAP) as vap,
            SUM(IAD) as iad,
            SUM(PLEB) as pleb,
            SUM(ISK) as cauti,
            SUM(ILO) as ilo,
            SUM(HAP) as hap
        ')->first();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Kasus',
                    'data' => [
                        (int) ($data->vap ?? 0),
                        (int) ($data->iad ?? 0),
                        (int) ($data->pleb ?? 0),
                        (int) ($data->cauti ?? 0),
                        (int) ($data->ilo ?? 0),
                        (int) ($data->hap ?? 0),
                    ],
                ],
            ],
            'xaxis' => [
                'categories' => ['VAP', 'IAD', 'PLEB', 'CAUTI', 'ILO', 'HAP'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal' => false,
                    'distributed' => true,
                ],
            ],
            'legend' => [
                'show' => false,
            ],
        ];
    }
}