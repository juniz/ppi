<?php

namespace App\Filament\Resources\AuditCuciTanganMedisResource\Pages;

use App\Filament\Resources\AuditCuciTanganMedisResource;
use App\Filament\Resources\AuditCuciTanganMedisResource\Widgets\AuditCuciTanganStats;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\MaxWidth;

class ManageAuditCuciTanganMedis extends ManageRecords
{
    protected static string $resource = AuditCuciTanganMedisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Catat Audit Baru')
                ->icon('heroicon-m-plus')
                ->modalHeading('Audit Cuci Tangan Medis')
                ->modalWidth(MaxWidth::ExtraLarge),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AuditCuciTanganStats::class,
        ];
    }
}
