<?php

namespace App\Filament\Resources\AuditBundleIskResource\Pages;

use App\Filament\Resources\AuditBundleIskResource;
use App\Filament\Resources\AuditBundleIskResource\Widgets\IskChart;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundleIsks extends ManageRecords
{
    protected static string $resource = AuditBundleIskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IskChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }
}
