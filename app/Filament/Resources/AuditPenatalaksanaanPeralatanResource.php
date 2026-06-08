<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPenatalaksanaanPeralatanResource\Pages;
use App\Models\AuditPenatalaksanaanPeralatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditPenatalaksanaanPeralatanResource extends Resource
{
    protected static ?string $model = AuditPenatalaksanaanPeralatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Penatalaksanaan Peralatan';

    private const AUDIT_LABELS = [
        'audit1' => 'Pre cleaning 1. Petugas menggunakan APD lengkap',
        'audit2' => 'Pre cleaning 2. Petugas memisahkan alat kesehatan disposable dan peralatan/perangkat medis kotor pakai ulang',
        'audit3' => 'Pre cleaning 3. Petugas membersihkan peralatan/perangkat medis kotor di bawah air mengalir',
        'audit4' => 'Pre cleaning 4. Petugas memasukkan peralatan/perangkat medis ke box kotor',
        'audit5' => 'Pre cleaning 5. Petugas segera mengirim peralatan/perangkat medis kotor ke ISS',
        'audit6' => 'Pre cleaning 6. Alur pre cleaning',
        'audit7' => 'Transportasi 1. Petugas menggunakan APD',
        'audit8' => 'Transportasi 2. Petugas memasukkan peralatan/perangkat medis kotor ke dalam box kotor tertutup',
        'audit9' => 'Transportasi 3. Petugas membawa 2 box tertutup, yaitu box bersih dan box kotor ke ISS',
        'audit10' => 'Transportasi 4. Petugas melakukan serah terima dengan petugas ISS',
        'audit11' => 'Transportasi 5. Petugas membawa peralatan/perangkat medis steril dalam box bersih',
        'audit12' => 'Penyimpanan 1. Simpan pada suhu 18-22 derajat C, kelembaban 35-75%',
        'audit13' => 'Penyimpanan 2. Tidak disimpan dekat saluran air/lokasi yang memiliki risiko basah',
        'audit14' => 'Penyimpanan 3. Simpan 30 cm dari lantai, 5 cm dari dinding, 45 cm dari atap/langit-langit',
        'audit15' => 'Penyimpanan 4. Alat digunakan dengan prinsip FIFO',
        'audit16' => 'Penyimpanan 5. Alat non critical dibersihkan menggunakan alkohol 70%',
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
                AuditPenatalaksanaanPeralatan::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_penatalaksanaan_peralatan.*', DB::raw(self::ttlExpression()))
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
            'index' => Pages\ManageAuditPenatalaksanaanPeralatans::route('/'),
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
