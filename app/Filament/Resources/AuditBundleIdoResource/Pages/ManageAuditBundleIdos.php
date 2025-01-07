<?php

namespace App\Filament\Resources\AuditBundleIdoResource\Pages;

use App\Filament\Resources\AuditBundleIdoResource;
use App\Filament\Resources\AuditBundleIdoResource\Widgets\IdoChart;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundleIdos extends ManageRecords
{
    protected static string $resource = AuditBundleIdoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IdoChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }
}
