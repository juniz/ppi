<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use App\Models\RegPeriksa;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Dokter;
use App\Models\Poliklinik;
use Filament\Forms\Components\Select;
use App\Models\Penjab;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Filament\Tables\Enums\ActionsPosition;

class Igd extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $model = RegPeriksa::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Data Pasien';
    protected static ?int $navigationSort = -3;

    protected static string $view = 'filament.pages.igd';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make('Daftar')
                ->label('Daftar Pasien')
                ->icon('heroicon-o-plus-circle')
                ->modalHeading('Pasien Baru')
                ->action(function (array $data): void {
                    try {
                        $data['kd_poli'] = env('IGD', 'IGDK');
                        $pasien = \App\Models\Pasien::where('no_rkm_medis', $data['no_rkm_medis'])->first();
                        $cekStatus = \App\Models\RegPeriksa::where('no_rkm_medis', $data['no_rkm_medis'])->where('status_lanjut', 'Ralan')->first();
                        $tgl_lahir = Carbon::parse($pasien->tgl_lahir);
                        $data['umurdaftar'] = $tgl_lahir->diff(Carbon::now())->format('%y Th %m Bl %d Hr');
                        $data['no_reg'] = RegPeriksa::generateNoReg($data['kd_dokter'], $data['kd_poli']);
                        $data['no_rawat'] = RegPeriksa::generateNoRawat();
                        $data['tgl_registrasi'] = date('Y-m-d');
                        $data['jam_reg'] = date('H:i:s');
                        $data['status_lanjut'] = 'Ralan';
                        $data['stts'] = 'Belum';
                        $data['sttsumur'] = 'Th';
                        $data['biaya_reg'] = \App\Models\Poliklinik::where('kd_poli', $data['kd_poli'])->first()->registrasi;
                        $data['status_bayar'] = 'Belum Bayar';
                        $data['stts_daftar'] = $cekStatus ? 'Lama' : 'Baru';

                        DB::transaction(function () use ($data, $pasien) {
                            RegPeriksa::create($data);
                            $pasien->umur = $data['umurdaftar'];
                            $pasien->save();
                        });

                        Notification::make()
                            ->title('Pasien Berhasil Didaftarkan')
                            ->success()
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Notification::make()
                            ->title('Gagal Mendaftarkan Pasien')
                            ->body($e->getMessage())
                            ->danger()
                            // ->icon('heroicon-o-exclamation')
                            // ->iconColor('error')
                            ->send();
                    }
                })
                ->form([
                    Select::make('no_rkm_medis')
                        ->label('Pasien')
                        ->placeholder('cari pasien...')
                        ->searchable()
                        ->reactive()
                        ->getSearchResultsUsing(function (string $search) {
                            return \App\Models\Pasien::query()
                                ->where('nm_pasien', 'like', "%$search%")
                                ->orWhere('no_rkm_medis', 'like', "%$search%")
                                ->limit(10)
                                ->select('nm_pasien', 'no_rkm_medis')
                                ->pluck('nm_pasien', 'no_rkm_medis');
                        })
                        ->afterStateUpdated(function ($state, callable $set) {
                            $pasien = \App\Models\Pasien::where('no_rkm_medis', $state)->first();
                            $cekStatus = \App\Models\RegPeriksa::where('no_rkm_medis', $state)->where('status_lanjut', 'Ralan')->first();
                            $set('p_jawab', $pasien->namakeluarga ?? '');
                            $set('hubunganpj', $pasien->keluarga ?? '');
                            $set('almt_pj', $pasien->alamatpj ?? '');
                            $set('status_poli', $cekStatus ? 'Lama' : 'Baru');
                        })
                        ->required(),
                    TextInput::make('p_jawab')
                        ->label('Penanggung Jawab')
                        ->reactive()
                        ->required(),
                    TextInput::make('hubunganpj')
                        ->label('Hubungan Penanggung Jawab')
                        ->reactive()
                        ->required(),
                    TextInput::make('almt_pj')
                        ->label('Alamat Penanggung Jawab')
                        ->reactive()
                        ->required(),
                    TextInput::make('status_poli')
                        ->label('Status Poli')
                        ->reactive()
                        ->required(),
                    Select::make('kd_pj')
                        ->label('Jenis Bayar')
                        ->options(Penjab::where('status', '1')->pluck('png_jawab', 'kd_pj'))
                        ->searchable()
                        ->required(),
                    // Select::make('kd_poli')
                    //     ->label('Poliklinik')
                    //     ->options(Poliklinik::where('status', '1')->pluck('nm_poli', 'kd_poli'))
                    //     ->searchable()
                    //     ->required(),
                    Select::make('kd_dokter')
                        ->label('Dokter')
                        ->options(Dokter::where('status', '1')->pluck('nm_dokter', 'kd_dokter'))
                        ->searchable()
                        ->required(),
                ])
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(RegPeriksa::query()->where('kd_poli', env('IGD', 'IGDK')))
            ->defaultSort('tgl_registrasi', 'desc')
            ->filters([
                SelectFilter::make('kd_poli')
                    ->options(Poliklinik::where('status', '1')->pluck('nm_poli', 'kd_poli'))
                    ->label('Poliklinik')
                    ->placeholder('Pilih Poliklinik'),
                SelectFilter::make('kd_dokter')
                    ->options(Dokter::where('status', '1')->pluck('nm_dokter', 'kd_dokter'))
                    ->label('Dokter')
                    ->placeholder('Pilih Dokter'),
                SelectFilter::make('kd_pj')
                    ->options(Penjab::where('status', '1')->pluck('png_jawab', 'kd_pj'))
                    ->label('Jenis Bayar')
                    ->placeholder('Pilih Jenis Bayar')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\CreateAction::make('kamar_inap')
                        ->label('Kamar Inap')
                        ->icon('heroicon-o-plus-circle')
                        ->modalHeading('Kamar Inap')
                        ->action(function (array $data, RegPeriksa $regPeriksa): void {
                            try {
                                $kamar = \App\Models\Kamar::where('kd_kamar', $data['kd_kamar'])->first();
                                $kamar->status = 'ISI';
                                $kamar->save();
                                $regPeriksa->status_lanjut = 'Ranap';
                                $regPeriksa->save();
                                $data['no_rawat'] = $regPeriksa->no_rawat;
                                $data['tgl_masuk'] = date('Y-m-d');
                                $data['jam_masuk'] = date('H:i:s');
                                $data['tgl_keluar'] = '0000-00-00';
                                $data['jam_keluar'] = '00:00:00';
                                $data['lama'] = 1;
                                $data['diagnosa_akhir'] = '-';
                                $data['trf_kamar'] = $kamar->trf_kamar;
                                $data['ttl_biaya'] = $kamar->trf_kamar;
                                \App\Models\KamarInap::create($data);
                                Notification::make()
                                    ->title('Pasien Berhasil Diinapkan')
                                    ->success()
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('success')
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Menginapkan Pasien')
                                    ->body($e->getMessage())
                                    ->danger()
                                    // ->icon('heroicon-o-exclamation')
                                    // ->iconColor('error')
                                    ->send();
                            }
                        })
                        ->form([
                            Select::make('kd_kamar')
                                ->label('Kamar')
                                ->options(\App\Models\Kamar::where('status', 'KOSONG')->pluck('kd_kamar', 'kd_kamar'))
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $kamar = \App\Models\Kamar::where('kd_kamar', $state)->first();
                                    // dd($kamar->trf_kamar);
                                    $set('ttl_biaya', $kamar->trf_kamar ?? 0);
                                })
                                ->required(),
                            TextInput::make('diagnosa_awal')
                                ->label('Diagnosa Awal')
                                ->required(),
                            TextInput::make('ttl_biaya')
                                ->label('Total Biaya')
                                ->reactive()
                                ->required(),
                        ]),
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->modalHeading('Edit Pendafataran')
                        ->form([
                            Select::make('kd_pj')
                                ->label('Jenis Bayar')
                                ->options(Penjab::where('status', '1')->pluck('png_jawab', 'kd_pj'))
                                ->searchable()
                                ->required(),
                            Select::make('kd_poli')
                                ->label('Poliklinik')
                                ->options(Poliklinik::where('status', '1')->pluck('nm_poli', 'kd_poli'))
                                ->searchable()
                                ->required(),
                            Select::make('kd_dokter')
                                ->label('Dokter')
                                ->options(Dokter::where('status', '1')->pluck('nm_dokter', 'kd_dokter'))
                                ->searchable()
                                ->required(),
                        ])
                ])
            ], position: ActionsPosition::BeforeColumns)
            ->columns([
                TextColumn::make('no_reg')
                    ->label('No. Reg')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_rkm_medis')
                    ->label('No. Rekam Medis')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tgl_registrasi')
                    ->label('Tgl Registrasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('poliklinik.nm_poli')
                    ->label('Poliklinik')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('penjab.png_jawab')
                    ->label('Penanggung Jawab')
                    ->searchable()
                    ->sortable(),
            ]);
    }
}
