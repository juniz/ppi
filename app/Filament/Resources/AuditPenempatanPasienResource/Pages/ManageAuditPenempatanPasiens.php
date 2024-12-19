<?php

namespace App\Filament\Resources\AuditPenempatanPasienResource\Pages;

use App\Filament\Resources\AuditPenempatanPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditPenempatanPasiens extends ManageRecords
{
    protected static string $resource = AuditPenempatanPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
