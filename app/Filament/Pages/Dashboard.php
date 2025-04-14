<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianAlatChart;
use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianInfeksiChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;
    
    protected static string $view = 'filament.pages.dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            HaisHarianInfeksiChart::class,
            HaisHarianAlatChart::class,
        ];
    }
} 