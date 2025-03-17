<?php

namespace App\Filament\Widgets;

use App\Models\DataHais;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class HaisHarianInfeksiChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Infeksi';
    protected static ?int $sort = 1;
    
    protected function getData(): array
    {
        $data = DataHais::selectRaw('
            DATE(tanggal) as date,
            SUM(VAP) as vap,
            SUM(IAD) as iad,
            SUM(PLEB) as pleb,
            SUM(ISK) as isk,
            SUM(ILO) as ilo,
            SUM(HAP) as hap
        ')
        ->whereDate('tanggal', Carbon::today())
        ->groupBy('date')
        ->first();

        return [
            'datasets' => [
                [
                    'label' => 'Infeksi',
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
            'labels' => ['VAP', 'IAD', 'PLEB', 'ISK', 'ILO', 'HAP'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
} 