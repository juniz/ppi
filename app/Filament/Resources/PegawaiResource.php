<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->required(),
                Forms\Components\TextInput::make('nama')
                    ->required(),
                Forms\Components\Select::make('jk')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Pria' => 'Laki-laki',
                        'Wanita' => 'Perempuan',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jbtn')
                    ->label('Jabatan')
                    ->required(),
                Forms\Components\Select::make('jnj_jabatan')
                    ->label('Jenjang Jabatan')
                    ->relationship('jnjJabatan', 'nama')
                    ->default('-')
                    ->required(),
                Forms\Components\Select::make('kode_kelompok')
                    ->label('Kelompok Jabatan')
                    ->relationship('kelompokJabatan', 'nama_kelompok')
                    ->default('-')
                    ->required(),
                Forms\Components\Select::make('kode_resiko')
                    ->label('Resiko Kerja')
                    ->relationship('resikoKerja', 'nama_resiko')
                    ->default('-')
                    ->required(),
                Forms\Components\Select::make('kode_emergency')
                    ->label('Tingkat Emergency')
                    ->relationship('emergencyIndex', 'nama_emergency')
                    ->default('-')
                    ->required(),
                Forms\Components\Select::make('departemen')
                    ->relationship('getDepartemen', 'nama')
                    ->default('-')
                    ->required(),
                Forms\Components\Select::make('bidang')
                    ->relationship('getBidang', 'nama')
                    ->default('-')
                    ->required(),
                Forms\Components\Select::make('stts_wp')
                    ->label('Status WP')
                    ->relationship('sttsWp', 'ktg')
                    ->default('-')
                    ->required(),
                Forms\Components\Select::make('stts_kerja')
                    ->label('Status Kerja')
                    ->relationship('sttsKerja', 'ktg')
                    ->default('-')
                    ->required(),
                Forms\Components\TextInput::make('npwp')
                    ->label('NPWP')
                    ->required()
                    ->maxLength(15),
                Forms\Components\Select::make('pendidikan')
                    ->relationship('getPendidikan', 'tingkat')
                    ->default('-')
                    ->required(),
                // Forms\Components\TextInput::make('gapok')
                //     ->required()
                //     ->numeric(),
                Forms\Components\TextInput::make('tmp_lahir')
                    ->label('Tempat Lahir')
                    ->required()
                    ->maxLength(20),
                Forms\Components\DatePicker::make('tgl_lahir')
                    ->label('Tanggal Lahir')
                    ->required(),
                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('kota')
                    ->required()
                    ->maxLength(20),
                Forms\Components\DatePicker::make('mulai_kerja')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('ms_kerja')
                    ->label('Masa Kerja')
                    ->options([
                        '<1' => '<1',
                        'PT' => 'PT',
                        'FT>1' => 'FT>1',
                    ])
                    ->default('<1')
                    ->required(),
                Forms\Components\Select::make('indexins')
                    ->label('Kode Index')
                    ->options(\App\Models\Indexins::select('dep_id', DB::raw('CONCAT(dep_id, " ", persen, "%") as persen'))->get()->pluck('persen', 'dep_id'))
                    ->required(),
                Forms\Components\Select::make('bpd')
                    ->label('Bank')
                    ->relationship('bank', 'namabank')
                    ->required(),
                Forms\Components\TextInput::make('rekening')
                    ->required()
                    ->maxLength(25),
                Forms\Components\Select::make('stts_aktif')
                    ->label('Status Aktif')
                    ->options([
                        'AKTIF' => 'Aktif',
                        'CUTI' => 'Cuti',
                        'KELUAR' => 'Keluar',
                        'TENAGA LUAR' => 'Tenaga Luar',
                    ])
                    ->default('AKTIF')
                    ->required(),
                Forms\Components\TextInput::make('wajibmasuk')
                ->label('Wajib Masuk')
                    ->required()
                    ->default(0)
                    ->numeric(),
                // Forms\Components\TextInput::make('pengurang')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('indek')
                //     ->required()
                //     ->numeric(),
                Forms\Components\DatePicker::make('mulai_kontrak'),
                // Forms\Components\TextInput::make('cuti_diambil')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('dankes')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('photo')
                //     ->maxLength(500),
                Forms\Components\TextInput::make('no_ktp')
                    ->label('No. KTP')
                    ->required()
                    ->maxLength(20),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Pegawai::with([
                    'jnjJabatan',
                    'kelompokJabatan',
                    'resikoKerja',
                    'emergencyIndex',
                    'getDepartemen',
                    'getBidang',
                    'sttsWp',
                    'sttsKerja',
                    'getPendidikan',
                    'bank',
                    'indexIns',
                ])
                ->orderBy('nik', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk'),
                Tables\Columns\TextColumn::make('jbtn')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jnjJabatan.nama')
                    ->label('Jenjang Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelompokJabatan.nama_kelompok')
                    ->label('Kelompok Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resikoKerja.nama_resiko')
                    ->label('Resiko Kerja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('emergencyIndex.nama_emergency')
                    ->label('Tingkat Emergency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('getDepartemen.nama')
                    ->label('Departemen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sttsWp.ktg')
                    ->label('Status WP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sttsKerja.ktg')
                    ->label('Status Kerja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('npwp')
                    ->label('NPWP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('getPendidikan.tingkat')
                    ->label('Pendidikan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tmp_lahir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mulai_kerja')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ms_kerja'),
                Tables\Columns\TextColumn::make('bank.namabank')
                    ->label('Bank')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rekening')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_aktif'),
                Tables\Columns\TextColumn::make('wajibmasuk')
                    ->label('Wajib Masuk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mulai_kontrak')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_ktp')
                    ->searchable(),
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
            'index' => Pages\ManagePegawais::route('/'),
        ];
    }
}
