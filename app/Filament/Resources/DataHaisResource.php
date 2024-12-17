<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataHaisResource\Pages;
use App\Filament\Resources\DataHaisResource\RelationManagers;
use App\Models\DataHais;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataHaisResource extends Resource
{
    protected static ?string $model = DataHais::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->required(),
                Forms\Components\TextInput::make('ETT')
                    ->numeric(),
                Forms\Components\TextInput::make('CVL')
                    ->numeric(),
                Forms\Components\TextInput::make('IVL')
                    ->numeric(),
                Forms\Components\TextInput::make('UC')
                    ->numeric(),
                Forms\Components\TextInput::make('VAP')
                    ->numeric(),
                Forms\Components\TextInput::make('IAD')
                    ->numeric(),
                Forms\Components\TextInput::make('PLEB')
                    ->numeric(),
                Forms\Components\TextInput::make('ISK')
                    ->numeric(),
                Forms\Components\TextInput::make('ILO')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('HAP')
                    ->numeric(),
                Forms\Components\TextInput::make('Tinea')
                    ->numeric(),
                Forms\Components\TextInput::make('Scabies')
                    ->numeric(),
                Forms\Components\TextInput::make('DEKU'),
                Forms\Components\TextInput::make('SPUTUM')
                    ->maxLength(200),
                Forms\Components\TextInput::make('DARAH')
                    ->maxLength(200),
                Forms\Components\TextInput::make('URINE')
                    ->maxLength(200),
                Forms\Components\TextInput::make('ANTIBIOTIK')
                    ->maxLength(200),
                Forms\Components\TextInput::make('kd_kamar')
                    ->maxLength(15),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                DataHais::query()
                    ->with('regPeriksa')
            )
            ->columns([
                Tables\Columns\TextColumn::make('no_rawat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ETT')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('CVL')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('IVL')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('UC')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('VAP')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('IAD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('PLEB')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ISK')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ILO')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('HAP')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Tinea')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Scabies')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('DEKU'),
                Tables\Columns\TextColumn::make('SPUTUM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DARAH')
                    ->searchable(),
                Tables\Columns\TextColumn::make('URINE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ANTIBIOTIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kd_kamar')
                    ->searchable(),
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
            'index' => Pages\ManageDataHais::route('/'),
        ];
    }
}
