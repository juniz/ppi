<?php

namespace App\Filament\Resources\AuditFasilitasApdResource\Pages;

use App\Filament\Resources\AuditFasilitasApdResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAuditFasilitasApds extends ManageRecords
{
    protected static string $resource = AuditFasilitasApdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
