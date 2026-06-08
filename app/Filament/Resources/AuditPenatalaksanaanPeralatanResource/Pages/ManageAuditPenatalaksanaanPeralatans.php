<?php

namespace App\Filament\Resources\AuditPenatalaksanaanPeralatanResource\Pages;

use App\Filament\Resources\AuditPenatalaksanaanPeralatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditPenatalaksanaanPeralatans extends ManageRecords
{
    protected static string $resource = AuditPenatalaksanaanPeralatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
