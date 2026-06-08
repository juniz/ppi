<?php

namespace App\Filament\Resources\AuditPengendalianLingkunganResource\Pages;

use App\Filament\Resources\AuditPengendalianLingkunganResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditPengendalianLingkungans extends ManageRecords
{
    protected static string $resource = AuditPengendalianLingkunganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
