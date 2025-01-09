<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }
} 