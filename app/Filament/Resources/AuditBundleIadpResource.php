<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditBundleIadpResource\Pages;
use App\Filament\Resources\AuditBundleIadpResource\RelationManagers;
use App\Models\AuditBundleIadp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AuditBundleIadpResource extends Resource
{
    protected static ?string $model = AuditBundleIadp::class;
    protected static ?string $title = 'Audit Bundle IADP';
    protected static ?string $heading = 'Audit Bundle IADP';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Bundle IADP';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nik')
                    ->relationship('pegawai', 'nama')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->nama)
                    ->searchable(['nama', 'nik'])
                    ->required(),
                Forms\Components\Select::make('handhygiene')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak'
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('apd')
                    ->label('ALAT PELINDUNG DIRI')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak'
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('skin_antiseptik')
                    ->label('SKIN ANTISEPTIK')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak'
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('lokasi_iv')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak'
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('perawatan_rutin')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak'
                    ])
                    ->default('Ya')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditBundleIadp::query()
                    ->with('pegawai')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_bundle_iadp.*', DB::raw('CONCAT(ROUND(((handhygiene = "Ya") + (apd = "Ya") + (skin_antiseptik = "Ya") + (lokasi_iv = "Ya") + (perawatan_rutin = "Ya")) / 5 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pegawai.nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('handhygiene'),
                Tables\Columns\TextColumn::make('apd'),
                Tables\Columns\TextColumn::make('skin_antiseptik'),
                Tables\Columns\TextColumn::make('lokasi_iv'),
                Tables\Columns\TextColumn::make('perawatan_rutin'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('handhygiene')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('handhygiene', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('handhygiene', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('handhygiene', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                            ->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('apd')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('apd', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('apd', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('apd', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                            ->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('skin_antiseptik')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('skin_antiseptik', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('skin_antiseptik', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('skin_antiseptik', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                            ->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('lokasi_iv')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('lokasi_iv', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('lokasi_iv', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('lokasi_iv', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                            ->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('perawatan_rutin')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_rutin', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_rutin', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('perawatan_rutin', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                            ->suffix('%')
                    ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->handhygiene == 'Ya') $ttl++;
                                if ($item->apd == 'Ya') $ttl++;
                                if ($item->skin_antiseptik == 'Ya') $ttl++;
                                if ($item->lokasi_iv == 'Ya') $ttl++;
                                if ($item->perawatan_rutin == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->handhygiene == 'Tidak') $ttl++;
                                if ($item->apd == 'Tidak') $ttl++;
                                if ($item->skin_antiseptik == 'Tidak') $ttl++;
                                if ($item->lokasi_iv == 'Tidak') $ttl++;
                                if ($item->perawatan_rutin == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count() * 5;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->handhygiene == 'Ya') $ttl++;
                                if ($item->apd == 'Ya') $ttl++;
                                if ($item->skin_antiseptik == 'Ya') $ttl++;
                                if ($item->lokasi_iv == 'Ya') $ttl++;
                                if ($item->perawatan_rutin == 'Ya') $ttl++;
                            }
                            return round((($ttl / $total) * 100), 2);
                        })
                            ->suffix('%'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ManageAuditBundleIadps::route('/'),
        ];
    }
}
