<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokterResource\Pages;
use App\Filament\Resources\DokterResource\RelationManagers;
use App\Models\Dokter;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DokterResource extends Resource
{
    protected static ?string $model = Dokter::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Data Dokter';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('kd_dokter')
                            ->label('Kode Dokter')
                            ->required()
                            ->maxLength(20)
                            ->unique(table: Dokter::class, column: 'kd_dokter', ignoreRecord: true),
                        Forms\Components\TextInput::make('nm_dokter')
                            ->label('Nama Dokter')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('jk')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('tmp_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(20),
                        Forms\Components\DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->format('Y-m-d'),
                        // ... field lainnya
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kd_dokter')
                    ->label('Kode Dokter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nm_dokter')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('spesialis.nm_sps')
                    ->label('Spesialis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk')
                    ->label('Jenis Kelamin'),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('No. Telepon'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                // ... tambahkan filter jika diperlukan
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokters::route('/'),
            'create' => Pages\CreateDokter::route('/create'),
            'edit' => Pages\EditDokter::route('/{record}/edit'),
        ];
    }
}
