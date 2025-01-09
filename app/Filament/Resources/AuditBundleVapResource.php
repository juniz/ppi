<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditBundleVapResource\Pages;
use App\Filament\Resources\AuditBundleVapResource\RelationManagers;
use App\Models\AuditBundleVap;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;

class AuditBundleVapResource extends Resource
{
    protected static ?string $model = AuditBundleVap::class;

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
                Forms\Components\Select::make('posisi_kepala')
                    ->label('Posisi Kepala')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('pengkajian_setiap_hari')
                    ->label('Pengkajian Setiap Hari')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('hand_hygiene')
                    ->label('Hand Hygiene')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('oral_hygiene')
                    ->label('Oral Hygiene')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('suction_manajemen_sekresi')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('profilaksis_peptic_ulcer')
                    ->label('Profilaksis Peptic Ulcer')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('dvt_profiklasisi')
                    ->label('DVT Profiklasisi')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('penggunaan_apd_sesuai')
                    ->label('Penggunaan APD Sesuai')
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
                AuditBundleVap::query()
                    ->with(['ruangAuditKepatuhan'])
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_bundle_vap.*', DB::raw('CONCAT(ROUND(((posisi_kepala = "Ya") + (pengkajian_setiap_hari = "Ya") + (hand_hygiene = "Ya") + (oral_hygiene = "Ya") + (suction_manajemen_sekresi = "Ya") + (profilaksis_peptic_ulcer = "Ya") + (dvt_profiklasisi = "Ya") + (penggunaan_apd_sesuai = "Ya")) / 8 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('posisi_kepala'),
                Tables\Columns\TextColumn::make('pengkajian_setiap_hari'),
                Tables\Columns\TextColumn::make('hand_hygiene'),
                Tables\Columns\TextColumn::make('oral_hygiene'),
                Tables\Columns\TextColumn::make('suction_manajemen_sekresi'),
                Tables\Columns\TextColumn::make('profilaksis_peptic_ulcer'),
                Tables\Columns\TextColumn::make('dvt_profiklasisi'),
                Tables\Columns\TextColumn::make('penggunaan_apd_sesuai'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('posisi_kepala')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('posisi_kepala', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('posisi_kepala', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('posisi_kepala', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('pengkajian_setiap_hari')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pengkajian_setiap_hari', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pengkajian_setiap_hari', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pengkajian_setiap_hari', 'Ya')->count();
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
                Tables\Columns\TextColumn::make('oral_hygiene')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('oral_hygiene', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('oral_hygiene', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('oral_hygiene', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('suction_manajemen_sekresi')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('suction_manajemen_sekresi', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('suction_manajemen_sekresi', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('suction_manajemen_sekresi', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('profilaksis_peptic_ulcer')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('profilaksis_peptic_ulcer', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('profilaksis_peptic_ulcer', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('profilaksis_peptic_ulcer', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('dvt_profiklasisi')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('dvt_profiklasisi', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('dvt_profiklasisi', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('dvt_profiklasisi', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('penggunaan_apd_sesuai')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('penggunaan_apd_sesuai', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('penggunaan_apd_sesuai', 'Tidak')),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('penggunaan_apd_sesuai', 'Ya')->count();
                            return round($total == 0 ? 0 : ($ya / $total) * 100, 2);
                        })
                    ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->posisi_kepala == 'Ya') $ttl++;
                                if ($item->pengkajian_setiap_hari == 'Ya') $ttl++;
                                if ($item->hand_hygiene == 'Ya') $ttl++;
                                if ($item->oral_hygiene == 'Ya') $ttl++;
                                if ($item->suction_manajemen_sekresi == 'Ya') $ttl++;
                                if ($item->profilaksis_peptic_ulcer == 'Ya') $ttl++;
                                if ($item->dvt_profiklasisi == 'Ya') $ttl++;
                                if ($item->penggunaan_apd_sesuai == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->posisi_kepala == 'Tidak') $ttl++;
                                if ($item->pengkajian_setiap_hari == 'Tidak') $ttl++;
                                if ($item->hand_hygiene == 'Tidak') $ttl++;
                                if ($item->oral_hygiene == 'Tidak') $ttl++;
                                if ($item->suction_manajemen_sekresi == 'Tidak') $ttl++;
                                if ($item->profilaksis_peptic_ulcer == 'Tidak') $ttl++;
                                if ($item->dvt_profiklasisi == 'Tidak') $ttl++;
                                if ($item->penggunaan_apd_sesuai == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count() * 8;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->posisi_kepala == 'Ya') $ttl++;
                                if ($item->pengkajian_setiap_hari == 'Ya') $ttl++;
                                if ($item->hand_hygiene == 'Ya') $ttl++;
                                if ($item->oral_hygiene == 'Ya') $ttl++;
                                if ($item->suction_manajemen_sekresi == 'Ya') $ttl++;
                                if ($item->profilaksis_peptic_ulcer == 'Ya') $ttl++;
                                if ($item->dvt_profiklasisi == 'Ya') $ttl++;
                                if ($item->penggunaan_apd_sesuai == 'Ya') $ttl++;
                            }
                            return round((($ttl / $total) * 100), 2);
                        }),
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
            'index' => Pages\ManageAuditBundleVaps::route('/'),
        ];
    }
}
