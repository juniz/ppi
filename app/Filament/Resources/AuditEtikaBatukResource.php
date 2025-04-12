<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditEtikaBatukResource\Pages;
use App\Filament\Resources\AuditEtikaBatukResource\RelationManagers;
use App\Models\AuditEtikaBatuk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AuditEtikaBatukResource extends Resource
{
    protected static ?string $model = AuditEtikaBatuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-frown';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationBadgeColor = 'warning';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nik')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'nama')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->nama)
                    ->searchable(['nama'])
                    ->required(),
                Forms\Components\Select::make('tutup_mulut')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('buang_tissue')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('tisue_tutup_siku')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('kebersihan_tangan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('gunakan_masker')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditEtikaBatuk::query()
                    ->with('pegawai')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_etika_batuk.*', DB::raw('CONCAT(ROUND(((tutup_mulut = "Ya") + (buang_tissue = "Ya") + (tisue_tutup_siku = "Ya") + (kebersihan_tangan = "Ya") + (gunakan_masker = "Ya")) / 5 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tutup_mulut'),
                Tables\Columns\TextColumn::make('buang_tissue'),
                Tables\Columns\TextColumn::make('tisue_tutup_siku'),
                Tables\Columns\TextColumn::make('kebersihan_tangan'),
                Tables\Columns\TextColumn::make('gunakan_masker'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('TTL')
                    ->sortable(),
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
            'index' => Pages\ManageAuditEtikaBatuks::route('/'),
        ];
    }
}
