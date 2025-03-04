<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RuangAuditKepatuhanResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\RuangAuditKepatuhan;

class RuangAuditKepatuhanResource extends Resource
{
    protected static ?string $model = RuangAuditKepatuhan::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Ruang Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_ruang')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('nama_ruang')
                    ->required()
                    ->maxLength(40),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_ruang')
                    ->label('Id ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ruang')
                    ->label('Nama ruang')
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
            'index' => Pages\ManageRuangAuditKepatuhans::route('/'),
        ];
    }
}
