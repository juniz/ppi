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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;

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
                    ->select('reg_periksa.*')
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
                        'Pindah Kamar' => 'Pindah Kamar',
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
                    ->multiple()
                    ->default(['Pindah Kamar', '-'])
                    ->placeholder('Pilih Status Pulang'),
            ])
            ->columns([
                TextColumn::make('no_rkm_medis')
                    ->label('No. RM')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('reg_periksa.no_rkm_medis', 'like', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('pasien', function ($q) use ($search) {
                            $q->where('nm_pasien', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('reg_periksa.no_rawat', 'like', "%{$search}%");
                    })
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
                IconColumn::make('has_hais_today')
                    ->label('HAIs')
                    ->alignCenter()
                    ->tooltip('Status pengisian HAIs hari ini')
                    ->boolean()
                    ->getStateUsing(function ($record): bool {
                        return \App\Models\DataHais::query()
                            ->where('no_rawat', $record->no_rawat)
                            ->whereDate('tanggal', now()->format('Y-m-d'))
                            ->exists();
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('input_hais')
                        ->label('Input HAIs')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->modalHeading(fn (RegPeriksa $record) => new HtmlString("
                            <div>
                                <h2 class='text-xl font-bold tracking-tight'>Input HAIs</h2>
                                <p class='mt-1 text-gray-600'>
                                    {$record->pasien->nm_pasien} (RM: {$record->no_rkm_medis})
                                </p>
                            </div>
                        "))
                        ->form([
                            Grid::make([
                                'default' => 1,    
                                'sm' => 2,         
                                'lg' => 4          // Ubah menjadi 4 kolom
                            ])
                            ->schema([
                                // Kolom 1: Data Umum
                                Section::make('Data Umum')
                                    ->schema([
                                        DatePicker::make('tanggal')
                                            ->label('Tanggal')
                                            ->default(now())
                                            ->required(),
                                        TextInput::make('ANTIBIOTIK')
                                            ->label('Antibiotik')
                                            ->default('-')
                                            ->required(),
                                        Select::make('DEKU')
                                            ->label('Dekubitus')
                                            ->options([
                                                'TIDAK' => 'TIDAK',
                                                'IYA' => 'IYA'
                                            ])
                                            ->default('TIDAK')
                                            ->required(),
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('ETT')
                                                    ->label('ETT')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('CVL')
                                                    ->label('CVL')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('IVL')
                                                    ->label('IVL')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('UC')
                                                    ->label('UC')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                            ]),
                                    ])
                                    ->columnSpan(['lg' => 1]),

                                // Kolom 2: Infeksi RS
                                Section::make('Infeksi RS')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('VAP')
                                                    ->label('VAP')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('IAD')
                                                    ->label('IAD')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('PLEB')
                                                    ->label('PLEB')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('ISK')
                                                    ->label('ISK')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('ILO')
                                                    ->label('ILO')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('HAP')
                                                    ->label('HAP')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('Tinea')
                                                    ->label('Tinea')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                                Select::make('Scabies')
                                                    ->label('Scabies')
                                                    ->options([
                                                        0 => 'TIDAK',
                                                        1 => 'YA'
                                                    ])
                                                    ->default(0),
                                            ]),
                                    ])
                                    ->columnSpan(['lg' => 1]),

                                // Kolom 3: Kultur
                                Section::make('Kultur')
                                    ->schema([
                                        TextInput::make('SPUTUM')
                                            ->label('Sputum'),
                                        TextInput::make('DARAH')
                                            ->label('Darah'),
                                        TextInput::make('URINE')
                                            ->label('Urine'),
                                    ])
                                    ->columnSpan(['lg' => 1]),

                                // Kolom 4: Status Pengisian HAIs
                                Section::make('Status Pengisian HAIs')
                                    ->schema([
                                        Placeholder::make('status_hais')
                                            ->content(function (RegPeriksa $record): HtmlString {
                                                $tglMasuk = Carbon::parse($record->kamarInap->tgl_masuk);
                                                $today = Carbon::today();
                                                
                                                // Hitung total hari
                                                $totalDays = $tglMasuk->diffInDays($today) + 1;
                                                
                                                $html = '<div class="space-y-2">';
                                                
                                                for ($date = clone $tglMasuk; $date->lte($today); $date->addDay()) {
                                                    $isDataExist = \App\Models\DataHais::query()
                                                        ->where('no_rawat', $record->no_rawat)
                                                        ->whereDate('tanggal', $date->format('Y-m-d'))
                                                        ->exists();
                                                    
                                                    if ($isDataExist) {
                                                        $icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color: #10b981;">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>';
                                                        $textColor = 'text-success-600';
                                                    } else {
                                                        $icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color: #ef4444;">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>';
                                                        $textColor = 'text-danger-600';
                                                    }
                                                    
                                                    $html .= '<div class="flex items-center gap-2">
                                                        ' . $icon . '
                                                        <span class="' . $textColor . '">' . $date->format('d/m/Y') . '</span>
                                                    </div>';
                                                }
                                                
                                                $html .= '</div>';
                                                
                                                return new HtmlString($html);
                                            }),
                                    ])
                                    ->columnSpan(['lg' => 1]),
                            ])
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                // Tambahkan no_rawat dan kd_kamar
                                $data['no_rawat'] = $record->no_rawat;
                                $data['kd_kamar'] = $record->kamarInap->kd_kamar;
                                
                                \App\Models\DataHais::create($data);
                                
                                Notification::make()
                                    ->title('Data HAIs berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal menyimpan data HAIs: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->modalWidth(MaxWidth::SevenExtraLarge)
                        ->modalSubmitActionLabel('Simpan')
                        ->modalCancelActionLabel('Batal'),
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
                                ->label('1. Posisi Kepala 30 derajat s/d 45 derajat')
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
                            
                            Forms\Components\Section::make('Saat Pemasangan')
                                ->description('Tahap 1-5 dilakukan saat pemasangan')
                                ->schema([
                                    Forms\Components\Select::make('sebelum_melakukan_hand_hygiene')
                                        ->label('1. Melakukan Hand Hygiene dan 5 Moment')
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
                                ]),

                            Forms\Components\Section::make('Penggantian/Perawatan Peralatan')
                                ->description('Tahap 6-10 dilakukan saat perawatan')
                                ->schema([
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
                                ]),
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                $data['tanggal'] = date('Y-m-d H:i:s');
                                $data['no_rawat'] = $record->no_rawat;
                                
                                \App\Models\AuditBundlePlabsi::create($data);
                                
                                Notification::make()
                                    ->title('Bundle CLABSI berhasil disimpan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Gagal menyimpan bundle CLABSI: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('pindah_kamar')
                        ->label('Pindah Kamar')
                        ->icon('heroicon-o-arrows-right-left')
                        ->form([
                            Section::make('Informasi Pasien')
                                ->schema([
                                    TextInput::make('no_rkm_medis')
                                        ->label('No. RM')
                                        ->default(fn (RegPeriksa $record) => $record->no_rkm_medis)
                                        ->disabled(),
                                    TextInput::make('nm_pasien')
                                        ->label('Nama Pasien')
                                        ->default(fn (RegPeriksa $record) => $record->pasien->nm_pasien)
                                        ->disabled(),
                                ])
                                ->columns(2),
                                
                            Section::make('Pindah Kamar')
                                ->schema([
                                    Select::make('kd_kamar')
                                        ->label('Pilih Kamar')
                                        ->options(function () {
                                            return \App\Models\Kamar::query()
                                                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                                                ->pluck('bangsal.nm_bangsal', 'kamar.kd_kamar')
                                                ->toArray();
                                        })
                                        ->required()
                                ]),
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                DB::table('kamar_inap')
                                    ->where('no_rawat', $record->no_rawat)
                                    ->update([
                                        'kd_kamar' => $data['kd_kamar'],
                                        'stts_pulang' => 'Pindah Kamar'
                                    ]);

                                Notification::make()
                                    ->title('Berhasil pindah kamar')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal pindah kamar')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->modalHeading('Pindah Kamar')
                        ->modalSubmitActionLabel('Simpan')
                        ->modalCancelActionLabel('Batal'),
                    Action::make('pemulangan_pasien')
                        ->label('Pemulangan Pasien')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->modalWidth(MaxWidth::SevenExtraLarge)
                        ->modalHeading(fn (RegPeriksa $record): string => "Pemulangan Pasien: {$record->pasien->nm_pasien} ({$record->no_rawat})")
                        ->form([
                            Split::make([
                                // Kolom Kiri
                                Grid::make()
                                    ->schema([
                                        Section::make('Informasi Pasien')
                                            ->schema([
                                                TextInput::make('no_rawat')
                                                    ->label('No Rawat')
                                                    ->default(fn ($record) => $record->no_rawat)
                                                    ->disabled(),
                                                TextInput::make('nm_pasien')
                                                    ->label('Nama Pasien')
                                                    ->default(fn ($record) => $record->pasien->nm_pasien)
                                                    ->disabled(),
                                            ])
                                            ->columns(2),

                                        Section::make('Data Pemulangan')
                                            ->schema([
                                                DateTimePicker::make('tgl_keluar')
                                                    ->label('Tanggal & Jam Keluar')
                                                    ->default(now())
                                                    ->required(),
                                                Select::make('stts_pulang')
                                                    ->label('Status Pulang')
                                                    ->options([
                                                        'Sehat' => 'Sehat',
                                                        'Rujuk' => 'Rujuk',
                                                        'APS' => 'APS',
                                                        '+' => '+',
                                                        'Meninggal' => 'Meninggal',
                                                        'Sembuh' => 'Sembuh',
                                                        'Membaik' => 'Membaik',
                                                        'Pulang Paksa' => 'Pulang Paksa',
                                                        '-' => '-',
                                                        'Pindah Kamar' => 'Pindah Kamar',
                                                        'Status Belum Lengkap' => 'Status Belum Lengkap',
                                                        'Atas Persetujuan Dokter' => 'Atas Persetujuan Dokter',
                                                        'Atas Permintaan Sendiri' => 'Atas Permintaan Sendiri',
                                                        'Isoman' => 'Isoman',
                                                        'Lain-lain' => 'Lain-lain'
                                                    ])
                                                    ->required(),
                                            ])
                                            ->columns(2),
                                    ])
                                    ->columnSpan(['lg' => 1]),

                                // Kolom Kanan
                                Section::make('Status Pengisian HAIs')
                                    ->schema([
                                        Placeholder::make('status_hais')
                                            ->content(function (RegPeriksa $record): HtmlString {
                                                $tglMasuk = Carbon::parse($record->kamarInap->tgl_masuk);
                                                $today = Carbon::today();
                                                
                                                // Hitung total hari
                                                $totalDays = $tglMasuk->diffInDays($today) + 1;
                                                // Hitung jumlah kolom yang dibutuhkan (8 item per kolom)
                                                $columns = ceil($totalDays / 8);
                                                
                                                $html = '<div class="grid grid-cols-' . $columns . ' gap-4">';
                                                
                                                // Array untuk menampung item per kolom
                                                $columnItems = array_fill(0, $columns, '');
                                                $currentColumn = 0;
                                                $itemsInCurrentColumn = 0;
                                                
                                                for ($date = clone $tglMasuk; $date->lte($today); $date->addDay()) {
                                                    $isDataExist = \App\Models\DataHais::query()
                                                        ->where('no_rawat', $record->no_rawat)
                                                        ->whereDate('tanggal', $date->format('Y-m-d'))
                                                        ->exists();
                                                    
                                                    if ($isDataExist) {
                                                        $icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color: #10b981;">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>';
                                                        $textColor = 'text-success-600';
                                                    } else {
                                                        $icon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color: #ef4444;">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>';
                                                        $textColor = 'text-danger-600';
                                                    }
                                                    
                                                    $item = '<div class="flex items-center gap-2">
                                                        ' . $icon . '
                                                        <span class="' . $textColor . '">' . $date->format('d/m/Y') . '</span>
                                                    </div>';
                                                    
                                                    // Tambahkan item ke kolom saat ini
                                                    $columnItems[$currentColumn] .= $item;
                                                    $itemsInCurrentColumn++;
                                                    
                                                    // Pindah ke kolom berikutnya jika sudah mencapai 8 item
                                                    if ($itemsInCurrentColumn >= 8) {
                                                        $currentColumn++;
                                                        $itemsInCurrentColumn = 0;
                                                    }
                                                }
                                                
                                                // Gabungkan semua kolom
                                                foreach ($columnItems as $items) {
                                                    $html .= '<div class="space-y-2">' . $items . '</div>';
                                                }
                                                
                                                $html .= '</div>';
                                                
                                                return new HtmlString($html);
                                            }),
                                    ])
                                    ->columnSpan(['lg' => 1]),
                            ])->columns(['lg' => 2]),
                        ])
                        ->action(function (array $data, RegPeriksa $record): void {
                            try {
                                DB::table('kamar_inap')
                                    ->where('no_rawat', $record->no_rawat)
                                    ->update([
                                        'tgl_keluar' => Carbon::parse($data['tgl_keluar'])->format('Y-m-d'),
                                        'jam_keluar' => Carbon::parse($data['tgl_keluar'])->format('H:i:s'),
                                        'stts_pulang' => $data['stts_pulang']
                                    ]);

                                Notification::make()
                                    ->title('Berhasil memulangkan pasien')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal memulangkan pasien')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
            ], position: ActionsPosition::BeforeColumns);
    }
}
