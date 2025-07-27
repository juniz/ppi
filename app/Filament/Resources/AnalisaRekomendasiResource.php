<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalisaRekomendasiResource\Pages;
use App\Models\AnalisaRekomendasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Infolists\Infolist;

class AnalisaRekomendasiResource extends Resource
{
    protected static ?string $model = AnalisaRekomendasi::class;

    // Menghapus konfigurasi navigasi agar tidak muncul di sidebar
    protected static bool $shouldRegisterNavigation = false;
    
    protected static ?string $modelLabel = 'Analisa & Rekomendasi';
    protected static ?string $pluralModelLabel = 'Data Analisa & Rekomendasi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->disabled(),
                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('ruangan')
                    ->label('Ruangan')
                    ->required()
                    ->disabled(),
                Forms\Components\Textarea::make('analisa')
                    ->label('Analisa')
                    ->rows(4)
                    ->disabled(),
                Forms\Components\Textarea::make('rekomendasi')
                    ->label('Rekomendasi')
                    ->rows(4)
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('periode')
                    ->label('Periode')
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->tanggal_mulai || !$record->tanggal_selesai) {
                            return '-';
                        }
                        return Carbon::parse($record->tanggal_mulai)->format('d/m/Y') . ' - ' . 
                               Carbon::parse($record->tanggal_selesai)->format('d/m/Y');
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('analisa')
                    ->label('Analisa')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('rekomendasi')
                    ->label('Rekomendasi')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                ViewColumn::make('summary_data')
                    ->label('Ringkasan Data HAIs')
                    ->view('filament.tables.columns.summary-hais'),
                ViewColumn::make('chart_images')
                    ->label('Grafik HAIs')
                    ->view('filament.tables.columns.chart-images'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('periode')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->where('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->where('tanggal_selesai', '<=', $date),
                            );
                    }),
                SelectFilter::make('ruangan')
                    ->label('Ruangan')
                    ->options(function () {
                        return AnalisaRekomendasi::distinct()
                            ->pluck('ruangan', 'ruangan')
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->visible(false), // Disable edit untuk read-only
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalisaRekomendasis::route('/'),
            'view' => Pages\ViewAnalisaRekomendasi::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Disable create karena data dibuat melalui halaman analisa laju
    }
}