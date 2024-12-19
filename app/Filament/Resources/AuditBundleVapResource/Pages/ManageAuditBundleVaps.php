<?php

namespace App\Filament\Resources\AuditBundleVapResource\Pages;

use App\Filament\Resources\AuditBundleVapResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditBundleVaps extends ManageRecords
{
    protected static string $resource = AuditBundleVapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
