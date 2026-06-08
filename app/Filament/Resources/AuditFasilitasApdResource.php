<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditFasilitasApdResource\Pages;
use App\Models\AuditFasilitasApd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditFasilitasApdResource extends Resource
{
    protected static ?string $model = AuditFasilitasApd::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Fasilitas APD';

    private const AUDIT_LABELS = [
        'audit1' => '1. Tersedia/pakai sarung tangan bedah',
        'audit2' => '2. Tersedia/pakai sarung tangan steril',
        'audit3' => '3. Tersedia/pakai sarung tangan rumah tangga',
        'audit4' => '4. Tersedia/pakai masker bedah',
        'audit5' => '5. Tersedia/pakai masker N-95',
        'audit6' => '6. Tersedia/pakai gaun/schort/baju tindakan',
        'audit7' => '7. Tersedia/pakai alat pelindung wajah',
        'audit8' => '8. Tersedia/pakai kaca mata goggle',
        'audit9' => '9. Tersedia/pakai alat pelindung kaki/sepatu boot',
        'audit10' => '10. Tersedia/pakai alat pelindung kepala',
        'audit11' => '11. Tersedia eyewash (alat irigasi dengan cairan NaCl 9%)',
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
                AuditFasilitasApd::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_fasilitas_apd.*', DB::raw(self::ttlExpression()))
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
            'index' => Pages\ManageAuditFasilitasApds::route('/'),
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
