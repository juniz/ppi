<?php

namespace App\Filament\Resources\AnalisaRekomendasiResource\Pages;

use App\Filament\Resources\AnalisaRekomendasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnalisaRekomendasis extends ListRecords
{
    protected static string $resource = AnalisaRekomendasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tidak ada create action karena data dibuat melalui halaman analisa laju
        ];
    }
}