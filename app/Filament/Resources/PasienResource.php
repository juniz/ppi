<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PasienResource\Pages;
use App\Filament\Resources\PasienResource\RelationManagers;
use App\Models\Pasien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard\Step;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use App\Models\RegPeriksa;
use App\Models\Kamar;
use App\Models\KamarInap;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Pasien';
    protected static ?int $navigationSort = -4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nm_pasien')
                    ->maxLength(40),
                Forms\Components\TextInput::make('no_ktp')
                    ->maxLength(20),
                Forms\Components\TextInput::make('jk'),
                Forms\Components\TextInput::make('tmp_lahir')
                    ->maxLength(15),
                Forms\Components\DatePicker::make('tgl_lahir'),
                Forms\Components\TextInput::make('nm_ibu')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('alamat')
                    ->maxLength(200),
                Forms\Components\TextInput::make('gol_darah'),
                Forms\Components\TextInput::make('pekerjaan')
                    ->maxLength(60),
                Forms\Components\TextInput::make('stts_nikah'),
                Forms\Components\TextInput::make('agama')
                    ->maxLength(12),
                Forms\Components\DatePicker::make('tgl_daftar'),
                Forms\Components\TextInput::make('no_tlp')
                    ->maxLength(40),
                Forms\Components\TextInput::make('umur')
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('pnd')
                    ->required(),
                Forms\Components\TextInput::make('keluarga'),
                Forms\Components\TextInput::make('namakeluarga')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('kd_pj')
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('no_peserta')
                    ->maxLength(25),
                Forms\Components\TextInput::make('kd_kel')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kd_kec')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kd_kab')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaanpj')
                    ->required()
                    ->maxLength(35),
                Forms\Components\TextInput::make('alamatpj')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('kelurahanpj')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('kecamatanpj')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('kabupatenpj')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('perusahaan_pasien')
                    ->required()
                    ->maxLength(8),
                Forms\Components\TextInput::make('suku_bangsa')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bahasa_pasien')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cacat_fisik')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('nip')
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('kd_prop')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('propinsipj')
                    ->required()
                    ->maxLength(30),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('no_rkm_medis', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('no_rkm_medis')
                    ->label('No Rekam Medis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('no_ktp')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('jk')
                    ->label('Jenis Kelamin'),
                Tables\Columns\TextColumn::make('tmp_lahir')
                    ->label('Tempat Lahir'),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->date()
                    ->label('Tanggal Lahir'),
                // Tables\Columns\TextColumn::make('nm_ibu')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('gol_darah'),
                // Tables\Columns\TextColumn::make('pekerjaan')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('stts_nikah'),
                // Tables\Columns\TextColumn::make('agama')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('tgl_daftar')
                //     ->date()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('no_tlp')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('umur')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('pnd'),
                // Tables\Columns\TextColumn::make('keluarga'),
                // Tables\Columns\TextColumn::make('namakeluarga')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('kd_pj')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('no_peserta')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('kelurahan.nm_kel')
                //     ->label('Kelurahan')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('kd_kec')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('kd_kab')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('pekerjaanpj')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('alamatpj')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('kelurahanpj')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('kecamatanpj')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('kabupatenpj')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('perusahaan_pasien')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('suku_bangsa')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('bahasa_pasien')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('cacat_fisik')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('nip')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('kd_prop')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('propinsipj')
                //     ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Rawat Jalan')
                        ->icon('heroicon-o-plus-circle')
                        ->color('warning')
                        ->modalHeading('Pasien Baru')
                        ->action(function (array $data): void {
                            try {
                                $pasien = \App\Models\Pasien::where('no_rkm_medis', $data['no_rkm_medis'])->first();
                                $cekStatus = \App\Models\RegPeriksa::where('no_rkm_medis', $data['no_rkm_medis'])->where('status_lanjut', 'Ralan')->first();
                                $tgl_lahir = Carbon::parse($pasien->tgl_lahir);
                                $data['umurdaftar'] = $tgl_lahir->diff(Carbon::now())->format('%y Th %m Bl %d Hr');
                                $data['no_reg'] = \App\Models\RegPeriksa::generateNoReg($data['kd_dokter'], $data['kd_poli']);
                                $data['no_rawat'] = \App\Models\RegPeriksa::generateNoRawat();
                                $data['tgl_registrasi'] = date('Y-m-d');
                                $data['jam_reg'] = date('H:i:s');
                                $data['status_lanjut'] = 'Ralan';
                                $data['stts'] = 'Belum';
                                $data['sttsumur'] = 'Th';
                                $data['biaya_reg'] = \App\Models\Poliklinik::where('kd_poli', $data['kd_poli'])->first()->registrasi;
                                $data['status_bayar'] = 'Belum Bayar';
                                $data['stts_daftar'] = $cekStatus ? 'Lama' : 'Baru';

                                DB::transaction(function () use ($data, $pasien) {
                                    \App\Models\RegPeriksa::create($data);
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
                                    // ->icon('heroicon-o-exclamation')
                                    // ->iconColor('error')
                                    ->send();
                            }
                        })
                        ->mountUsing(function (Form $form, Pasien $pasien) {
                            $cekStatus = \App\Models\RegPeriksa::where('no_rkm_medis', $pasien->no_rkm_medis)->where('status_lanjut', 'Ralan')->first();
                            $form->fill([
                                'no_rkm_medis' => $pasien->no_rkm_medis,
                                'p_jawab' => $pasien->namakeluarga ?? '',
                                'hubunganpj' => $pasien->namakeluarga ?? '',
                                'almt_pj' => $pasien->almt_pj ?? '',
                                'status_poli' => $cekStatus ? 'Lama' : 'Baru',
                            ]);
                        })
                        ->form([
                            TextInput::make('no_rkm_medis')
                                ->label('No Rekam Medis')
                                ->required(),
                            TextInput::make('status_poli')
                                ->label('Status Poli')
                                ->reactive()
                                ->required(),
                            Select::make('kd_pj')
                                ->label('Jenis Bayar')
                                ->options(\App\Models\Penjab::where('status', '1')->pluck('png_jawab', 'kd_pj'))
                                ->searchable()
                                ->required(),
                            Select::make('kd_poli')
                                ->label('Poliklinik')
                                ->options(\App\Models\Poliklinik::where('status', '1')->pluck('nm_poli', 'kd_poli'))
                                ->searchable()
                                ->required(),
                            Select::make('kd_dokter')
                                ->label('Dokter')
                                ->options(\App\Models\Dokter::where('status', '1')->pluck('nm_dokter', 'kd_dokter'))
                                ->searchable()
                                ->required(),
                        ]),
                    Tables\Actions\CreateAction::make('masuk_rawat_inap')
                        ->label('Masuk Rawat Inap')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('success')
                        ->modalHeading('Form Masuk Rawat Inap')
                        ->modalWidth('full')
                        ->stickyModalFooter()
                        ->form([
                            Grid::make(3)
                                ->schema([
                                    Section::make('Data Pasien')
                                        ->description('Informasi pasien yang akan dirawat')
                                        ->schema([
                                            TextInput::make('no_rkm_medis')
                                                ->label('No. Rekam Medis')
                                                ->default(fn (Pasien $record) => $record->no_rkm_medis)
                                                ->disabled(),
                                            TextInput::make('nm_pasien')
                                                ->label('Nama Pasien')
                                                ->default(fn (Pasien $record) => $record->nm_pasien)
                                                ->disabled(),
                                            TextInput::make('jk')
                                                ->label('Jenis Kelamin')
                                                ->default(fn (Pasien $record) => $record->jk)
                                                ->disabled(),
                                            TextInput::make('alamat')
                                                ->label('Alamat')
                                                ->default(fn (Pasien $record) => $record->alamat)
                                                ->disabled(),
                                            Grid::make(2)
                                                ->schema([
                                                    DatePicker::make('tgl_masuk')
                                                        ->label('Tanggal Masuk')
                                                        ->default(now())
                                                        ->required(),
                                                    TimePicker::make('jam_masuk')
                                                        ->label('Jam Masuk')
                                                        ->default(now())
                                                        ->seconds(false)
                                                        ->required(),
                                                ]),
                                        ])
                                        ->columnSpan(1),

                                    Section::make('Data Rawat Inap')
                                        ->description('Informasi kamar dan perawatan')
                                        ->schema([
                                            Hidden::make('status_poli')
                                                ->default('Baru'),
                                            Select::make('kd_pj')
                                                ->label('Jenis Bayar')
                                                ->options(\App\Models\Penjab::where('status', '1')->pluck('png_jawab', 'kd_pj'))
                                                ->searchable()
                                                ->required(),
                                            Select::make('kd_poli')
                                                ->label('Poliklinik')
                                                ->options(\App\Models\Poliklinik::where('status', '1')->pluck('nm_poli', 'kd_poli'))
                                                ->searchable()
                                                ->required(),
                                            Select::make('kd_dokter')
                                                ->label('Dokter')
                                                ->options(\App\Models\Dokter::where('status', '1')->pluck('nm_dokter', 'kd_dokter'))
                                                ->searchable()
                                                ->required(),
                                            Select::make('kd_kamar')
                                                ->label('Kamar')
                                                ->options(\App\Models\Kamar::query()
                                                    ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                                                    ->where('kamar.statusdata', '1')
                                                    ->where('bangsal.status', '1')
                                                    ->get()
                                                    ->mapWithKeys(function ($kamar) {
                                                        return [
                                                            $kamar->kd_kamar => $kamar->kd_kamar . ' - ' . $kamar->nm_bangsal
                                                        ];
                                                    }))
                                                ->searchable()
                                                ->required(),
                                            TextInput::make('diagnosa_awal')
                                                ->label('Diagnosa Awal')
                                                ->required(),
                                        ])
                                        ->columnSpan(2),
                                ])
                        ])
                        ->modalSubmitActionLabel('Simpan')
                        ->modalCancelActionLabel('Batal')
                        ->action(function (array $data, Pasien $record): void {
                            try {
                                DB::beginTransaction();
                                
                                // Generate no_rawat yang unik
                                $tanggal = date('Y/m/d');
                                $lastNoRawat = RegPeriksa::where('no_rawat', 'like', $tanggal . '/%')
                                    ->orderBy('no_rawat', 'desc')
                                    ->first();
                                    
                                if ($lastNoRawat) {
                                    $lastNumber = (int) substr($lastNoRawat->no_rawat, -6);
                                    $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                                } else {
                                    $newNumber = '000001';
                                }
                                
                                $no_rawat = $tanggal . '/' . $newNumber;
                                
                                // Generate no_reg yang unik untuk hari ini
                                $lastNoReg = RegPeriksa::where('tgl_registrasi', date('Y-m-d'))
                                    ->orderBy('no_reg', 'desc')
                                    ->first();
                                    
                                if ($lastNoReg) {
                                    $lastRegNumber = (int) substr($lastNoReg->no_reg, -3);
                                    $newRegNumber = str_pad($lastRegNumber + 1, 3, '0', STR_PAD_LEFT);
                                } else {
                                    $newRegNumber = '001';
                                }
                                
                                $no_reg = date('Ymd') . $newRegNumber;

                                // Buat reg_periksa terlebih dahulu
                                RegPeriksa::create([
                                    'no_rawat' => $no_rawat, // Gunakan no_rawat yang baru digenerate
                                    'no_reg' => $no_reg, // Gunakan no_reg yang baru digenerate
                                    'tgl_registrasi' => $data['tgl_masuk'],
                                    'jam_reg' => $data['jam_masuk'],
                                    'kd_dokter' => $data['kd_dokter'],
                                    'no_rkm_medis' => $record->no_rkm_medis,
                                    'kd_poli' => $data['kd_poli'],
                                    'p_jawab' => $record->namakeluarga,
                                    'almt_pj' => $record->alamat,
                                    'hubunganpj' => $record->keluarga,
                                    'biaya_reg' => 0,
                                    'stts' => 'Belum',
                                    'stts_daftar' => '-',
                                    'status_lanjut' => 'Ranap',
                                    'kd_pj' => $data['kd_pj'],
                                    'umurdaftar' => $record->umur,
                                    'stts_pulang' => '-',
                                    'status_bayar' => 'Belum Bayar',
                                ]);

                                // Update status kamar
                                $kamar = Kamar::where('kd_kamar', $data['kd_kamar'])->first();
                                $kamar->status = 'ISI';
                                $kamar->save();

                                // Buat data kamar inap
                                KamarInap::create([
                                    'no_rawat' => $no_rawat, // Gunakan no_rawat yang baru digenerate
                                    'kd_kamar' => $data['kd_kamar'],
                                    'trf_kamar' => $kamar->trf_kamar,
                                    'diagnosa_awal' => $data['diagnosa_awal'],
                                    'diagnosa_akhir' => '-',
                                    'tgl_masuk' => $data['tgl_masuk'],
                                    'jam_masuk' => $data['jam_masuk'],
                                    'tgl_keluar' => null,
                                    'jam_keluar' => null,
                                    'stts_pulang' => '-',
                                    'lama' => 1,
                                    'ttl_biaya' => $kamar->trf_kamar,
                                ]);

                                DB::commit();

                                Notification::make()
                                    ->title('Pasien Berhasil Masuk Rawat Inap')
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                DB::rollBack();
                                
                                Notification::make()
                                    ->title('Gagal Menginapkan Pasien')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                    Tables\Actions\EditAction::make('ubah')
                        ->label('Ubah')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->form([
                            TextInput::make('no_rkm_medis')
                                ->label('No Rekam Medis')
                                ->required(),
                            TextInput::make('nm_pasien')
                                ->label('Nama Pasien')
                                ->required(),
                            Select::make('jk')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan'
                                ])
                                ->required(),
                            DatePicker::make('tgl_lahir')
                                ->label('Tanggal Lahir')
                                ->required(),
                            TextInput::make('umur')
                                ->label('Umur')
                                ->disabled(),
                            Textarea::make('alamat')
                                ->label('Alamat')
                                ->required(),
                        ])
                        ->modalHeading('Edit Pasien')
                        ->modalWidth('md')
                        ->modalSubmitActionLabel('Simpan')
                        ->modalCancelActionLabel('Batal'),
                ]),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePasiens::route('/'),
        ];
    }
}
