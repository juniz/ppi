<?php

namespace App\Filament\Resources\RuangAuditKepatuhanResource\Pages;

use App\Filament\Resources\RuangAuditKepatuhanResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions;

class ManageRuangAuditKepatuhans extends ManageRecords
{
    protected static string $resource = RuangAuditKepatuhanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
