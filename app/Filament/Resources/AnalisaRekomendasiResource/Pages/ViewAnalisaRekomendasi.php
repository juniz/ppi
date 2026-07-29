<?php

namespace App\Filament\Resources\AnalisaRekomendasiResource\Pages;

use App\Filament\Resources\AnalisaRekomendasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ViewEntry;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewAnalisaRekomendasi extends ViewRecord
{
    protected static string $resource = AnalisaRekomendasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('export.analisa-rekomendasi.pdf', $this->getRecord()->id))
                ->openUrlInNewTab(),
            Actions\EditAction::make()
                ->visible(false), // Disable edit untuk read-only
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Periode')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->date('d/m/Y'),
                                TextEntry::make('tanggal_selesai')
                                    ->label('Tanggal Selesai')
                                    ->date('d/m/Y'),
                                TextEntry::make('ruangan')
                                    ->label('Ruangan'),
                            ]),
                    ]),

                Section::make('Analisa dan Rekomendasi')
                    ->schema([
                        TextEntry::make('analisa')
                            ->label('Analisa')
                            ->prose(),
                        TextEntry::make('rekomendasi')
                            ->label('Rekomendasi')
                            ->prose(),
                    ]),

                Section::make('Grafik HAIs')
                    ->schema([
                        ViewEntry::make('chart_images')
                            ->label('')
                            ->view('filament.infolists.entries.chart-images-detail'),
                    ])
                    ->collapsible(),

                // Menghapus bagian Data HAIs dan langsung menampilkan Data Detail
                Section::make('Data Detail')
                    ->schema([
                        ViewEntry::make('detailed_data')
                            ->label('')
                            ->view('filament.infolists.entries.detailed-hais-data'),
                    ])
                    ->collapsible(),

                Section::make('Informasi Sistem')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d/m/Y H:i:s'),
                                TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d/m/Y H:i:s'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}