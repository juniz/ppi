<?php

namespace App\Filament\Resources\AuditInjeksiAmanResource\Pages;

use App\Filament\Resources\AuditInjeksiAmanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditInjeksiAmans extends ManageRecords
{
    protected static string $resource = AuditInjeksiAmanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
