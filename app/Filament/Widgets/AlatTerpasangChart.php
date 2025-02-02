<?php

namespace App\Filament\Widgets;

use App\Models\DataHais;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AlatTerpasangChart extends ChartWidget
{
    protected static ?string $heading = 'Alat Terpasang Hari Ini';
    
    protected static ?int $sort = 3;
    
    // Mengubah ukuran widget agar sama dengan chart di atasnya
    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 'full',
        'lg' => 6,    // Setengah layar pada ukuran large
        'xl' => 6,    // Setengah layar pada ukuran extra large
        '2xl' => 6,   // Setengah layar pada ukuran 2x extra large
    ];

    // Mengatur tinggi chart
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $data = DataHais::query()
            ->join('kamar', 'data_HAIs.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereDate('tanggal', $today)
            ->select(
                'bangsal.nm_bangsal',
                DB::raw('SUM(ETT) as total_ett'),
                DB::raw('SUM(CVL) as total_cvl'),
                DB::raw('SUM(IVL) as total_ivl'),
                DB::raw('SUM(UC) as total_uc')
            )
            ->groupBy('bangsal.nm_bangsal')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'ETT',
                    'data' => $data->pluck('total_ett')->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
                [
                    'label' => 'CVL',
                    'data' => $data->pluck('total_cvl')->toArray(),
                    'backgroundColor' => '#FF6384',
                ],
                [
                    'label' => 'IVL',
                    'data' => $data->pluck('total_ivl')->toArray(),
                    'backgroundColor' => '#4BC0C0',
                ],
                [
                    'label' => 'UC',
                    'data' => $data->pluck('total_uc')->toArray(),
                    'backgroundColor' => '#FF9F40',
                ],
            ],
            'labels' => $data->pluck('nm_bangsal')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'stacked' => true,
                ],
                'x' => [
                    'stacked' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Data ' . Carbon::now()->format('d M Y'),
                ],
            ],
        ];
    }
} 