<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Facades\FilamentView;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class HaisPerPasien extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $title = 'HAIs Per Pasien';
    protected static ?string $slug = 'hais-per-pasien';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.hais-per-pasien';

    public function mount(): void
    {
        FilamentView::registerRenderHook(
            'charts.scripts',
            fn(): string => "
                <script src='https://cdn.jsdelivr.net/npm/apexcharts'></script>
            ",
        );
    }
} 