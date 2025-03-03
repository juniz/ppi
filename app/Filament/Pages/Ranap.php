<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Penjab;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\RegPeriksa;
use App\Models\Dokter;
use App\Models\Poliklinik;
use Filament\Forms\Components\Select;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Actions;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AuditBundleIadp;
use Filament\Tables\Actions\Action;

class Ranap extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Data Pasien';
    protected static ?int $navigationSort = -1;
    protected static ?string $navigationLabel = 'Rawat Inap';

    protected static string $view = 'filament.pages.ranap';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RegPeriksa::query()
                    ->join('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
                    ->with(['pasien', 'poliklinik', 'penjab', 'kamarInap.kamar'])
                    ->where('status_lanjut', 'Ranap')
            )
            ->defaultSort('tgl_registrasi', 'desc')
            ->filters([
                // Menonaktifkan filter tanggal registrasi dengan komentar
                /*DateRangeFilter::make('tgl_registrasi')
                    ->label('Tanggal Registrasi')
                    ->startDate(Carbon::now())
                    ->endDate(Carbon::now())
                    ->modifyQueryUsing(
                        fn(Builder $query, ?Carbon $startDate, ?Carbon $endDate, $dateString) =>
                        $query->when(
                            !empty($dateString),
                            fn(Builder $query, $date): Builder =>
                            $query->whereBetween('tgl_registrasi', [$startDate, $endDate])
                        )
                    )
                    ->autoApply(),*/
                SelectFilter::make('kd_kamar')
                    ->label('Kamar')
                    // ->default(auth()->user()->kamar ?? '')
                    ->options(\App\Models\Kamar::join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')->pluck('nm_bangsal', 'kamar.kd_kamar'))
                    ->placeholder('Pilih Kamar'),
                SelectFilter::make('stts_pulang')
                    ->label('Status Pulang')
                    ->options([
                        '-' => '-',
                        'Sehat' => 'Sehat',
                        'Rujuk' => 'Rujuk',
                        'APS' => 'APS',
                        '+' => '+',
                        'Meninggal' => 'Meninggal',
                        'Membaik' => 'Membaik',
                        'Pulang Paksa' => 'Pulang Paksa',
                        'Status Belum Lengkap' => 'Status Belum Lengkap',
                        'Atas Persetujuan Dokter' => 'Atas Persetujuan Dokter',
                        'Atas Permintaan Sendiri' => 'Atas Permintaan Sendiri',
                        'Isoman' => 'Isoman',
                        'Lain-lain' => 'Lain-lain'
                    ])
                    ->default('-')
                    ->placeholder('Pilih Status Pulang'),
            ])
            ->columns([
                TextColumn::make('no_rkm_medis')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kamarInap.kamar.bangsal.nm_bangsal')
                    ->label('Kamar')
                    ->sortable(),
                TextColumn::make('kamarInap.tgl_masuk')
                    ->label('Tgl Masuk')
                    // ->date()
                    ->sortable(),
                TextColumn::make('kamarInap.tgl_keluar')
                    ->label('Tgl Keluar')
                    // ->date()
                    ->sortable(),
                TextColumn::make('kamarInap.stts_pulang')
                    ->label('Status Pulang')
                    ->sortable(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make('status')
                        ->label('Status Pasien')
                        ->modalHeading('Status Pulang Pasien')
                        ->action(function (array $data, RegPeriksa $regPeriksa) {
                            try {
                                DB::table('kamar_inap')
                                    ->where('no_rawat', $regPeriksa->no_rawat)
                                    ->update(['stts_pulang' => $data['stts_pulang']]);
                                Notification::make()
                                    ->title('Data berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                // dd($e->getMessage());
                                Notification::make()
                                    ->title('Data gagal disimpan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->form([
                            Select::make('stts_pulang')
                                ->label('Status')
                                ->options([
                                    '-' => '-',
                                    'Sehat' => 'Sehat',
                                    'Rujuk' => 'Rujuk',
                                    'APS' => 'APS',
                                    '+' => '+',
                                    'Meninggal' => 'Meninggal',
                                    'Membaik' => 'Membaik',
                                    'Pulang Paksa' => 'Pulang Paksa',
                                    'Status Belum Lengkap' => 'Status Belum Lengkap',
                                    'Atas Persetujuan Dokter' => 'Atas Persetujuan Dokter',
                                    'Atas Permintaan Sendiri' => 'Atas Permintaan Sendiri',
                                    'Isoman' => 'Isoman',
                                    'Lain-lain' => 'Lain-lain'
                                ])
                        ]),
                    Tables\Actions\EditAction::make('hais')
                        ->label('Data HAIs')
                        ->modalHeading('Data HAIs')
                        ->mountUsing(function (Form $form, RegPeriksa $regPeriksa) {
                            $data = \App\Models\DataHais::where('no_rawat', $regPeriksa->no_rawat)
                                ->whereDate('tanggal', date('Y-m-d'))
                                ->first();
                            
                            if ($data) {
                                $form->fill([
                                    ...$data->toArray(),
                                    'tanggal' => date('Y-m-d H:i:s'),
                                ]);
                            } else {
                                $form->fill([
                                    'tanggal' => date('Y-m-d H:i:s'),
                                    'DEKU' => 'TIDAK',
                                    'SPUTUM' => '',
                                    'DARAH' => '',
                                    'URINE' => '',
                                    'ETT' => 0,
                                    'CVL' => 0,
                                    'IVL' => 0,
                                    'UC' => 0,
                                    'VAP' => 0,
                                    'IAD' => 0,
                                    'PLEB' => 0,
                                    'ISK' => 0,
                                    'ILO' => 0,
                                    'HAP' => 0,
                                    'Tinea' => 0,
                                    'Scabies' => 0,
                                    'ANTIBIOTIK' => '',
                                ]);
                            }
                        })
                        ->action(function (array $data, RegPeriksa $regPeriksa) {
                            try {
                                $data['no_rawat'] = $regPeriksa->no_rawat;
                                $kamar = \App\Models\KamarInap::where('no_rawat', $regPeriksa->no_rawat)->first();
                                $data['kd_kamar'] = $kamar->kd_kamar;
                                \App\Models\DataHais::updateOrCreate([
                                    'no_rawat' => $regPeriksa->no_rawat,
                                    'tanggal' => $data['tanggal'],
                                ], $data);

                                Notification::make()
                                    ->title('Data HAIs berhasil disimpan')
                                    ->success()
                                    ->send();

                                // $this->redirect('/data-hais?tableSearch=' . $regPeriksa->no_rawat);
                            } catch (\Exception $e) {
                                // dd($e->getMessage());
                                Notification::make()
                                    ->title('Data Pasien Gagal Disimpan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->form([
                            Split::make([
                                Section::make([
                                    DatePicker::make('tanggal')
                                        ->label('Tanggal')
                                        ->default(now())
                                        ->required(),
                                    Select::make('DEKU')
                                        ->label('Dekubitus')
                                        ->options([
                                            'IYA' => 'IYA',
                                            'TIDAK' => 'TIDAK'
                                        ])
                                        ->default('Tidak')
                                        ->required(),
                                    TextInput::make('ANTIBIOTIK')
                                        ->label('Antibiotik')
                                        ->default('')
                                        ->required(),
                                    Section::make('Hari Pemasangan Alat')
                                        ->schema([
                                            Select::make('ETT')
                                                ->label('ETT')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya',
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('CVL')
                                                ->label('CVL')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya',
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('IVL')
                                                ->label('IVL')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya',
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('UC')
                                                ->label('UC')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya',
                                                ])
                                                ->default('0')
                                                ->required(),
                                        ])
                                        ->columns(4),
                                ]),
                                Section::make([
                                    Section::make('Infeksi RS')
                                        ->schema([
                                            Select::make('VAP')
                                                ->label('VAP')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('IAD')
                                                ->label('IAD')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('PLEB')
                                                ->label('PLEB')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('ISK')
                                                ->label('ISK')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('ILO')
                                                ->label('ILO')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('HAP')
                                                ->label('HAP')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('Tinea')
                                                ->label('Tinea')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                            Select::make('Scabies')
                                                ->label('Scabies')
                                                ->options([
                                                    '0' => 'Tidak',
                                                    '1' => 'Ya'
                                                ])
                                                ->default('0')
                                                ->required(),
                                        ])
                                        ->columns(4),
                                    Section::make('Kultur')
                                        ->schema([
                                            TextInput::make('SPUTUM')
                                                ->label('Sputum')
                                                ->default(''),
                                            TextInput::make('DARAH')
                                                ->label('Darah')
                                                ->default(''),
                                            TextInput::make('URINE')
                                                ->label('Urine')
                                                ->default(''),
                                        ]),
                                ]),
                            ])->from('md')
                        ])
                        ->modalWidth(MaxWidth::Full)
                        // ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Batal')
                        ->modalSubmitActionLabel('Simpan'),
                    Action::make('input_bundle_iadp')
                        ->label('Bundle IADP (Plebitis)')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->form([
                            Forms\Components\Select::make('nik')
                                ->label('Petugas / Pegawai')
                                ->options(\App\Models\Pegawai::pluck('nama', 'nik')->toArray())
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->nama)
                                ->searchable(['nama', 'nik'])
                                ->required(),
                            Forms\Components\Select::make('handhygiene')
                                ->label('Handhygiene')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('apd')
                                ->label('APD (Alat Pelindung Diri)')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('skin_antiseptik')
                                ->label('Skin Antiseptik')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('lokasi_iv')
                                ->label('Lokasi IV')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('perawatan_rutin')
                                ->label('Perawatan Rutin')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                        ]),
                    Action::make('input_bundle_ido')
                        ->label('Bundle IDO')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\DB::table('ruang_audit_kepatuhan')->pluck('nama_ruang', 'id_ruang'))
                                ->required(),
                            Forms\Components\Select::make('pencukuran_rambut')
                                ->label('Pencukuran Rambut Yang Mengganggu Jalanya Operasi')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('antibiotik')
                                ->label('Antibiotik')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('temperature')
                                ->label('Temperature (Suhu Pasien)')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('sugar')
                                ->label('Sugar (Gula Darah Pasien)')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                // Set data untuk disimpan
                                $data['tanggal'] = date('Y-m-d H:i:s');
                                $data['no_rawat'] = $record->no_rawat;
                                
                                \App\Models\AuditBundleIdo::create($data);
                                
                                Notification::make()
                                    ->title('Bundle IDO berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal menyimpan bundle IDO: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('input_bundle_isk')
                        ->label('Bundle ISK')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\DB::table('ruang_audit_kepatuhan')->pluck('nama_ruang', 'id_ruang'))
                                ->required(),
                            Forms\Components\Select::make('pemasangan_sesuai_indikasi')
                                ->label('1. Pemasangan Sesuai Indikasi')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('hand_hygiene')
                                ->label('2. Hand Hygiene')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('menggunakan_apd_yang_tepat')
                                ->label('3. Menggunakan APD yang Tepat')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('pemasangan_menggunakan_alat_steril')
                                ->label('4. Pemasangan Menggunakan Alat Steril')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('segera_dilepas_setelah_tidak_diperlukan')
                                ->label('5. Segera dilepas setelah tidak diperlukan')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('pengisian_balon_sesuai_petunjuk')
                                ->label('6. Pengisian Balon Sesuai Petunjuk (20 ml)')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('fiksasi_kateter_dengan_plester')
                                ->label('7. Fiksasi Kateter dengan Plester')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('urinebag_menggantung_tidak_menyentuh_lantai')
                                ->label('8. Urinebag Menggantung Tidak Menyentuh Lantai')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                // Set data untuk disimpan
                                $data['tanggal'] = date('Y-m-d H:i:s');
                                $data['no_rawat'] = $record->no_rawat;
                                
                                \App\Models\AuditBundleIsk::create($data);
                                
                                Notification::make()
                                    ->title('Bundle ISK berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal menyimpan bundle ISK: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('input_bundle_vap')
                        ->label('Bundle VAP')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\DB::table('ruang_audit_kepatuhan')->pluck('nama_ruang', 'id_ruang'))
                                ->required(),
                            Forms\Components\Select::make('posisi_kepala')
                                ->label('1. Posisi Kepala')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('pengkajian_setiap_hari')
                                ->label('2. Pengkajian Setiap Hari terhadap sedasi dan extubasi')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('hand_hygiene')
                                ->label('3. Hand Hygiene')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('oral_hygiene')
                                ->label('4. Oral Hygiene secara rutin')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('suction_manajemen_sekresi')
                                ->label('5. Suction / Manajemen Sekresi')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('profilaksis_peptic_ulcer')
                                ->label('6. Profilaksis Peptic Ulcer')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('dvt_profiklasisi')
                                ->label('7. DVT Profilaksisi jika ada indikasi')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('penggunaan_apd_sesuai')
                                ->label('8. Penggunaan APD Sesuai')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                $data['tanggal'] = date('Y-m-d H:i:s');
                                $data['no_rawat'] = $record->no_rawat;
                                
                                \App\Models\AuditBundleVap::create($data);
                                
                                Notification::make()
                                    ->title('Bundle VAP berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal menyimpan bundle VAP: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('input_bundle_plabsi')
                        ->label('Bundle CLABSI')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\DB::table('ruang_audit_kepatuhan')->pluck('nama_ruang', 'id_ruang'))
                                ->required(),
                            Forms\Components\Select::make('sebelum_melakukan_hand_hygiene')
                                ->label('1.Melakukan Hand Hygiene dan 5 Moment')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('menggunakan_apd_lengkap')
                                ->label('2. Menggunakan APD Lengkap dan Sarung tangan steril')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('lokasi_pemasangan_sesuai')
                                ->label('3. Lokasi Pemasangan Sesuai')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('alat_yang_digunakan_steril')
                                ->label('4. Alat Yang Digunakan Steril')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('pembersihan_kulit')
                                ->label('5. Pembersihan Kulit area pemasangan dengan chlorhexidine 2% atau alcohol 70%')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('setelah_melakukan_hand_hygiene')
                                ->label('6. Melakukan Hand Hygiene dan 5 Moment')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('perawatan_dressing_infus')
                                ->label('7. Perawatan Dressing Infus jika kotor atau basah')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('spoit_yang_digunakan_disposible')
                                ->label('8. Spoit Yang Digunakan Disposible')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('memberi_tanggal_dan_jam_pemasangan_infus')
                                ->label('9. Memberi Tanggal dan Jam Pemasangan Infus')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('set_infus_setiap_72jam')
                                ->label('10. Set Infus Setiap 72 Jam')
                                ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])
                                ->default('Ya')
                                ->required(),
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                $data['tanggal'] = date('Y-m-d H:i:s');
                                $data['no_rawat'] = $record->no_rawat;
                                
                                \App\Models\AuditBundlePlabsi::create($data);
                                
                                Notification::make()
                                    ->title('Bundle Plabsi berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal menyimpan bundle Plabsi: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])

            ], position: ActionsPosition::BeforeColumns);
    }
}
