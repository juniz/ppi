<?php

namespace App\Filament\Resources\AuditEtikaBatukResource\Pages;

use App\Filament\Resources\AuditEtikaBatukResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditEtikaBatuks extends ManageRecords
{
    protected static string $resource = AuditEtikaBatukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
