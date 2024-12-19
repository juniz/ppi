<?php

namespace App\Filament\Resources\AuditBundleIdoResource\Pages;

use App\Filament\Resources\AuditBundleIdoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundleIdos extends ManageRecords
{
    protected static string $resource = AuditBundleIdoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
