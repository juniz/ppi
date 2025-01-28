<?php

namespace App\Filament\Widgets;

use App\Models\DataHais;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 'full',
        'lg' => 6,
        'xl' => 6,
        '2xl' => 6,
    ];

    protected function getStats(): array
    {
        $today = Carbon::now()->format('Y-m-d');
        
        return [
            Stat::make('Pasien Rawat Inap', '8')
                ->description('Total pasien dirawat saat ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pasien Pulang', '0')
                ->description('Pasien pulang hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),
            Stat::make('Pasien Masuk', '0')
                ->description('Pasien masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }

    public static function refresh(): string 
    {
        return '10s';
    }
}
