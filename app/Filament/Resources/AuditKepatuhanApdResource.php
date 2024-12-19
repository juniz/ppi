<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditKepatuhanApdResource\Pages;
use App\Filament\Resources\AuditKepatuhanApdResource\RelationManagers;
use App\Models\AuditKepatuhanApd;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

class AuditKepatuhanApdResource extends Resource
{
    protected static ?string $model = AuditKepatuhanApd::class;

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
                Forms\Components\TextInput::make('tindakan')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DateTimePicker::make('tanggal')
                    ->required(),
                Forms\Components\Select::make('topi')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('masker')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('kacamata')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('sarungtangan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('apron')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                        'Na' => 'Na',
                    ])
                    ->required(),
                Forms\Components\Select::make('sepatu')
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
                AuditKepatuhanApd::query()
                    ->with('pegawai')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_kepatuhan_apd.*', DB::raw('CONCAT(ROUND(((topi = "Ya") + (masker = "Ya") + (kacamata = "Ya") + (sarungtangan = "Ya") + (apron = "Ya") + (sepatu = "Ya")) / (6-ROUND((topi = "Na") + (masker = "Na") + (kacamata = "Na") + (sarungtangan = "Na") + (apron = "Na") + (sepatu = "Na"))) * 100, 2), "%") as ttl'))
            )
            ->columns([
                // Tables\Columns\TextColumn::make('nik')
                //     ->label('NIK')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tindakan')
                    ->searchable(),
                Tables\Columns\IconColumn::make('topi')
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
                Tables\Columns\IconColumn::make('masker')
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
                Tables\Columns\IconColumn::make('kacamata')
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
                Tables\Columns\IconColumn::make('sarungtangan')
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
                Tables\Columns\IconColumn::make('apron')
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
                Tables\Columns\IconColumn::make('sepatu')
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
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('start')
                            ->label('Tanggal Mulai'),
                        DatePicker::make('end')
                            ->label('Tanggal Selesai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['end'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
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
            'index' => Pages\ManageAuditKepatuhanApds::route('/'),
        ];
    }
}
