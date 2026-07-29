<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditBundleIskResource\Pages;
use App\Filament\Resources\AuditBundleIskResource\RelationManagers;
use App\Models\AuditBundleIsk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;

class AuditBundleIskResource extends Resource
{
    protected static ?string $model = AuditBundleIsk::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan Bundle';
    protected static ?string $navigationLabel = 'Bundle CAUTI';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->label('Ruang')
                    ->relationship('ruangAuditKepatuhan', 'nama_ruang')
                    ->required()
                    ->columnSpanFull(),
                    
                Forms\Components\Section::make('Pemasangan')
                    ->schema([
                        Forms\Components\Select::make('pemasangan_1_indikasi')
                            ->label('1. Indikasi pemasangan kateter urin menetap sesuai kebutuhan')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('pemasangan_2_hand_hygiene')
                            ->label('2. Hand hygiene dilakukan sebelum dan sesudah tindakan')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('pemasangan_3_teknik_aseptik')
                            ->label('3. Menggunakan teknik aseptik')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('pemasangan_4_alat_steril')
                            ->label('4. Menggunakan alat steril, pelumas steril, dan area kerja bersih')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Perawatan')
                    ->schema([
                        Forms\Components\Select::make('perawatan_1_hand_hygiene')
                            ->label('1. Hand hygiene sebelum dan sesudah memanipulasi kateter/perangkat')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_2_genitalia_dibersihkan')
                            ->label('2. Daerah genitalia dibersihkan')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_3_fiksasi_kateter')
                            ->label('3. Kateter diberikan fiksasi dengan baik')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_4_tidak_diganti_rutin')
                            ->label('4. Tidak dilakukan penggantian kateter secara rutin kecuali terjadi infeksi')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_5_aliran_steril_tertutup')
                            ->label('5. Sistem aliran urin dipertahankan steril dan tertutup')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_6_hubungan_kateter_tertutup')
                            ->label('6. Hubungan kateter dengan pipa drainase tidak dibuka kecuali atas indikasi')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_7_urine_bag_tidak_di_lantai')
                            ->label('7. Urine bag tidak diletakkan di lantai')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_8_selang_tidak_terlipat')
                            ->label('8. Selang tidak terlipat/tertekuk')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                        Forms\Components\Select::make('perawatan_10_segera_dilepas')
                            ->label('9. Kateter segera dilepas jika sudah tidak dibutuhkan')
                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
                            ->default('Ya')->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditBundleIsk::query()
                    ->with(['ruangAuditKepatuhan'])
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_bundle_isk.*', DB::raw('CONCAT(ROUND( ((pemasangan_1_indikasi = "Ya") + (pemasangan_2_hand_hygiene = "Ya") + (pemasangan_3_teknik_aseptik = "Ya") + (pemasangan_4_alat_steril = "Ya") + (perawatan_1_hand_hygiene = "Ya") + (perawatan_2_genitalia_dibersihkan = "Ya") + (perawatan_3_fiksasi_kateter = "Ya") + (perawatan_4_tidak_diganti_rutin = "Ya") + (perawatan_5_aliran_steril_tertutup = "Ya") + (perawatan_6_hubungan_kateter_tertutup = "Ya") + (perawatan_7_urine_bag_tidak_di_lantai = "Ya") + (perawatan_8_selang_tidak_terlipat = "Ya") + (perawatan_10_segera_dilepas = "Ya")) / NULLIF( ((pemasangan_1_indikasi != "NA") + (pemasangan_2_hand_hygiene != "NA") + (pemasangan_3_teknik_aseptik != "NA") + (pemasangan_4_alat_steril != "NA") + (perawatan_1_hand_hygiene != "NA") + (perawatan_2_genitalia_dibersihkan != "NA") + (perawatan_3_fiksasi_kateter != "NA") + (perawatan_4_tidak_diganti_rutin != "NA") + (perawatan_5_aliran_steril_tertutup != "NA") + (perawatan_6_hubungan_kateter_tertutup != "NA") + (perawatan_7_urine_bag_tidak_di_lantai != "NA") + (perawatan_8_selang_tidak_terlipat != "NA") + (perawatan_10_segera_dilepas != "NA")), 0) * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->searchable(),
                ColumnGroup::make('Pemasangan')->columns([
                Tables\Columns\TextColumn::make('pemasangan_1_indikasi')->label('1. Indikasi pemasangan kateter urin menetap sesuai kebutuhan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pemasangan_1_indikasi', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pemasangan_1_indikasi', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('pemasangan_1_indikasi', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('pemasangan_1_indikasi', '!=', 'NA')->count();
                            $ya = $query->where('pemasangan_1_indikasi', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('pemasangan_2_hand_hygiene')->label('2. Hand hygiene dilakukan sebelum dan sesudah tindakan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pemasangan_2_hand_hygiene', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pemasangan_2_hand_hygiene', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('pemasangan_2_hand_hygiene', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('pemasangan_2_hand_hygiene', '!=', 'NA')->count();
                            $ya = $query->where('pemasangan_2_hand_hygiene', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('pemasangan_3_teknik_aseptik')->label('3. Menggunakan teknik aseptik')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pemasangan_3_teknik_aseptik', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pemasangan_3_teknik_aseptik', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('pemasangan_3_teknik_aseptik', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('pemasangan_3_teknik_aseptik', '!=', 'NA')->count();
                            $ya = $query->where('pemasangan_3_teknik_aseptik', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('pemasangan_4_alat_steril')->label('4. Menggunakan alat steril, pelumas steril, dan area kerja bersih')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pemasangan_4_alat_steril', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pemasangan_4_alat_steril', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('pemasangan_4_alat_steril', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('pemasangan_4_alat_steril', '!=', 'NA')->count();
                            $ya = $query->where('pemasangan_4_alat_steril', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                            ]),
            ColumnGroup::make('Perawatan')->columns([
                Tables\Columns\TextColumn::make('perawatan_1_hand_hygiene')->label('1. Hand hygiene sebelum dan sesudah memanipulasi kateter/perangkat')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_1_hand_hygiene', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_1_hand_hygiene', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_1_hand_hygiene', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_1_hand_hygiene', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_1_hand_hygiene', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_2_genitalia_dibersihkan')->label('2. Daerah genitalia dibersihkan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_2_genitalia_dibersihkan', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_2_genitalia_dibersihkan', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_2_genitalia_dibersihkan', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_2_genitalia_dibersihkan', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_2_genitalia_dibersihkan', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_3_fiksasi_kateter')->label('3. Kateter diberikan fiksasi dengan baik')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_3_fiksasi_kateter', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_3_fiksasi_kateter', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_3_fiksasi_kateter', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_3_fiksasi_kateter', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_3_fiksasi_kateter', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_4_tidak_diganti_rutin')->label('4. Tidak dilakukan penggantian kateter secara rutin kecuali terjadi infeksi')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_4_tidak_diganti_rutin', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_4_tidak_diganti_rutin', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_4_tidak_diganti_rutin', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_4_tidak_diganti_rutin', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_4_tidak_diganti_rutin', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_5_aliran_steril_tertutup')->label('5. Sistem aliran urin dipertahankan steril dan tertutup')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_5_aliran_steril_tertutup', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_5_aliran_steril_tertutup', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_5_aliran_steril_tertutup', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_5_aliran_steril_tertutup', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_5_aliran_steril_tertutup', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_6_hubungan_kateter_tertutup')->label('6. Hubungan kateter dengan pipa drainase tidak dibuka kecuali atas indikasi')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_6_hubungan_kateter_tertutup', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_6_hubungan_kateter_tertutup', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_6_hubungan_kateter_tertutup', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_6_hubungan_kateter_tertutup', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_6_hubungan_kateter_tertutup', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_7_urine_bag_tidak_di_lantai')->label('7. Urine bag tidak diletakkan di lantai')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_7_urine_bag_tidak_di_lantai', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_7_urine_bag_tidak_di_lantai', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_7_urine_bag_tidak_di_lantai', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_7_urine_bag_tidak_di_lantai', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_7_urine_bag_tidak_di_lantai', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_8_selang_tidak_terlipat')->label('8. Selang tidak terlipat/tertekuk')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_8_selang_tidak_terlipat', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_8_selang_tidak_terlipat', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_8_selang_tidak_terlipat', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_8_selang_tidak_terlipat', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_8_selang_tidak_terlipat', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_10_segera_dilepas')->label('9. Kateter segera dilepas jika sudah tidak dibutuhkan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_10_segera_dilepas', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_10_segera_dilepas', 'Tidak')),
                        Count::make()->label('NA')->query(fn(Builder $query) => $query->where('perawatan_10_segera_dilepas', 'NA')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->where('perawatan_10_segera_dilepas', '!=', 'NA')->count();
                            $ya = $query->where('perawatan_10_segera_dilepas', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                            ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemasangan_1_indikasi == 'Ya') $ttl++;
                                if ($item->pemasangan_2_hand_hygiene == 'Ya') $ttl++;
                                if ($item->pemasangan_3_teknik_aseptik == 'Ya') $ttl++;
                                if ($item->pemasangan_4_alat_steril == 'Ya') $ttl++;
                                if ($item->perawatan_1_hand_hygiene == 'Ya') $ttl++;
                                if ($item->perawatan_2_genitalia_dibersihkan == 'Ya') $ttl++;
                                if ($item->perawatan_3_fiksasi_kateter == 'Ya') $ttl++;
                                if ($item->perawatan_4_tidak_diganti_rutin == 'Ya') $ttl++;
                                if ($item->perawatan_5_aliran_steril_tertutup == 'Ya') $ttl++;
                                if ($item->perawatan_6_hubungan_kateter_tertutup == 'Ya') $ttl++;
                                if ($item->perawatan_7_urine_bag_tidak_di_lantai == 'Ya') $ttl++;
                                if ($item->perawatan_8_selang_tidak_terlipat == 'Ya') $ttl++;
                                if ($item->perawatan_10_segera_dilepas == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemasangan_1_indikasi == 'Tidak') $ttl++;
                                if ($item->pemasangan_2_hand_hygiene == 'Tidak') $ttl++;
                                if ($item->pemasangan_3_teknik_aseptik == 'Tidak') $ttl++;
                                if ($item->pemasangan_4_alat_steril == 'Tidak') $ttl++;
                                if ($item->perawatan_1_hand_hygiene == 'Tidak') $ttl++;
                                if ($item->perawatan_2_genitalia_dibersihkan == 'Tidak') $ttl++;
                                if ($item->perawatan_3_fiksasi_kateter == 'Tidak') $ttl++;
                                if ($item->perawatan_4_tidak_diganti_rutin == 'Tidak') $ttl++;
                                if ($item->perawatan_5_aliran_steril_tertutup == 'Tidak') $ttl++;
                                if ($item->perawatan_6_hubungan_kateter_tertutup == 'Tidak') $ttl++;
                                if ($item->perawatan_7_urine_bag_tidak_di_lantai == 'Tidak') $ttl++;
                                if ($item->perawatan_8_selang_tidak_terlipat == 'Tidak') $ttl++;
                                if ($item->perawatan_10_segera_dilepas == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = 0;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemasangan_1_indikasi != 'NA') $total++;
                                if ($item->pemasangan_1_indikasi == 'Ya') $ttl++;
                                if ($item->pemasangan_2_hand_hygiene != 'NA') $total++;
                                if ($item->pemasangan_2_hand_hygiene == 'Ya') $ttl++;
                                if ($item->pemasangan_3_teknik_aseptik != 'NA') $total++;
                                if ($item->pemasangan_3_teknik_aseptik == 'Ya') $ttl++;
                                if ($item->pemasangan_4_alat_steril != 'NA') $total++;
                                if ($item->pemasangan_4_alat_steril == 'Ya') $ttl++;
                                if ($item->perawatan_1_hand_hygiene != 'NA') $total++;
                                if ($item->perawatan_1_hand_hygiene == 'Ya') $ttl++;
                                if ($item->perawatan_2_genitalia_dibersihkan != 'NA') $total++;
                                if ($item->perawatan_2_genitalia_dibersihkan == 'Ya') $ttl++;
                                if ($item->perawatan_3_fiksasi_kateter != 'NA') $total++;
                                if ($item->perawatan_3_fiksasi_kateter == 'Ya') $ttl++;
                                if ($item->perawatan_4_tidak_diganti_rutin != 'NA') $total++;
                                if ($item->perawatan_4_tidak_diganti_rutin == 'Ya') $ttl++;
                                if ($item->perawatan_5_aliran_steril_tertutup != 'NA') $total++;
                                if ($item->perawatan_5_aliran_steril_tertutup == 'Ya') $ttl++;
                                if ($item->perawatan_6_hubungan_kateter_tertutup != 'NA') $total++;
                                if ($item->perawatan_6_hubungan_kateter_tertutup == 'Ya') $ttl++;
                                if ($item->perawatan_7_urine_bag_tidak_di_lantai != 'NA') $total++;
                                if ($item->perawatan_7_urine_bag_tidak_di_lantai == 'Ya') $ttl++;
                                if ($item->perawatan_8_selang_tidak_terlipat != 'NA') $total++;
                                if ($item->perawatan_8_selang_tidak_terlipat == 'Ya') $ttl++;
                                if ($item->perawatan_10_segera_dilepas != 'NA') $total++;
                                if ($item->perawatan_10_segera_dilepas == 'Ya') $ttl++;
                            }
                            return round($total == 0 ? 0 : (($ttl / $total) * 100), 2);
                        })
                            ->suffix('%'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ManageAuditBundleIsks::route('/'),
        ];
    }
}
