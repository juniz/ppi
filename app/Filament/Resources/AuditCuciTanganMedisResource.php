<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditCuciTanganMedisResource\Pages;
use App\Filament\Resources\AuditCuciTanganMedisResource\RelationManagers;
use App\Models\AuditCuciTanganMedis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class AuditCuciTanganMedisResource extends Resource
{
    protected static ?string $model = AuditCuciTanganMedis::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nik')
                    ->relationship('pegawai', 'nama')
                    ->getOptionLabelFromRecordUsing(fn(Model $record) => $record->nama)
                    ->searchable(['nama'])
                    ->getOptionLabelUsing(fn($value): ?string => Pegawai::find($value)?->nama)
                    ->createOptionForm(function (Form $form) {
                        return $form
                            ->schema([
                                Forms\Components\TextInput::make('nik')
                                    ->disabled(),
                                Forms\Components\TextInput::make('nama')
                                    ->disabled(),
                            ]);
                    })
                    ->required(),
                // Forms\Components\DateTimePicker::make('tanggal')
                //     ->required(),
                Forms\Components\Select::make('sebelum_menyentuh_pasien')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('sebelum_tehnik_aseptik')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('setelah_terpapar_cairan_tubuh_pasien')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('setelah_kontak_dengan_pasien')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('setelah_kontak_dengan_lingkungan_pasien')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditCuciTanganMedis::query()
                    ->with('pegawai')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_cuci_tangan_medis.*', DB::raw('CONCAT(ROUND(((sebelum_menyentuh_pasien = "Ya") + (sebelum_tehnik_aseptik = "Ya") + (setelah_terpapar_cairan_tubuh_pasien = "Ya") + (setelah_kontak_dengan_pasien = "Ya") + (setelah_kontak_dengan_lingkungan_pasien = "Ya")) / (5-ROUND((sebelum_menyentuh_pasien = "Na") + (sebelum_tehnik_aseptik = "Na") + (setelah_terpapar_cairan_tubuh_pasien = "Na") + (setelah_kontak_dengan_pasien = "Na") + (setelah_kontak_dengan_lingkungan_pasien = "Na"))) * 100, 2), "%") as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('sebelum_menyentuh_pasien')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                        'Na' => 'secondary',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                        'Na' => 'heroicon-o-minus-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('sebelum_tehnik_aseptik')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                        'Na' => 'secondary',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                        'Na' => 'heroicon-o-minus-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('setelah_terpapar_cairan_tubuh_pasien')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                        'Na' => 'secondary',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                        'Na' => 'heroicon-o-minus-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('setelah_kontak_dengan_pasien')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                        'Na' => 'secondary',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                        'Na' => 'heroicon-o-minus-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('setelah_kontak_dengan_lingkungan_pasien')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                        'Na' => 'secondary',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                        'Na' => 'heroicon-o-minus-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai'),
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
            'index' => Pages\ManageAuditCuciTanganMedis::route('/'),
        ];
    }
}
