<?php

namespace App\Filament\Resources\AuditFasilitasCuciTanganResource\Pages;

use App\Filament\Resources\AuditFasilitasCuciTanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditFasilitasCuciTangans extends ManageRecords
{
    protected static string $resource = AuditFasilitasCuciTanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
