<?php

namespace App\Filament\Resources\AuditBundleIadpResource\Pages;

use App\Filament\Resources\AuditBundleIadpResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundleIadps extends ManageRecords
{
    protected static string $resource = AuditBundleIadpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
