<?php

namespace App\Filament\Resources\AuditKepatuhanApdResource\Pages;

use App\Filament\Resources\AuditKepatuhanApdResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditKepatuhanApds extends ManageRecords
{
    protected static string $resource = AuditKepatuhanApdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
