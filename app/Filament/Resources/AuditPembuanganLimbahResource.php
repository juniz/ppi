<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPembuanganLimbahResource\Pages;
use App\Filament\Resources\AuditPembuanganLimbahResource\RelationManagers;
use App\Models\AuditPembuanganLimbah;
use App\Models\RuangAuditKepatuhan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\RuangAuditKepatuhanApd;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;

class AuditPembuanganLimbahResource extends Resource
{
    protected static ?string $model = AuditPembuanganLimbah::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->options(
                        RuangAuditKepatuhan::pluck('nama_ruang', 'id_ruang')
                    )
                    ->getOptionLabelUsing(fn($value): ?string => RuangAuditKepatuhan::find($value)?->nama_ruangan)
                    ->required(),
                Forms\Components\Select::make('pemisahan_limbah_oleh_penghasil_limbah')
                    ->label('1. Pemisahan Limbah Dilakukan Segera Oleh Penghasil Limbah')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
                Forms\Components\Select::make('limbah_infeksius_dimasukkan_kantong_kuning')
                    ->label('2. Limbah Infeksius Dimasukkan Ke Kantong Plastik Warna Kuning')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
                Forms\Components\Select::make('limbah_noninfeksius_dimasukkan_kantong_hitam')
                    ->label('3. Limbah Non Infeksius Dimasukkan Ke Kantong Plastik Warna Hitam')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
                Forms\Components\Select::make('limbah_tigaperempat_diikat')
                    ->label('4. Limbah 3/4 Penuh Diikat')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
                Forms\Components\Select::make('limbah_segera_dibawa_kepembuangan_sementara')
                    ->label('5. Limbah segera dibawa ke tempat pembuangan sampah sementara')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
                Forms\Components\Select::make('kotak_sampah_dalam_kondisi_bersih')
                    ->label('6. Kotak sampah dalam kondisi bersih')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
                Forms\Components\Select::make('pembersihan_tempat_sampah_dengan_desinfekten')
                    ->label('7. Pembersihan tempat sampah menggunakan desinfekten')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
                Forms\Components\Select::make('pembersihan_penampungan_sementara_dengan_desinfekten')
                    ->label('8. Pembersihan penampungan sementara menggunakan desinfekten')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditPembuanganLimbah::query()
                    ->with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select(
                        'audit_pembuangan_limbah.*',
                        DB::raw('CONCAT(ROUND(((pemisahan_limbah_oleh_penghasil_limbah = "Ya") + (limbah_infeksius_dimasukkan_kantong_kuning = "Ya") + (limbah_noninfeksius_dimasukkan_kantong_hitam = "Ya") + (limbah_tigaperempat_diikat = "Ya") + (limbah_segera_dibawa_kepembuangan_sementara = "Ya") + (kotak_sampah_dalam_kondisi_bersih = "Ya") + (pembersihan_tempat_sampah_dengan_desinfekten = "Ya") + (pembersihan_penampungan_sementara_dengan_desinfekten = "Ya")) / 8 * 100, 2)) as ttl')
                    )
            )
            ->columns([
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->label('Ruangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d-m-Y H:i:s'),
                Tables\Columns\IconColumn::make('pemisahan_limbah_oleh_penghasil_limbah')
                    ->label('1. Pemisahan Limbah Dilakukan Segera Oleh Penghasil Limbah'),
                Tables\Columns\IconColumn::make('limbah_infeksius_dimasukkan_kantong_kuning')
                    ->label('2. Limbah Infeksius Dimasukkan Ke Kantong Plastik Warna Kuning')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('limbah_noninfeksius_dimasukkan_kantong_hitam')
                    ->label('3. Limbah Non Infeksius Dimasukkan Ke Kantong Plastik Warna Hitam')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('limbah_tigaperempat_diikat')
                    ->label('4. Limbah 3/4 Penuh Diikat')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('limbah_segera_dibawa_kepembuangan_sementara')
                    ->label('5. Limbah segera dibawa ke tempat pembuangan sampah sementara')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('kotak_sampah_dalam_kondisi_bersih')
                    ->label('6. Kotak sampah dalam kondisi bersih')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('pembersihan_tempat_sampah_dengan_desinfekten')
                    ->label('7. Pembersihan tempat sampah menggunakan desinfekten')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('pembersihan_penampungan_sementara_dengan_desinfekten')
                    ->label('8. Pembersihan penampungan sementara menggunakan desinfekten')
                    ->color(fn(string $state): string => match ($state) {
                        'Ya' => 'success',
                        'Tidak' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Ya' => 'heroicon-o-check-circle',
                        'Tidak' => 'heroicon-o-x-circle',
                    })
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('ttl')
                    ->suffix('%')
                    ->label('Ttl. Nilai'),
                Tables\Columns\TextColumn::make('pemisahan_limbah_oleh_penghasil_limbah')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pemisahan_limbah_oleh_penghasil_limbah', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pemisahan_limbah_oleh_penghasil_limbah', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pemisahan_limbah_oleh_penghasil_limbah', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('limbah_infeksius_dimasukkan_kantong_kuning')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('limbah_infeksius_dimasukkan_kantong_kuning', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('limbah_infeksius_dimasukkan_kantong_kuning', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('limbah_infeksius_dimasukkan_kantong_kuning', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('limbah_noninfeksius_dimasukkan_kantong_hitam')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('limbah_noninfeksius_dimasukkan_kantong_hitam', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('limbah_noninfeksius_dimasukkan_kantong_hitam', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('limbah_noninfeksius_dimasukkan_kantong_hitam', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('limbah_tigaperempat_diikat')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('limbah_tigaperempat_diikat', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('limbah_tigaperempat_diikat', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('limbah_tigaperempat_diikat', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('limbah_segera_dibawa_kepembuangan_sementara')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('limbah_segera_dibawa_kepembuangan_sementara', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('limbah_segera_dibawa_kepembuangan_sementara', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('limbah_segera_dibawa_kepembuangan_sementara', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('kotak_sampah_dalam_kondisi_bersih')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('kotak_sampah_dalam_kondisi_bersih', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('kotak_sampah_dalam_kondisi_bersih', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('kotak_sampah_dalam_kondisi_bersih', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('pembersihan_tempat_sampah_dengan_desinfekten')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pembersihan_tempat_sampah_dengan_desinfekten', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pembersihan_tempat_sampah_dengan_desinfekten', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pembersihan_tempat_sampah_dengan_desinfekten', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('pembersihan_penampungan_sementara_dengan_desinfekten')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('pembersihan_penampungan_sementara_dengan_desinfekten', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('pembersihan_penampungan_sementara_dengan_desinfekten', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('pembersihan_penampungan_sementara_dengan_desinfekten', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemisahan_limbah_oleh_penghasil_limbah == 'Ya') $ttl++;
                                if ($item->limbah_infeksius_dimasukkan_kantong_kuning == 'Ya') $ttl++;
                                if ($item->limbah_noninfeksius_dimasukkan_kantong_hitam == 'Ya') $ttl++;
                                if ($item->limbah_tigaperempat_diikat == 'Ya') $ttl++;
                                if ($item->limbah_segera_dibawa_kepembuangan_sementara == 'Ya') $ttl++;
                                if ($item->kotak_sampah_dalam_kondisi_bersih == 'Ya') $ttl++;
                                if ($item->pembersihan_tempat_sampah_dengan_desinfekten == 'Ya') $ttl++;
                                if ($item->pembersihan_penampungan_sementara_dengan_desinfekten == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemisahan_limbah_oleh_penghasil_limbah == 'Tidak') $ttl++;
                                if ($item->limbah_infeksius_dimasukkan_kantong_kuning == 'Tidak') $ttl++;
                                if ($item->limbah_noninfeksius_dimasukkan_kantong_hitam == 'Tidak') $ttl++;
                                if ($item->limbah_tigaperempat_diikat == 'Tidak') $ttl++;
                                if ($item->limbah_segera_dibawa_kepembuangan_sementara == 'Tidak') $ttl++;
                                if ($item->kotak_sampah_dalam_kondisi_bersih == 'Tidak') $ttl++;
                                if ($item->pembersihan_tempat_sampah_dengan_desinfekten == 'Tidak') $ttl++;
                                if ($item->pembersihan_penampungan_sementara_dengan_desinfekten == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count() * 8;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->pemisahan_limbah_oleh_penghasil_limbah == 'Ya') $ttl++;
                                if ($item->pemisahan_limbah_oleh_penghasil_limbah == 'Ya') $ttl++;
                                if ($item->limbah_infeksius_dimasukkan_kantong_kuning == 'Ya') $ttl++;
                                if ($item->limbah_noninfeksius_dimasukkan_kantong_hitam == 'Ya') $ttl++;
                                if ($item->limbah_tigaperempat_diikat == 'Ya') $ttl++;
                                if ($item->limbah_segera_dibawa_kepembuangan_sementara == 'Ya') $ttl++;
                                if ($item->kotak_sampah_dalam_kondisi_bersih == 'Ya') $ttl++;
                                if ($item->pembersihan_tempat_sampah_dengan_desinfekten == 'Ya') $ttl++;
                                if ($item->pembersihan_penampungan_sementara_dengan_desinfekten == 'Ya') $ttl++;
                            }
                            return ($ttl / $total) * 100;
                        })
                            ->suffix('%'),
                    ]),
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
            'index' => Pages\ManageAuditPembuanganLimbahs::route('/'),
        ];
    }
}
