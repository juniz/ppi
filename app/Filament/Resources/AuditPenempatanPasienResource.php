<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPenempatanPasienResource\Pages;
use App\Models\AuditPenempatanPasien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuditPenempatanPasienResource extends Resource
{
    protected static ?string $model = AuditPenempatanPasien::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Penempatan Pasien';

    private const AUDIT_LABELS = [
        'audit1' => '1. Tempatkan pasien infeksius terpisah dengan pasien non infeksius',
        'audit2' => '2. Penempatan pasien sesuai dengan transmisi (kontak, droplet, airborne)',
        'audit3' => '3. Terapkan sistem cohorting jika tidak ada ruang isolasi',
        'audit4' => '4. Sistem cohorting sesuai jenis transmisi (kontak, droplet, airborne)',
        'audit5' => '5. Isolasi pasien infeksius airborne dibatasi dengan fasilitas pelayanan kesehatan lain untuk menghindari transmisi penyakit',
        'audit6' => '6. Pasien HIV tidak dirawat bersama dengan TB tetapi pasien TB-HIV dirawat dengan pasien TB',
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
                AuditPenempatanPasien::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_penempatan_pasien.*', DB::raw(self::ttlExpression()))
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
            'index' => Pages\ManageAuditPenempatanPasiens::route('/'),
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
