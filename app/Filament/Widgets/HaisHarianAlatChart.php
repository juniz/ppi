<?php

namespace App\Filament\Widgets;

use App\Models\DataHais;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class HaisHarianAlatChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pemasangan Alat';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = DataHais::selectRaw('
            DATE(tanggal) as date,
            SUM(ETT) as ett,
            SUM(CVL) as cvl,
            SUM(IVL) as ivl,
            SUM(UC) as uc
        ')
        ->whereDate('tanggal', Carbon::today())
        ->groupBy('date')
        ->first();

        return [
            'datasets' => [
                [
                    'label' => 'Pemasangan Alat',
                    'data' => [
                        $data->ett ?? 0,
                        $data->cvl ?? 0,
                        $data->ivl ?? 0,
                        $data->uc ?? 0,
                    ],
                ],
            ],
            'labels' => ['ETT', 'CVL', 'IVL', 'UC'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
} 