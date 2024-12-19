<?php

namespace App\Filament\Resources\AuditBundleIskResource\Pages;

use App\Filament\Resources\AuditBundleIskResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundleIsks extends ManageRecords
{
    protected static string $resource = AuditBundleIskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
