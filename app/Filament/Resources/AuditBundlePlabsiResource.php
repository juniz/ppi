<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditBundlePlabsiResource\Pages;
use App\Filament\Resources\AuditBundlePlabsiResource\RelationManagers;
use App\Models\AuditBundlePlabsi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AuditBundlePlabsiResource extends Resource
{
    protected static ?string $model = AuditBundlePlabsi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->label('Ruang')
                    ->relationship('ruangAuditKepatuhan', 'nama_ruang')
                    ->required(),
                Forms\Components\Select::make('sebelum_melakukan_hand_hygiene')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('menggunakan_apd_lengkap')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('lokasi_pemasangan_sesuai')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('alat_yang_digunakan_steril')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('pembersihan_kulit')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('setelah_melakukan_hand_hygiene')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('perawatan_dressing_infus')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('spoit_yang_digunakan_disposible')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('memberi_tanggal_dan_jam_pemasangan_infus')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('set_infus_setiap_72jam')
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
                AuditBundlePlabsi::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_bundle_plabsi.*', DB::raw('CONCAT(ROUND(((sebelum_melakukan_hand_hygiene = "Ya") + (menggunakan_apd_lengkap = "Ya") + (lokasi_pemasangan_sesuai = "Ya") + (alat_yang_digunakan_steril = "Ya") + (pembersihan_kulit = "Ya") + (setelah_melakukan_hand_hygiene = "Ya") + (perawatan_dressing_infus = "Ya") + (spoit_yang_digunakan_disposible = "Ya") + (memberi_tanggal_dan_jam_pemasangan_infus = "Ya") + (set_infus_setiap_72jam = "Ya")) / 10 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->label('Ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sebelum_melakukan_hand_hygiene'),
                Tables\Columns\TextColumn::make('menggunakan_apd_lengkap'),
                Tables\Columns\TextColumn::make('lokasi_pemasangan_sesuai'),
                Tables\Columns\TextColumn::make('alat_yang_digunakan_steril'),
                Tables\Columns\TextColumn::make('pembersihan_kulit'),
                Tables\Columns\TextColumn::make('setelah_melakukan_hand_hygiene'),
                Tables\Columns\TextColumn::make('perawatan_dressing_infus'),
                Tables\Columns\TextColumn::make('spoit_yang_digunakan_disposible'),
                Tables\Columns\TextColumn::make('memberi_tanggal_dan_jam_pemasangan_infus'),
                Tables\Columns\TextColumn::make('set_infus_setiap_72jam'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sebelum_melakukan_hand_hygiene')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('sebelum_melakukan_hand_hygiene', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('sebelum_melakukan_hand_hygiene', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('sebelum_melakukan_hand_hygiene', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('menggunakan_apd_lengkap')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('menggunakan_apd_lengkap', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('menggunakan_apd_lengkap', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('menggunakan_apd_lengkap', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('lokasi_pemasangan_sesuai')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('lokasi_pemasangan_sesuai', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('lokasi_pemasangan_sesuai', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('lokasi_pemasangan_sesuai', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('alat_yang_digunakan_steril')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('alat_yang_digunakan_steril', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('alat_yang_digunakan_steril', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('alat_yang_digunakan_steril', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('pembersihan_kulit')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pembersihan_kulit', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pembersihan_kulit', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pembersihan_kulit', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('setelah_melakukan_hand_hygiene')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('setelah_melakukan_hand_hygiene', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('setelah_melakukan_hand_hygiene', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('setelah_melakukan_hand_hygiene', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('perawatan_dressing_infus')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('perawatan_dressing_infus', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('perawatan_dressing_infus', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('perawatan_dressing_infus', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('spoit_yang_digunakan_disposible')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('spoit_yang_digunakan_disposible', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('spoit_yang_digunakan_disposible', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('spoit_yang_digunakan_disposible', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('memberi_tanggal_dan_jam_pemasangan_infus')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('memberi_tanggal_dan_jam_pemasangan_infus', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('memberi_tanggal_dan_jam_pemasangan_infus', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('memberi_tanggal_dan_jam_pemasangan_infus', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('set_infus_setiap_72jam')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('set_infus_setiap_72jam', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('set_infus_setiap_72jam', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('set_infus_setiap_72jam', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->sebelum_melakukan_hand_hygiene == 'Ya') $ttl++;
                                if ($item->menggunakan_apd_lengkap == 'Ya') $ttl++;
                                if ($item->lokasi_pemasangan_sesuai == 'Ya') $ttl++;
                                if ($item->alat_yang_digunakan_steril == 'Ya') $ttl++;
                                if ($item->pembersihan_kulit == 'Ya') $ttl++;
                                if ($item->setelah_melakukan_hand_hygiene == 'Ya') $ttl++;
                                if ($item->perawatan_dressing_infus == 'Ya') $ttl++;
                                if ($item->spoit_yang_digunakan_disposible == 'Ya') $ttl++;
                                if ($item->memberi_tanggal_dan_jam_pemasangan_infus == 'Ya') $ttl++;
                                if ($item->set_infus_setiap_72jam == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->sebelum_melakukan_hand_hygiene == 'Tidak') $ttl++;
                                if ($item->menggunakan_apd_lengkap == 'Tidak') $ttl++;
                                if ($item->lokasi_pemasangan_sesuai == 'Tidak') $ttl++;
                                if ($item->alat_yang_digunakan_steril == 'Tidak') $ttl++;
                                if ($item->pembersihan_kulit == 'Tidak') $ttl++;
                                if ($item->setelah_melakukan_hand_hygiene == 'Tidak') $ttl++;
                                if ($item->perawatan_dressing_infus == 'Tidak') $ttl++;
                                if ($item->spoit_yang_digunakan_disposible == 'Tidak') $ttl++;
                                if ($item->memberi_tanggal_dan_jam_pemasangan_infus == 'Tidak') $ttl++;
                                if ($item->set_infus_setiap_72jam == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count() * 10;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->sebelum_melakukan_hand_hygiene == 'Ya') $ttl++;
                                if ($item->menggunakan_apd_lengkap == 'Ya') $ttl++;
                                if ($item->lokasi_pemasangan_sesuai == 'Ya') $ttl++;
                                if ($item->alat_yang_digunakan_steril == 'Ya') $ttl++;
                                if ($item->pembersihan_kulit == 'Ya') $ttl++;
                                if ($item->setelah_melakukan_hand_hygiene == 'Ya') $ttl++;
                                if ($item->perawatan_dressing_infus == 'Ya') $ttl++;
                                if ($item->spoit_yang_digunakan_disposible == 'Ya') $ttl++;
                                if ($item->memberi_tanggal_dan_jam_pemasangan_infus == 'Ya') $ttl++;
                                if ($item->set_infus_setiap_72jam == 'Ya') $ttl++;
                            }
                            return round((($ttl / $total) * 100), 2);
                        })
                    ])
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
            'index' => Pages\ManageAuditBundlePlabsis::route('/'),
        ];
    }
}
