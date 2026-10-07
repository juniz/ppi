<?php

namespace App\Filament\Resources\BridgingMappingResource\Pages;

use App\Filament\Resources\BridgingMappingResource;
use App\Services\SimrsBridgeService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Throwable;

class ManageBridgingMappings extends ManageRecords
{
    protected static string $resource = BridgingMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('discover')
                ->label('Tarik Ruangan SIMRS')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalDescription('Mengambil daftar ruangan terbaru dari SIMRS tanpa mengubah mapping yang sudah dibuat.')
                ->action(function (): void {
                    try {
                        $result = (new SimrsBridgeService)->discoverRuanganMappings();

                        Notification::make()
                            ->success()
                            ->title('Data ruangan diperbarui')
                            ->body("{$result['total']} ruangan ditemukan; {$result['unmapped']} belum dipetakan.")
                            ->send();
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->danger()
                            ->title('Gagal mengambil ruangan')
                            ->body('Periksa koneksi dan konfigurasi SIMRS.')
                            ->send();
                    }
                }),
            Actions\CreateAction::make()->label('Tambah Manual'),
        ];
    }
}
