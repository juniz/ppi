<?php

namespace App\Filament\Widgets;

use App\Models\KamarInap;
use App\Models\RegPeriksa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::now()->format('Y-m-d');
        
        // Query untuk Pasien Rawat Inap yang masih dirawat (stts_pulang = '-')
        $rawatInap = KamarInap::where('stts_pulang', '-')
            ->count();

        // Query untuk Pasien Pulang hari ini
        $pasienPulang = KamarInap::whereDate('tgl_keluar', $today)
            ->where('stts_pulang', '!=', '-')
            ->count();

        // Query untuk Pasien Masuk hari ini
        $pasienMasuk = KamarInap::whereDate('tgl_masuk', $today)
            ->count();

        return [
            Stat::make('Pasien Rawat Inap', $rawatInap)
                ->description('Total pasien dirawat saat ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),
            Stat::make('Pasien Pulang', $pasienPulang)
                ->description('Pasien pulang hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
            Stat::make('Pasien Masuk', $pasienMasuk)
                ->description('Pasien masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') 
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 'full',
        'lg' => 6,
        'xl' => 6,
        '2xl' => 6,
    ];

    public static function refresh(): string 
    {
        return '10s';
    }
}
