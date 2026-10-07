<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BridgingMappingResource\Pages;
use App\Models\BridgingMapping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BridgingMappingResource extends Resource
{
    protected static ?string $model = BridgingMapping::class;

    protected static ?string $slug = 'bridging-mapping';

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Bridging SIMRS';

    protected static ?string $navigationLabel = 'Mapping Data';

    protected static ?string $modelLabel = 'mapping';

    protected static ?string $pluralModelLabel = 'Mapping Data';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Mapping Ruangan')
                ->description('Hubungkan kode ruangan dari SIMRS dengan master bangsal SiHAI\'s.')
                ->schema([
                    Forms\Components\Hidden::make('mapping_type')->default('ruangan'),
                    Forms\Components\TextInput::make('external_code')
                        ->label('Kode SIMRS')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('external_name')
                        ->label('Nama Ruangan SIMRS')
                        ->maxLength(255),
                    Forms\Components\Select::make('local_code')
                        ->label('Bangsal SiHAI\'s')
                        ->relationship('localBangsal', 'nm_bangsal')
                        ->getOptionLabelFromRecordUsing(fn ($record): string => "{$record->kd_bangsal} — {$record->nm_bangsal}")
                        ->searchable(['kd_bangsal', 'nm_bangsal'])
                        ->preload()
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Mapping aktif')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum ada data ruangan dari SIMRS')
            ->emptyStateDescription('Klik "Tarik Ruangan SIMRS" untuk mengambil kandidat mapping dari endpoint bridging.')
            ->emptyStateIcon('heroicon-o-arrows-right-left')
            ->columns([
                Tables\Columns\TextColumn::make('external_code')
                    ->label('Kode SIMRS')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('external_name')
                    ->label('Ruangan SIMRS')
                    ->searchable()
                    ->description(fn (BridgingMapping $record): ?string => $record->last_seen_at?->diffForHumans()),
                Tables\Columns\TextColumn::make('localBangsal.nm_bangsal')
                    ->label('Bangsal SiHAI\'s')
                    ->placeholder('Belum dipetakan')
                    ->description(fn (BridgingMapping $record): ?string => $record->local_code),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('mapping_status')
                    ->label('Status')
                    ->state(fn (BridgingMapping $record): string => filled($record->local_code) ? 'Terpetakan' : 'Perlu mapping')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Terpetakan' ? 'success' : 'warning'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('local_code')
                    ->label('Status mapping')
                    ->nullable()
                    ->trueLabel('Sudah dipetakan')
                    ->falseLabel('Belum dipetakan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Atur Mapping'),
            ])
            ->defaultSort('external_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBridgingMappings::route('/'),
        ];
    }
}
