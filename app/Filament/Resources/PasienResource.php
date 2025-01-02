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

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Pasien';

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
                Tables\Actions\EditAction::make()
                    ->action(function (array $data): void {
                        try {
                            $data['no_rkm_medis'] = Pasien::generateNoRm();
                            $data['umur'] = Pasien::calculateAge($data['tgl_lahir']);
                            // Pasien::create($data);
                            $pasien = new Pasien();
                            $pasien->fill($data);
                            $pasien->save();
                            Notification::make()
                                ->title('Data Pasien Berhasil Disimpan')
                                ->success()
                                ->icon('heroicon-o-document-text')
                                ->iconColor('success')
                                ->send();
                        } catch (\Exception $e) {
                            // dd($e->getMessage());
                            Notification::make()
                                ->title('Data Pasien Gagal Disimpan')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->steps(
                        [
                            Step::make('informasi_pasien')
                                ->label('Pasien')
                                ->description('Informasi Pasien')
                                ->schema([
                                    // TextInput::make('no_rkm_medis')
                                    //     ->label('No Rekam Medis')
                                    //     ->maxLength(15)
                                    //     ->required()
                                    //     ->default(fn() => \App\Models\Pasien::generateNoRm())
                                    //     ->disabled()
                                    //     ->maxWidth('sm')
                                    //     ->columnSpanFull(),
                                    TextInput::make('nm_pasien')
                                        ->label('Nama Pasien')
                                        ->minLength(3)
                                        ->maxLength(50)
                                        ->string()
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Nama Pasien tidak boleh kosong',
                                            'string' => 'Nama Pasien harus berupa huruf',
                                            'max' => 'Nama Pasien tidak boleh lebih dari 50 karakter',
                                            'min' => 'Nama Pasien tidak boleh kurang dari 3 karakter',
                                        ]),
                                    TextInput::make('no_ktp')
                                        ->label('No KTP/SIM')
                                        ->maxLength(20),
                                    TextInput::make('no_peserta')
                                        ->label('No Peserta')
                                        ->maxLength(20),
                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email(),
                                    TextInput::make('no_tlp')
                                        ->label('No Telepon')
                                        ->maxLength(15),
                                    DatePicker::make('tgl_daftar')
                                        ->label('Pertama Daftar')
                                        ->default(now())
                                        ->date()
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Pertama Daftar tidak boleh kosong',
                                            'date' => 'Pertama Daftar tidak valid',
                                        ]),
                                    Select::make('jk')
                                        ->label('Jenis Kelamin')
                                        ->options([
                                            'L' => 'Laki-laki',
                                            'P' => 'Perempuan',
                                        ])
                                        ->required()
                                        ->in('L', 'P')
                                        ->validationMessages([
                                            'required' => 'Jenis Kelamin tidak boleh kosong',
                                            'in' => 'Jenis Kelamin tidak valid',
                                        ]),
                                    Select::make('gol_darah')
                                        ->label('Golongan Darah')
                                        ->options([
                                            '-' => '-',
                                            'A' => 'A',
                                            'B' => 'B',
                                            'AB' => 'AB',
                                            'O' => 'O',
                                        ])
                                        ->default('-')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Golongan Darah tidak boleh kosong',
                                        ]),
                                    Select::make('agama')
                                        ->label('Agama')
                                        ->options([
                                            'ISLAM' => 'ISLAM',
                                            'KRISTEN' => 'KRISTEN',
                                            'KATOLIK' => 'KATOLIK',
                                            'HINDU' => 'HINDU',
                                            'BUDHA' => 'BUDHA',
                                            'KONGHUCU' => 'KONGHUCU',
                                        ])
                                        ->default('ISLAM')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Agama tidak boleh kosong',
                                        ]),
                                    TextInput::make('tmp_lahir')
                                        ->label('Tempat Lahir')
                                        ->maxLength(15),
                                    DatePicker::make('tgl_lahir')
                                        ->label('Tanggal Lahir')
                                        ->default(now())
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $birthDate = \Carbon\Carbon::parse($state);
                                            $age = $birthDate->age;
                                            $month = $birthDate->month;
                                            $day = $birthDate->day;
                                            $set('umur', $age . ' Th ' . $month . ' Bl ' . $day . ' Hr');
                                        })
                                        ->required(),
                                    TextInput::make('umur')
                                        ->disabled()
                                        ->label('Umur')
                                        ->required()
                                        ->maxLength(30),
                                    TextInput::make('pekerjaan')
                                        ->label('Pekerjaan')
                                        ->maxLength(60),
                                    Select::make('perusahaan_pasien')
                                        ->label('Perusahaan')
                                        ->placeholder('Pilih Perusahaan ...')
                                        ->options(\App\Models\PerusahaanPasien::pluck('nama_perusahaan', 'kode_perusahaan')->toArray())
                                        ->default('-')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Perusahaan tidak boleh kosong',
                                        ]),
                                    Select::make('stts_nikah')
                                        ->label('Status Nikah')
                                        ->options([
                                            'BELUM MENIKAH' => 'BELUM MENIKAH',
                                            'MENIKAH' => 'MENIKAH',
                                            'JANDA' => 'JANDA',
                                            'DUDHA' => 'DUDHA',
                                        ])
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Status Nikah tidak boleh kosong',
                                        ])
                                        ->default('BELUM MENIKAH'),
                                    Select::make('kd_pj')
                                        ->label('Penjab')
                                        ->placeholder('Pilih Asuransi/Askes')
                                        ->options(\App\Models\Penjab::where('status', '1')->pluck('png_jawab', 'kd_pj')->toArray())
                                        ->default('-')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Penjab tidak boleh kosong',
                                        ]),
                                    // TextInput::make('nm_ibu')
                                    //     ->label('Nama Ibu')
                                    //     ->required()
                                    //     ->string()
                                    //     ->maxLength(50)
                                    //     ->validationMessages([
                                    //         'required' => 'Nama Ibu tidak boleh kosong',
                                    //         'string' => 'Nama Ibu harus berupa huruf',
                                    //         'max' => 'Nama Ibu tidak boleh lebih dari 50 karakter',
                                    //     ]),
                                    Select::make('suku_bangsa')
                                        ->label('Suku Bangsa')
                                        ->placeholder('Pilih Suku Bangsa')
                                        ->options(\App\Models\SukuBangsa::pluck('nama_suku_bangsa', 'id')->toArray())
                                        ->default('-')
                                        ->required(),
                                    Select::make('bahasa_pasien')
                                        ->label('Bahasa Dipakai')
                                        ->placeholder('Pilih Bahasa')
                                        ->options(\App\Models\BahasaPasien::pluck('nama_bahasa', 'id')->toArray())
                                        ->default('-')
                                        ->required(),
                                    Select::make('cacat_fisik')
                                        ->label('Cacat Fisik')
                                        ->placeholder('Pilih Cacat Fisik')
                                        ->options(\App\Models\CacatFisik::pluck('nama_cacat', 'id')->toArray())
                                        ->default('-')
                                        ->required(),
                                    Select::make('pnd')
                                        ->label('Pendidikan')
                                        ->options([
                                            '-' => '-',
                                            'TS' => 'TS',
                                            'TK' => 'TK',
                                            'SD' => 'SD',
                                            'SMP' => 'SMP',
                                            'SMA' => 'SMA',
                                            'SLTA/SEDERAJAT' => 'SLTA/SEDERAJAT',
                                            'D1' => 'D1',
                                            'D2' => 'D2',
                                            'D3' => 'D3',
                                            'S1' => 'S1',
                                            'S2' => 'S2',
                                            'S3' => 'S3',
                                        ])
                                        ->default('-')
                                        ->required(),
                                    // Select::make()
                                ])
                                ->columns(2),
                            Step::make('Keluarga')
                                ->description('Informasi Keluarga')
                                ->schema([
                                    TextInput::make('nm_ibu')
                                        ->label('Nama Ibu')
                                        ->required()
                                        ->maxLength(50),
                                    Select::make('keluarga')
                                        ->label('Keluarga')
                                        ->required()
                                        ->options([
                                            'AYAH' => 'AYAH',
                                            'IBU' => 'IBU',
                                            'SAUDARA' => 'SAUDARA',
                                            'SUAMI' => 'SUAMI',
                                            'ISTRI' => 'ISTRI',
                                            'ANAK' => 'ANAK',
                                            'DIRI SENDIRI' => 'DIRI SENDIRI',
                                            'LAIN-LAIN' => 'LAIN-LAIN',
                                        ])
                                        ->default('SAUDARA'),
                                    TextInput::make('namakeluarga')
                                        ->label('Nama Keluarga')
                                        ->required()
                                        ->maxLength(50),
                                    TextInput::make('pekerjaanpj')
                                        ->label('Pekerjaan Penanggung Jawab')
                                        ->maxLength(60),
                                ])
                                ->columns(1),
                            Step::make('alamat')
                                ->label('Alamat')
                                ->description('Alamat Pasien dan Keluarga')
                                ->schema([
                                    Textarea::make('alamat')
                                        ->label('Alamat Pasien')
                                        ->rows(4)
                                        ->maxLength(200)
                                        ->label('Alamat')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Alamat tidak boleh kosong',
                                        ])
                                        ->columnSpanFull(),
                                    Select::make('kd_prop')
                                        ->label('Propinsi')
                                        ->placeholder('Cari Propinsi ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Propinsi::where('nm_prop', 'like', '%' . $search . '%')->limit(10)->pluck('nm_prop', 'kd_prop'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Propinsi tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                    Select::make('kd_kab')
                                        ->label('Kabupaten')
                                        ->placeholder('Cari Kabupaten ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Kabupaten::where('nm_kab', 'like', '%' . $search . '%')->limit(10)->pluck('nm_kab', 'kd_kab'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Kabupaten tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                    Select::make('kd_kec')
                                        ->label('Kecamatan')
                                        ->placeholder('Cari Kecamatan ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Kecamatan::where('nm_kec', 'like', '%' . $search . '%')->limit(10)->pluck('nm_kec', 'kd_kec'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Kecamatan tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                    Select::make('kd_kel')
                                        ->label('Kelurahan')
                                        ->placeholder('Cari Kelurahan ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Kelurahan::where('nm_kel', 'like', '%' . $search . '%')->limit(10)->pluck('nm_kel', 'kd_kel'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Kelurahan tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                    TextArea::make('alamatpj')
                                        ->label('Alamat Penanggung Jawab')
                                        ->rows(4)
                                        ->maxLength(200)
                                        ->label('Alamat')
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Alamat Penanggung Jawab tidak boleh kosong',
                                        ])
                                        ->columnSpanFull(),
                                    Select::make('propinsipj')
                                        ->label('Propinsi Penanggung Jawab')
                                        ->placeholder('Cari Propinsi ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Propinsi::where('nm_prop', 'like', '%' . $search . '%')->limit(10)->pluck('nm_prop', 'kd_prop'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Propinsi Penanggung Jawab tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                    Select::make('kabupatenpj')
                                        ->label('Kabupaten Penanggung Jawab')
                                        ->placeholder('Cari Kabupaten ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Kabupaten::where('nm_kab', 'like', '%' . $search . '%')->limit(10)->pluck('nm_kab', 'kd_kab'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Kabupaten Penanggung Jawab tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                    Select::make('kecamatanpj')
                                        ->label('Kecamatan Penanggung Jawab')
                                        ->placeholder('Cari Kecamatan ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Kecamatan::where('nm_kec', 'like', '%' . $search . '%')->limit(10)->pluck('nm_kec', 'kd_kec'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Kecamatan Penanggung Jawab tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                    Select::make('kelurahanpj')
                                        ->label('Kelurahan Penanggung Jawab')
                                        ->placeholder('Cari Kelurahan ...')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => \App\Models\Kelurahan::where('nm_kel', 'like', '%' . $search . '%')->limit(10)->pluck('nm_kel', 'kd_kel'))
                                        ->required()
                                        ->validationMessages([
                                            'required' => 'Kelurahan Penanggung Jawab tidak boleh kosong',
                                        ])
                                        ->default('-'),
                                ])->columns(2),
                        ]
                    )
                    ->modalWidth(MaxWidth::Full)
                    ->modalCancelActionLabel('Batal')
                    ->modalSubmitActionLabel('Simpan'),
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
            'index' => Pages\ManagePasiens::route('/'),
        ];
    }
}
