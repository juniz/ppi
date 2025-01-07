<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KamarResource\Pages;
use App\Filament\Resources\KamarResource\RelationManagers;
use App\Models\Kamar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kd_bangsal')
                    ->relationship('bangsal', 'nm_bangsal')
                    ->required(),
                Forms\Components\TextInput::make('trf_kamar')
                    ->label('Tarif Kamar')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'KOSONG' => 'KOSONG',
                        'ISI' => 'ISI',
                        'DIBERSIHKAN' => 'DIBERSIHKAN',
                        'DIBOOKING' => 'DIBOOKING',
                    ])
                    ->required(),
                Forms\Components\Select::make('kelas')
                    ->options([
                        'Kelas 1' => 'Kelas 1',
                        'Kelas 2' => 'Kelas 2',
                        'Kelas 3' => 'Kelas 3',
                        'Kelas Utama' => 'Kelas Utama',
                        'Kelas VIP' => 'Kelas VIP',
                        'Kelas VVIP' => 'Kelas VVIP',
                    ])
                    ->required(),
                Forms\Components\Select::make('statusdata')
                    ->label('Status Data')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kd_kamar')
                    ->label('Kode Kamar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kd_bangsal')
                    ->label('Kode Bangsal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trf_kamar')
                    ->label('Tarif Kamar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('kelas'),
                Tables\Columns\TextColumn::make('statusdata')
                    ->label('Status Data'),
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
            'index' => Pages\ManageKamars::route('/'),
        ];
    }
}
