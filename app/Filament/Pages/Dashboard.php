<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BundleAuditChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use App\Models\DataHais;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            BundleAuditChart::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            // Widget lain bisa ditambahkan di sini jika diperlukan
        ];
    }
} 