<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPengendalianLingkunganResource\Pages;
use App\Models\AuditPengendalianLingkungan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditPengendalianLingkunganResource extends Resource
{
    protected static ?string $model = AuditPengendalianLingkungan::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Pengendalian Lingkungan';

    private const AUDIT_LABELS = [
        'audit1' => '1. Lingkungan udara bersih tidak berbau',
        'audit2' => '2. Sumber air lancar dan bersih sesuai kriteria air bersih',
        'audit3' => '3. Permukaan lingkungan kursi, meja/troli, dan loker tampak bersih dan dalam kondisi baik',
        'audit4' => '4. Penataan alat-alat rapi, lantai bersih dan dalam kondisi baik',
        'audit5' => '5. Tidak ditemukan debu di permukaan meja kerja',
        'audit6' => '6. Kipas angin bersih dan tidak berdebu',
        'audit7' => '7. AC dalam kondisi baik',
        'audit8' => '8. AC terdapat kartu control dari IPSRS',
        'audit9' => '9. Tirai pemisah ruang pasien/skeren dan tirai jendela bersih dan dalam kondisi baik',
        'audit10' => '10. Meja nurse station tidak kotor, rapi, dan tidak terdapat status pasien berserakan',
        'audit11' => '11. Terdapat wastafel, gambar cuci tangan, dan antiseptik',
        'audit12' => '12. Lingkungan terhindar dari binatang/serangga: semut, kucing, lalat, tikus, dll',
        'audit13' => '13. Area KM/toilet bebas dari benda-benda yang tidak sesuai',
        'audit14' => '14. Perlengkapan KM/toilet dalam kondisi baik',
        'audit15' => '15. Ada wastafel dengan kran air dan berfungsi dengan baik',
        'audit16' => '16. Wastafel tampak bersih, tidak terdapat benda-benda yang tidak sesuai',
        'audit17' => '17. Tersedia sabun cair di seluruh wastafel',
        'audit18' => '18. Terdapat gambar dan protap cuci tangan',
        'audit19' => '19. Tersedia fasilitas pembuangan sampah',
        'audit20' => '20. Alat makan kotor dimasukkan ke box kontainer tertutup yang telah disediakan',
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
                AuditPengendalianLingkungan::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_pengendalian_lingkungan.*', DB::raw(self::ttlExpression()))
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
            'index' => Pages\ManageAuditPengendalianLingkungans::route('/'),
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
