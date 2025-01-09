<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\RegPeriksa;
use App\Models\KamarInap;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Hitung pasien rawat inap saat ini
        $rawatInap = RegPeriksa::select('reg_periksa.no_rawat')
            ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->where('kamar_inap.stts_pulang', '-')
            ->distinct()
            ->count('reg_periksa.no_rawat');

        // Hitung pasien pulang hari ini
        $pasienPulang = KamarInap::whereDate('tgl_keluar', Carbon::today())
            ->where('stts_pulang', '!=', '-')
            ->count();

        // Hitung pasien masuk hari ini  
        $pasienMasuk = KamarInap::whereDate('tgl_masuk', Carbon::today())
            ->count();

        return [
            Stat::make('Pasien Rawat Inap', $rawatInap)
                ->description('Total pasien dirawat saat ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Pasien Pulang', $pasienPulang)
                ->description('Pasien pulang hari ini') 
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->icon('heroicon-o-home')
                ->color('warning'),

            Stat::make('Pasien Masuk', $pasienMasuk)
                ->description('Pasien masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up') 
                ->icon('heroicon-o-user-plus')
                ->color('info'),
        ];
    }

    public static function refresh(): string 
    {
        return '10s';
    }
}
