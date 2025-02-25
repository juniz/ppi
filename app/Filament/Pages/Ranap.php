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
                            $data = \App\Models\DataHais::where('no_rawat', $regPeriksa->no_rawat)->where('tanggal', date('Y-m-d'))->first();
                            if ($data) {
                                $form->fill($data->toArray());
                            } else {
                                $form->fill([
                                    'tanggal' => now(),
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
                    Tables\Actions\EditAction::make('audit_bundle_iadp')
                        ->label('Input Bundle IADP')
                        ->modalHeading('Audit Bundle IADP')
                        ->mountUsing(function (Form $form, RegPeriksa $regPeriksa) {
                            $data = \App\Models\AuditBundleIadp::where('no_rawat', $regPeriksa->no_rawat)->where('tanggal', date('Y-m-d'))->first();
                            if ($data) {
                                $form->fill($data->toArray());
                            } else {
                                $form->fill([
                                    'tanggal' => now(),
                                    'nik' => '',
                                    'handhygiene' => 'Ya',
                                    'apd' => 'Ya',
                                    'skin_antiseptik' => 'Ya',
                                    'lokasi_iv' => 'Ya',
                                    'perawatan_rutin' => 'Ya',
                                ]);
                            }
                        })
                        ->action(function (array $data, RegPeriksa $regPeriksa) {
                            try {
                                $data['no_rawat'] = $regPeriksa->no_rawat;
                                \App\Models\AuditBundleIadp::updateOrCreate([
                                    'no_rawat' => $regPeriksa->no_rawat,
                                    'tanggal' => date('Y-m-d'),
                                ], $data);

                                Notification::make()
                                    ->title('Data Audit Bundle IADP berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Data Audit Bundle IADP gagal disimpan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->form([
                            Forms\Components\Select::make('nik')
                                ->options(\App\Models\Pegawai::pluck('nama', 'nik')->toArray())
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->nama)
                                ->searchable(['nama', 'nik'])
                                ->required(),
                            Forms\Components\Select::make('handhygiene')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('apd')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('skin_antiseptik')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('lokasi_iv')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('perawatan_rutin')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak'
                                ])
                                ->default('Ya')
                                ->required(),
                        ]),
                    Tables\Actions\EditAction::make('audit_bundle_ido')
                        ->label('Input Bundle IDO')
                        ->modalHeading('Audit Bundle IDO')
                        ->mountUsing(function (Form $form, RegPeriksa $regPeriksa) {
                            $data = \App\Models\AuditBundleIdo::where('no_rawat', $regPeriksa->no_rawat)->where('tanggal', date('Y-m-d'))->first();
                            if ($data) {
                                $form->fill($data->toArray());
                            } else {
                                $form->fill([
                                    'tanggal' => now(),
                                    'id_ruang' => '',
                                    'pencukuran_rambut' => 'Ya',
                                    'antibiotik' => 'Ya',
                                    'temperature' => 'Ya',
                                    'sugar' => 'Ya',
                                ]);
                            }
                        })
                        ->action(function (array $data, RegPeriksa $regPeriksa) {
                            try {
                                $data['no_rawat'] = $regPeriksa->no_rawat;
                                \App\Models\AuditBundleIdo::updateOrCreate([
                                    'no_rawat' => $regPeriksa->no_rawat,
                                    'tanggal' => date('Y-m-d'),
                                ], $data);

                                Notification::make()
                                    ->title('Data Audit Bundle IDO berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Data Audit Bundle IDO gagal disimpan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\App\Models\RuangAuditKepatuhan::pluck('nama_ruang', 'id_ruang')->toArray())
                                ->required(),
                            Forms\Components\Select::make('pencukuran_rambut')
                                ->label('Pencukuran Rambut')
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
                                ->label('Temperature')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                            Forms\Components\Select::make('sugar')
                                ->label('Sugar')
                                ->options([
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ])
                                ->default('Ya')
                                ->required(),
                        ]),
                    Tables\Actions\EditAction::make('audit_bundle_isk')
                        ->label('Input Bundle ISK')
                        ->modalHeading('Audit Bundle ISK')
                        ->mountUsing(function (Form $form, RegPeriksa $regPeriksa) {
                            $data = \App\Models\AuditBundleIsk::where('no_rawat', $regPeriksa->no_rawat)->where('tanggal', date('Y-m-d'))->first();
                            if ($data) {
                                $form->fill($data->toArray());
                            } else {
                                $form->fill([
                                    'tanggal' => now(),
                                    'id_ruang' => '',
                                    'pemasangan_sesuai_indikasi' => 'Ya',
                                    'hand_hygiene' => 'Ya',
                                    'menggunakan_apd_yang_tepat' => 'Ya',
                                    'pemasangan_menggunakan_alat_steril' => 'Ya',
                                    'segera_dilepas_setelah_tidak_diperlukan' => 'Ya',
                                    'pengisian_balon_sesuai_petunjuk' => 'Ya',
                                    'fiksasi_kateter_dengan_plester' => 'Ya',
                                    'urinebag_menggantung_tidak_menyentuh_lantai' => 'Ya',
                                ]);
                            }
                        })
                        ->action(function (array $data, RegPeriksa $regPeriksa) {
                            try {
                                $data['no_rawat'] = $regPeriksa->no_rawat;
                                \App\Models\AuditBundleIsk::updateOrCreate([
                                    'no_rawat' => $regPeriksa->no_rawat,
                                    'tanggal' => date('Y-m-d'),
                                ], $data);

                                Notification::make()
                                    ->title('Data Audit Bundle ISK berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Data Audit Bundle ISK gagal disimpan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\App\Models\RuangAuditKepatuhan::pluck('nama_ruang', 'id_ruang')->toArray())
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
                        ]),
                    Tables\Actions\EditAction::make('audit_bundle_vap')
                        ->label('Input Bundle VAP')
                        ->modalHeading('Audit Bundle VAP')
                        ->mountUsing(function (Form $form, RegPeriksa $regPeriksa) {
                            $data = \App\Models\AuditBundleVap::where('no_rawat', $regPeriksa->no_rawat)->where('tanggal', date('Y-m-d'))->first();
                            if ($data) {
                                $form->fill($data->toArray());
                            } else {
                                $form->fill([
                                    'tanggal' => now(),
                                    'id_ruang' => '',
                                    'posisi_kepala' => 'Ya',
                                    'pengkajian_setiap_hari' => 'Ya',
                                    'hand_hygiene' => 'Ya',
                                    'oral_hygiene' => 'Ya',
                                    'suction_manajemen_sekresi' => 'Ya',
                                    'profilaksis_peptic_ulcer' => 'Ya',
                                    'dvt_profiklasisi' => 'Ya',
                                    'penggunaan_apd_sesuai' => 'Ya',
                                ]);
                            }
                        })
                        ->action(function (array $data, RegPeriksa $regPeriksa) {
                            try {
                                $data['no_rawat'] = $regPeriksa->no_rawat;
                                \App\Models\AuditBundleVap::updateOrCreate([
                                    'no_rawat' => $regPeriksa->no_rawat,
                                    'tanggal' => date('Y-m-d'),
                                ], $data);

                                Notification::make()
                                    ->title('Data Audit Bundle VAP berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Data Audit Bundle VAP gagal disimpan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\App\Models\RuangAuditKepatuhan::pluck('nama_ruang', 'id_ruang')->toArray())
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
                        ]),
                    Tables\Actions\EditAction::make('audit_bundle_plabsi')
                        ->label('Input Bundle Plabsi')
                        ->modalHeading('Audit Bundle Plabsi')
                        ->mountUsing(function (Form $form, RegPeriksa $regPeriksa) {
                            $data = \App\Models\AuditBundlePlabsi::where('no_rawat', $regPeriksa->no_rawat)->where('tanggal', date('Y-m-d'))->first();
                            if ($data) {
                                $form->fill($data->toArray());
                            } else {
                                $form->fill([
                                    'tanggal' => now(),
                                    'id_ruang' => '',
                                    'sebelum_melakukan_hand_hygiene' => 'Ya',
                                    'menggunakan_apd_lengkap' => 'Ya',
                                    'lokasi_pemasangan_sesuai' => 'Ya',
                                    'alat_yang_digunakan_steril' => 'Ya',
                                    'pembersihan_kulit' => 'Ya',
                                    'setelah_melakukan_hand_hygiene' => 'Ya',
                                    'perawatan_dressing_infus' => 'Ya',
                                    'spoit_yang_digunakan_disposible' => 'Ya',
                                    'memberi_tanggal_dan_jam_pemasangan_infus' => 'Ya',
                                    'set_infus_setiap_72jam' => 'Ya',
                                ]);
                            }
                        })
                        ->action(function (array $data, RegPeriksa $regPeriksa) {
                            try {
                                $data['no_rawat'] = $regPeriksa->no_rawat;
                                \App\Models\AuditBundlePlabsi::updateOrCreate([
                                    'no_rawat' => $regPeriksa->no_rawat,
                                    'tanggal' => date('Y-m-d'),
                                ], $data);

                                Notification::make()
                                    ->title('Data Audit Bundle Plabsi berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Data Audit Bundle Plabsi gagal disimpan')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->form([
                            Forms\Components\Select::make('id_ruang')
                                ->label('Ruang')
                                ->options(\App\Models\RuangAuditKepatuhan::pluck('nama_ruang', 'id_ruang')->toArray())
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
                        ])
                ])

            ], position: ActionsPosition::BeforeColumns);
    }
}
