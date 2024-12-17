<?php

namespace App\Filament\Resources\AuditPenangananDarahResource\Pages;

use App\Filament\Resources\AuditPenangananDarahResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditPenangananDarahs extends ManageRecords
{
    protected static string $resource = AuditPenangananDarahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
