<?php

namespace App\Filament\Resources\AuditPembuanganBendaTajamResource\Pages;

use App\Filament\Resources\AuditPembuanganBendaTajamResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditPembuanganBendaTajams extends ManageRecords
{
    protected static string $resource = AuditPembuanganBendaTajamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
