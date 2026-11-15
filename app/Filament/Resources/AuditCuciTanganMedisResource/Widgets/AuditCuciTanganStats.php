<?php

namespace App\Filament\Resources\AuditCuciTanganMedisResource\Widgets;

use App\Models\AuditCuciTanganMedis;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AuditCuciTanganStats extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = AuditCuciTanganMedis::query()
            ->selectRaw('
                COUNT(*) as total,
                ROUND(AVG(((sebelum_menyentuh_pasien = "Ya") + (sebelum_tehnik_aseptik = "Ya") + (setelah_terpapar_cairan_tubuh_pasien = "Ya") + (setelah_kontak_dengan_pasien = "Ya") + (setelah_kontak_dengan_lingkungan_pasien = "Ya")) / 5 * 100), 1) as avg_compliance,
                SUM(CASE WHEN ((sebelum_menyentuh_pasien = "Ya") + (sebelum_tehnik_aseptik = "Ya") + (setelah_terpapar_cairan_tubuh_pasien = "Ya") + (setelah_kontak_dengan_pasien = "Ya") + (setelah_kontak_dengan_lingkungan_pasien = "Ya")) = 5 THEN 1 ELSE 0 END) as total_100,
                SUM(CASE WHEN ((sebelum_menyentuh_pasien = "Ya") + (sebelum_tehnik_aseptik = "Ya") + (setelah_terpapar_cairan_tubuh_pasien = "Ya") + (setelah_kontak_dengan_pasien = "Ya") + (setelah_kontak_dengan_lingkungan_pasien = "Ya")) / 5 * 100 < 85 THEN 1 ELSE 0 END) as need_improvement
            ')
            ->first();

        $total = (int) ($stats->total ?? 0);
        $avg = (float) ($stats->avg_compliance ?? 0);
        $total100 = (int) ($stats->total_100 ?? 0);
        $needImp = (int) ($stats->need_improvement ?? 0);

        $avgColor = $avg >= 85 ? 'success' : ($avg >= 75 ? 'warning' : 'danger');

        return [
            Stat::make('Total Audit', number_format($total) . ' Sesi')
                ->description('Total observasi kepatuhan')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info')
                ->chart([$total, $total, $total]),

            Stat::make('Rata-rata Kepatuhan', "{$avg}%")
                ->description($avg >= 85 ? 'Memenuhi Standar Mutu (>=85%)' : 'Di Bawah Standar Mutu (<85%)')
                ->descriptionIcon($avg >= 85 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($avgColor)
                ->chart([70, 75, 80, $avg]),

            Stat::make('Patuh Sempurna (100%)', number_format($total100) . ' Petugas')
                ->description('Patuh seluruh 5 momen WHO')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Perlu Evaluasi (<85%)', number_format($needImp) . ' Petugas')
                ->description('Di bawah target akreditasi')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($needImp > 0 ? 'danger' : 'gray'),
        ];
    }
}
