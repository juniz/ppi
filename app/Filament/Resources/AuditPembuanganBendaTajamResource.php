<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPembuanganBendaTajamResource\Pages;
use App\Filament\Resources\AuditPembuanganBendaTajamResource\RelationManagers;
use App\Models\AuditPembuanganBendaTajam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AuditPembuanganBendaTajamResource extends Resource
{
    protected static ?string $model = AuditPembuanganBendaTajam::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationGroup = 'Audit';

    private const TTL_EXPRESSION = 'CASE WHEN ((setiap_injeksi_needle_langsung_dimasukkan_safety_box != "Na") + (setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box != "Na") + (setiap_benda_tajam_jarum_dimasukkan_safety_box != "Na") + (safety_box_tigaperempat_diganti != "Na") + (safety_box_keadaan_bersih != "Na") + (saftey_box_tertutup_setelah_digunakan != "Na")) = 0 THEN "0%" ELSE CONCAT(ROUND(((setiap_injeksi_needle_langsung_dimasukkan_safety_box = "Ya") + (setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box = "Ya") + (setiap_benda_tajam_jarum_dimasukkan_safety_box = "Ya") + (safety_box_tigaperempat_diganti = "Ya") + (safety_box_keadaan_bersih = "Ya") + (saftey_box_tertutup_setelah_digunakan = "Ya")) / ((setiap_injeksi_needle_langsung_dimasukkan_safety_box != "Na") + (setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box != "Na") + (setiap_benda_tajam_jarum_dimasukkan_safety_box != "Na") + (safety_box_tigaperempat_diganti != "Na") + (safety_box_keadaan_bersih != "Na") + (saftey_box_tertutup_setelah_digunakan != "Na")) * 100, 2), "%") END as ttl';

    private static function complianceSummarizers(string $column): array
    {
        return [
            Count::make()->label('Ya')->query(fn($query) => $query->where($column, 'Ya')),
            Count::make()->label('Tidak')->query(fn($query) => $query->where($column, 'Tidak')),
            Summarizer::make()
                ->label('Rata-rata')
                ->using(function ($query) use ($column): float {
                    $total = (clone $query)->where($column, '!=', 'Na')->count();

                    if ($total === 0) {
                        return 0;
                    }

                    return round((clone $query)->where($column, 'Ya')->count() / $total * 100, 2);
                }),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->options(
                        \App\Models\RuangAuditKepatuhan::pluck('nama_ruang', 'id_ruang')
                    )
                    ->getOptionLabelUsing(fn($value): ?string => \App\Models\RuangAuditKepatuhan::find($value)?->nama_ruangan)
                    ->required(),
                Forms\Components\Select::make('setiap_injeksi_needle_langsung_dimasukkan_safety_box')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('setiap_benda_tajam_jarum_dimasukkan_safety_box')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('safety_box_tigaperempat_diganti')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('safety_box_keadaan_bersih')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('saftey_box_tertutup_setelah_digunakan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\AuditPembuanganBendaTajam::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_pembuangan_benda_tajam.*', DB::raw(self::TTL_EXPRESSION))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->label('Ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('setiap_injeksi_needle_langsung_dimasukkan_safety_box')
                    ->label('1. Setiap Pemberian Injeksi, Needle Langsung Dimasukkan Safety Box'),
                Tables\Columns\TextColumn::make('setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box')
                    ->label('2. Setiap Pemasangan IV Canula Langsung Dimasukkan Safety Box'),
                Tables\Columns\TextColumn::make('setiap_benda_tajam_jarum_dimasukkan_safety_box')
                    ->label('3. Setiap Benda Tajam (Jarum) Dimasukkan Safety Box'),
                Tables\Columns\TextColumn::make('safety_box_tigaperempat_diganti')
                    ->label('4. Safety Box Tiga Perempat Diganti'),
                Tables\Columns\TextColumn::make('safety_box_keadaan_bersih')
                    ->label('5. Safety Box Keadaan Bersih'),
                Tables\Columns\TextColumn::make('saftey_box_tertutup_setelah_digunakan')
                    ->label('6. Safety Box Tertutup Setelah Digunakan'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('setiap_injeksi_needle_langsung_dimasukkan_safety_box')
                    ->summarize(self::complianceSummarizers('setiap_injeksi_needle_langsung_dimasukkan_safety_box')),
                Tables\Columns\TextColumn::make('setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box')
                    ->summarize(self::complianceSummarizers('setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box')),
                Tables\Columns\TextColumn::make('setiap_benda_tajam_jarum_dimasukkan_safety_box')
                    ->summarize(self::complianceSummarizers('setiap_benda_tajam_jarum_dimasukkan_safety_box')),
                Tables\Columns\TextColumn::make('safety_box_tigaperempat_diganti')
                    ->summarize(self::complianceSummarizers('safety_box_tigaperempat_diganti')),
                Tables\Columns\TextColumn::make('safety_box_keadaan_bersih')
                    ->summarize(self::complianceSummarizers('safety_box_keadaan_bersih')),
                Tables\Columns\TextColumn::make('saftey_box_tertutup_setelah_digunakan')
                    ->summarize(self::complianceSummarizers('saftey_box_tertutup_setelah_digunakan')),
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
            'index' => Pages\ManageAuditPembuanganBendaTajams::route('/'),
        ];
    }
}
