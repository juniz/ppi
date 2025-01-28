<?php

namespace App\Filament\Widgets;

use App\Models\DataHais;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BundleAuditChart extends ChartWidget
{
    protected static ?string $heading = 'Surveilans Infeksi Hari Ini';
    
    // Tambahkan ini untuk memindahkan posisi widget
    protected static ?int $sort = 2; // Angka yang lebih besar akan membuatnya muncul di bawah
    
    // Ubah columnSpan menjadi responsif
    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 'full',
        'lg' => 6,    // Setengah layar pada ukuran large
        'xl' => 6,    // Setengah layar pada ukuran extra large
        '2xl' => 6,   // Setengah layar pada ukuran 2x extra large
    ];

    // Mengatur tinggi responsif
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $data = DataHais::whereDate('tanggal', $today)
            ->select(
                DB::raw('SUM(IAD) as total_iad'),
                DB::raw('SUM(PLEB) as total_pleb'),
                DB::raw('SUM(ISK) as total_isk'),
                DB::raw('SUM(ILO) as total_ilo'),
                DB::raw('SUM(HAP) as total_hap')
            )
            ->first();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kasus',
                    'data' => [
                        $data->total_iad ?? 0,
                        $data->total_pleb ?? 0,
                        $data->total_isk ?? 0,
                        $data->total_ilo ?? 0,
                        $data->total_hap ?? 0,
                    ],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.8)',   // Biru
                        'rgba(255, 99, 132, 0.8)',   // Merah
                        'rgba(75, 192, 192, 0.8)',   // Tosca
                        'rgba(255, 206, 86, 0.8)',   // Kuning
                        'rgba(153, 102, 255, 0.8)',  // Ungu
                    ],
                    'borderColor' => [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 206, 86)',
                        'rgb(153, 102, 255)',
                    ],
                    'borderWidth' => 2,
                    'borderRadius' => 5,
                    'hoverBackgroundColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                ],
            ],
            'labels' => ['IAD', 'PLEB', 'ISK', 'ILO', 'HAP'],
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Data ' . Carbon::now()->format('d M Y'),
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'drawBorder' => true,
                        'color' => 'rgba(200, 200, 200, 0.3)',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
            ],
            'animation' => [
                'duration' => 2000,
            ],
        ];
    }
} 