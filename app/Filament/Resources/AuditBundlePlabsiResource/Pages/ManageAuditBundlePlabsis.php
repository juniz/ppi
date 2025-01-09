<?php

namespace App\Filament\Resources\AuditBundlePlabsiResource\Pages;

use App\Filament\Resources\AuditBundlePlabsiResource;
use App\Filament\Resources\AuditBundlePlabsiResource\Widgets\PlabsiChart;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundlePlabsis extends ManageRecords
{
    protected static string $resource = AuditBundlePlabsiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PlabsiChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }
}
