<?php

namespace App\Filament\Resources\DokterResource\Pages;

use App\Filament\Resources\DokterResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDokter extends CreateRecord
{
    protected static string $resource = DokterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Dokter berhasil ditambahkan')
            ->body('Data dokter baru telah berhasil disimpan.');
    }
}