<?php

namespace App\Filament\Resources\BangsalResource\Pages;

use App\Filament\Resources\BangsalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBangsals extends ManageRecords
{
    protected static string $resource = BangsalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
