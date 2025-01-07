<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditBundleIskResource\Pages;
use App\Filament\Resources\AuditBundleIskResource\RelationManagers;
use App\Models\AuditBundleIsk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;

class AuditBundleIskResource extends Resource
{
    protected static ?string $model = AuditBundleIsk::class;

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
                Forms\Components\Select::make('pemasangan_sesuai_indikasi')
                    ->label('Pemasangan sesuai indikasi')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('hand_hygiene')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('menggunakan_apd_yang_tepat')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('pemasangan_menggunakan_alat_steril')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('segera_dilepas_setelah_tidak_diperlukan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('pengisian_balon_sesuai_petunjuk')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('fiksasi_kateter_dengan_plester')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('urinebag_menggantung_tidak_menyentuh_lantai')
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
                AuditBundleIsk::query()
                    ->with(['ruangAuditKepatuhan'])
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_bundle_isk.*', DB::raw('CONCAT(ROUND(((pemasangan_sesuai_indikasi = "Ya") + (hand_hygiene = "Ya") + (menggunakan_apd_yang_tepat = "Ya") + (pemasangan_menggunakan_alat_steril = "Ya") + (segera_dilepas_setelah_tidak_diperlukan = "Ya") + (pengisian_balon_sesuai_petunjuk = "Ya") + (fiksasi_kateter_dengan_plester = "Ya") + (urinebag_menggantung_tidak_menyentuh_lantai = "Ya")) / 8 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pemasangan_sesuai_indikasi'),
                Tables\Columns\TextColumn::make('hand_hygiene'),
                Tables\Columns\TextColumn::make('menggunakan_apd_yang_tepat'),
                Tables\Columns\TextColumn::make('pemasangan_menggunakan_alat_steril'),
                Tables\Columns\TextColumn::make('segera_dilepas_setelah_tidak_diperlukan'),
                Tables\Columns\TextColumn::make('pengisian_balon_sesuai_petunjuk'),
                Tables\Columns\TextColumn::make('fiksasi_kateter_dengan_plester'),
                Tables\Columns\TextColumn::make('urinebag_menggantung_tidak_menyentuh_lantai'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pemasangan_sesuai_indikasi')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pemasangan_sesuai_indikasi', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pemasangan_sesuai_indikasi', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pemasangan_sesuai_indikasi', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('hand_hygiene')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('hand_hygiene', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('hand_hygiene', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('hand_hygiene', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('menggunakan_apd_yang_tepat')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('menggunakan_apd_yang_tepat', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('menggunakan_apd_yang_tepat', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('menggunakan_apd_yang_tepat', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('pemasangan_menggunakan_alat_steril')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pemasangan_menggunakan_alat_steril', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pemasangan_menggunakan_alat_steril', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pemasangan_menggunakan_alat_steril', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('segera_dilepas_setelah_tidak_diperlukan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('segera_dilepas_setelah_tidak_diperlukan', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('segera_dilepas_setelah_tidak_diperlukan', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('segera_dilepas_setelah_tidak_diperlukan', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('pengisian_balon_sesuai_petunjuk')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pengisian_balon_sesuai_petunjuk', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pengisian_balon_sesuai_petunjuk', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pengisian_balon_sesuai_petunjuk', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('fiksasi_kateter_dengan_plester')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('fiksasi_kateter_dengan_plester', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('fiksasi_kateter_dengan_plester', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('fiksasi_kateter_dengan_plester', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('urinebag_menggantung_tidak_menyentuh_lantai')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('urinebag_menggantung_tidak_menyentuh_lantai', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('urinebag_menggantung_tidak_menyentuh_lantai', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('urinebag_menggantung_tidak_menyentuh_lantai', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemasangan_sesuai_indikasi == 'Ya') $ttl++;
                                if ($item->hand_hygiene == 'Ya') $ttl++;
                                if ($item->menggunakan_apd_yang_tepat == 'Ya') $ttl++;
                                if ($item->pemasangan_menggunakan_alat_steril == 'Ya') $ttl++;
                                if ($item->segera_dilepas_setelah_tidak_diperlukan == 'Ya') $ttl++;
                                if ($item->pengisian_balon_sesuai_petunjuk == 'Ya') $ttl++;
                                if ($item->fiksasi_kateter_dengan_plester == 'Ya') $ttl++;
                                if ($item->urinebag_menggantung_tidak_menyentuh_lantai == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemasangan_sesuai_indikasi == 'Tidak') $ttl++;
                                if ($item->hand_hygiene == 'Tidak') $ttl++;
                                if ($item->menggunakan_apd_yang_tepat == 'Tidak') $ttl++;
                                if ($item->pemasangan_menggunakan_alat_steril == 'Tidak') $ttl++;
                                if ($item->segera_dilepas_setelah_tidak_diperlukan == 'Tidak') $ttl++;
                                if ($item->pengisian_balon_sesuai_petunjuk == 'Tidak') $ttl++;
                                if ($item->fiksasi_kateter_dengan_plester == 'Tidak') $ttl++;
                                if ($item->urinebag_menggantung_tidak_menyentuh_lantai == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count() * 8;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemasangan_sesuai_indikasi == 'Ya') $ttl++;
                                if ($item->hand_hygiene == 'Ya') $ttl++;
                                if ($item->menggunakan_apd_yang_tepat == 'Ya') $ttl++;
                                if ($item->pemasangan_menggunakan_alat_steril == 'Ya') $ttl++;
                                if ($item->segera_dilepas_setelah_tidak_diperlukan == 'Ya') $ttl++;
                                if ($item->pengisian_balon_sesuai_petunjuk == 'Ya') $ttl++;
                                if ($item->fiksasi_kateter_dengan_plester == 'Ya') $ttl++;
                                if ($item->urinebag_menggantung_tidak_menyentuh_lantai == 'Ya') $ttl++;
                            }
                            return round((($ttl / $total) * 100), 2);
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
