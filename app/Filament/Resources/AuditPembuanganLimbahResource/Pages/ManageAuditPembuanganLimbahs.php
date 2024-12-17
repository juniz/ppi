<?php

namespace App\Filament\Resources\AuditPembuanganLimbahResource\Pages;

use App\Filament\Resources\AuditPembuanganLimbahResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditPembuanganLimbahs extends ManageRecords
{
    protected static string $resource = AuditPembuanganLimbahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
