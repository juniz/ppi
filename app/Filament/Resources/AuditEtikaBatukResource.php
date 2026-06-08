<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditEtikaBatukResource\Pages;
use App\Models\AuditEtikaBatuk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditEtikaBatukResource extends Resource
{
    protected static ?string $model = AuditEtikaBatuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-frown';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Etika Batuk';
    protected static ?string $navigationBadgeColor = 'warning';

    private const AUDIT_LABELS = [
        'audit1' => '1. Metode batuk menutup lengan bagian atas',
        'audit2' => '2. Metode batuk menggunakan tissue',
        'audit3' => '3. Metode batuk menggunakan masker',
        'audit4' => '4. Tissue dibuang di tempat sampah kuning',
        'audit5' => '5. Masker dibuang di tempat sampah kuning',
        'audit6' => '6. Lakukan hand hygiene',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('nik')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'nama')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->nama)
                    ->searchable(['nama'])
                    ->required(),
                ...self::auditFormFields(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditEtikaBatuk::query()
                    ->with('pegawai')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_etika_batuk.*', DB::raw(self::ttlExpression()))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                ...self::auditTableColumns(),
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

    private static function auditFormFields(): array
    {
        return array_map(
            fn(string $column, string $label) => Forms\Components\Select::make($column)
                ->label($label)
                ->options([
                    'Ya' => 'Ya',
                    'Tidak' => 'Tidak',
                    'Na' => 'Na',
                ])
                ->default('Ya')
                ->required(),
            array_keys(self::AUDIT_LABELS),
            self::AUDIT_LABELS
        );
    }

    private static function auditTableColumns(): array
    {
        return array_map(
            fn(string $column, string $label) => Tables\Columns\TextColumn::make($column)
                ->label($label)
                ->toggleable(isToggledHiddenByDefault: true),
            array_keys(self::AUDIT_LABELS),
            self::AUDIT_LABELS
        );
    }

    private static function ttlExpression(): string
    {
        $columns = array_keys(self::AUDIT_LABELS);
        $yesCount = collect($columns)->map(fn(string $column) => "({$column} = \"Ya\")")->implode(' + ');
        $validCount = collect($columns)->map(fn(string $column) => "({$column} != \"Na\")")->implode(' + ');

        return "CASE WHEN ({$validCount}) = 0 THEN \"0%\" ELSE CONCAT(ROUND(({$yesCount}) / ({$validCount}) * 100, 2), \"%\") END as ttl";
    }
}
