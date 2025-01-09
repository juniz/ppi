<?php

namespace App\Filament\Resources\AuditBundleIadpResource\Pages;

use App\Filament\Resources\AuditBundleIadpResource;
use App\Filament\Resources\AuditBundleIadpResource\Widgets\IadpChart;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundleIadps extends ManageRecords
{
    protected static string $resource = AuditBundleIadpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IadpChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }
}
