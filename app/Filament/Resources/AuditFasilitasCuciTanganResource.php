<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditFasilitasCuciTanganResource\Pages;
use App\Models\AuditFasilitasCuciTangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditFasilitasCuciTanganResource extends Resource
{
    protected static ?string $model = AuditFasilitasCuciTangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Fasilitas Cuci Tangan';

    private const AUDIT_LABELS = [
        'audit1' => '1. Tersedia wastafel cuci tangan',
        'audit2' => '2. Kran air berfungsi dengan baik',
        'audit3' => '3. Tersedia sabun cair di seluruh wastafel',
        'audit4' => '4. Tersedia tissue towel di seluruh wastafel',
        'audit5' => '5. Tersedia fasilitas pembuangan sampah di dekat wastafel',
        'audit6' => '6. Tersedia alkohol hand rub di setiap kamar',
    ];

    public static function form(Form $form): Form
    {
        return $form->columns(1)->schema([
            Forms\Components\Select::make('id_ruang')
                ->label('Ruang')
                ->relationship('ruangAuditKepatuhan', 'nama_ruang')
                ->required(),
            ...self::auditFormFields(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditFasilitasCuciTangan::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_fasilitas_cuci_tangan.*', DB::raw(self::ttlExpression()))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')->label('Ruang')->searchable(),
                Tables\Columns\TextColumn::make('ttl')->label('Ttl. Nilai (%)')->sortable(),
                ...self::auditTableColumns(),
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
            'index' => Pages\ManageAuditFasilitasCuciTangans::route('/'),
        ];
    }

    private static function auditFormFields(): array
    {
        return array_map(fn(string $column, string $label) => Forms\Components\Select::make($column)
            ->label($label)
            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'Na' => 'Na'])
            ->default('Ya')
            ->required(), array_keys(self::AUDIT_LABELS), self::AUDIT_LABELS);
    }

    private static function auditTableColumns(): array
    {
        return array_map(fn(string $column, string $label) => Tables\Columns\TextColumn::make($column)
            ->label($label)
            ->toggleable(isToggledHiddenByDefault: true), array_keys(self::AUDIT_LABELS), self::AUDIT_LABELS);
    }

    private static function ttlExpression(): string
    {
        $columns = array_keys(self::AUDIT_LABELS);
        $yesCount = collect($columns)->map(fn(string $column) => "({$column} = \"Ya\")")->implode(' + ');
        $validCount = collect($columns)->map(fn(string $column) => "({$column} != \"Na\")")->implode(' + ');

        return "CASE WHEN ({$validCount}) = 0 THEN \"0%\" ELSE CONCAT(ROUND(({$yesCount}) / ({$validCount}) * 100, 2), \"%\") END as ttl";
    }
}
