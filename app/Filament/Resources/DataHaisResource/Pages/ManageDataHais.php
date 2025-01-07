<?php

namespace App\Filament\Resources\DataHaisResource\Pages;

use App\Filament\Resources\DataHaisResource;
use App\Filament\Resources\DataHaisResource\Widgets\HaisChart;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDataHais extends ManageRecords
{
    protected static string $resource = DataHaisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HaisChart::class,
        ];
    }
}
