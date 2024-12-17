<?php

namespace App\Filament\Resources\AuditPengelolaanLinenKotorResource\Pages;

use App\Filament\Resources\AuditPengelolaanLinenKotorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditPengelolaanLinenKotors extends ManageRecords
{
    protected static string $resource = AuditPengelolaanLinenKotorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
