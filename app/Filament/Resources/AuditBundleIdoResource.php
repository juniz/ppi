<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditBundleIdoResource\Pages;
use App\Models\AuditBundleIdo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AuditBundleIdoResource extends Resource
{
    protected static ?string $model = AuditBundleIdo::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Bundle IDO';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->label('Ruang')
                    ->relationship('ruangAuditKepatuhan', 'nama_ruang')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('pencukuran_rambut')
                    ->label('Pencukuran Rambut')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\Select::make('antibiotik')
                    ->label('Antibiotik')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\Select::make('temperature')
                    ->label('Temperature')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->selectablePlaceholder(false)
                    ->required(),
                Forms\Components\Select::make('sugar')
                    ->label('Sugar')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->selectablePlaceholder(false)
                    ->required(),
            ])
            ->statePath('data')
            ->model(AuditBundleIdo::class);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditBundleIdo::with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_bundle_ido.*', DB::raw('CONCAT(ROUND(((pencukuran_rambut = "Ya") + (antibiotik = "Ya") + (temperature = "Ya") + (sugar = "Ya")) / 4 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->label('Ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_rawat')
                    ->label('No Rawat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pencukuran_rambut'),
                Tables\Columns\TextColumn::make('antibiotik'),
                Tables\Columns\TextColumn::make('temperature'),
                Tables\Columns\TextColumn::make('sugar'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pencukuran_rambut')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn (Builder $query) => $query->where('pencukuran_rambut', 'Ya')),
                        Count::make()->label('Tidak')->query(fn (Builder $query) => $query->where('pencukuran_rambut', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pencukuran_rambut', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('antibiotik')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn (Builder $query) => $query->where('antibiotik', 'Ya')),
                        Count::make()->label('Tidak')->query(fn (Builder $query) => $query->where('antibiotik', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('antibiotik', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('temperature')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn (Builder $query) => $query->where('temperature', 'Ya')),
                        Count::make()->label('Tidak')->query(fn (Builder $query) => $query->where('temperature', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('temperature', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('sugar')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn (Builder $query) => $query->where('sugar', 'Ya')),
                        Count::make()->label('Tidak')->query(fn (Builder $query) => $query->where('sugar', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('sugar', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pencukuran_rambut == 'Ya') $ttl++;
                                if ($item->antibiotik == 'Ya') $ttl++;
                                if ($item->temperature == 'Ya') $ttl++;
                                if ($item->sugar == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pencukuran_rambut == 'Tidak') $ttl++;
                                if ($item->antibiotik == 'Tidak') $ttl++;
                                if ($item->temperature == 'Tidak') $ttl++;
                                if ($item->sugar == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count() * 4;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pencukuran_rambut == 'Ya') $ttl++;
                                if ($item->antibiotik == 'Ya') $ttl++;
                                if ($item->temperature == 'Ya') $ttl++;
                                if ($item->sugar == 'Ya') $ttl++;
                            }
                            return round((($ttl / $total) * 100), 2);
                        })->suffix('%'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuditBundleIdos::route('/'),
        ];
    }
}
