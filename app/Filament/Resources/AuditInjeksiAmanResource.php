<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditInjeksiAmanResource\Pages;
use App\Models\AuditInjeksiAman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditInjeksiAmanResource extends Resource
{
    protected static ?string $model = AuditInjeksiAman::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Injeksi Aman';

    private const AUDIT_LABELS = [
        'audit1' => '1. Melakukan cuci tangan',
        'audit2' => '2. Menggunakan jarum dan spuit satu kali pakai untuk satu jenis obat',
        'audit3' => '3. Melakukan pengoplosan obat di tempat khusus (aseptic dispensing)',
        'audit4' => '4. Gunakan singledose untuk obat injeksi. Jika menggunakan obat multidose semua alat yang akan dipergunakan harus steril dan perlakukan penyimpanan obat sesuai aturan',
        'audit5' => '5. Tidak memberikan obat-obat singledose kepada lebih dari satu pasien atau mencampur obat-obat sisa dari vial/ampul untuk pemberian berikutnya',
        'audit6' => '6. Gunakan cairan pelarut hanya untuk satu kali dan satu pasien',
        'audit7' => '7. Memeriksa obat sudah sesuai (tepat obat, tepat dosis, tepat pemberian, tepat waktu)',
        'audit8' => '8. Memeriksa tepat pasien',
        'audit9' => '9. Desinfeksi sebelum injeksi',
        'audit10' => '10. Membuang sampah pada tempatnya',
        'audit11' => '11. Tidak melakukan re-capping',
        'audit12' => '12. Melepas APD jika memakai',
        'audit13' => '13. Cuci tangan setelah melakukan injeksi',
        'audit14' => '14. Tersedia hand rubs di troli',
        'audit15' => '15. Tersedia tempat sampah infeksius troli',
        'audit16' => '16. Tersedia tempat sampah non infeksius',
        'audit17' => '17. Tersedia safety box troli',
        'audit18' => '18. Tersedia alkohol swab untuk desinfeksi troli',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
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
                AuditInjeksiAman::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_injeksi_aman.*', DB::raw(self::ttlExpression()))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->label('Ruang')
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
            'index' => Pages\ManageAuditInjeksiAmans::route('/'),
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
        $yesCount = collect($columns)
            ->map(fn(string $column) => "({$column} = \"Ya\")")
            ->implode(' + ');
        $validCount = collect($columns)
            ->map(fn(string $column) => "({$column} != \"Na\")")
            ->implode(' + ');

        return "CASE WHEN ({$validCount}) = 0 THEN \"0%\" ELSE CONCAT(ROUND(({$yesCount}) / ({$validCount}) * 100, 2), \"%\") END as ttl";
    }
}
