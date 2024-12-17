<?php

namespace App\Filament\Resources\AuditCuciTanganMedisResource\Pages;

use App\Filament\Resources\AuditCuciTanganMedisResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditCuciTanganMedis extends ManageRecords
{
    protected static string $resource = AuditCuciTanganMedisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
