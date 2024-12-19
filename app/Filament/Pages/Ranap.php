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
use Filament\Forms\Form;

class Ranap extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-m-building-office';
    protected static ?string $navigationGroup = 'Data Pasien';

    protected static string $view = 'filament.pages.ranap';

    public function table(Table $table): Table
    {
        return $table
            ->query(RegPeriksa::query()->where('status_lanjut', 'Ranap'))
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
            ])
            ->actions([
                Tables\Actions\CreateAction::make('hais')
                    ->label('Data HAIs')
                    ->modalHeading('Data HAIs')
                    ->mountUsing(function (Form $form, RegPeriksa $regPeriksa) {
                        $data = \App\Models\DataHais::where('no_rawat', $regPeriksa->no_rawat)->first();
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
                            $kamar = \App\Models\KamarInap::where('no_rawat', $regPeriksa->no_rawat)->where('tgl_keluar', '0000-00-00')->first();
                            $data['kd_kamar'] = $kamar->kd_kamar;
                            \App\Models\DataHais::updateOrCreate([
                                'no_rawat' => $regPeriksa->no_rawat,
                                'tanggal' => $data['tanggal'],
                            ], $data);

                            Notification::make()
                                ->title('Data HAIs berhasil disimpan')
                                ->success()
                                ->send();

                            $this->redirect('/data-hais?tableSearch=' . $regPeriksa->no_rawat);
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
                                    ->label('Deku')
                                    ->options([
                                        'IYA' => 'IYA',
                                        'TIDAK' => 'TIDAK'
                                    ])
                                    ->default('Tidak')
                                    ->required(),
                                TextInput::make('SPUTUM')
                                    ->label('Sputum')
                                    ->default('')
                                    ->required(),
                                TextInput::make('DARAH')
                                    ->label('Darah')
                                    ->default('')
                                    ->required(),
                                TextInput::make('URINE')
                                    ->label('Urine')
                                    ->default('')
                                    ->required(),
                            ]),
                            Section::make([
                                Section::make('Hari Pemasangan Alat')
                                    ->schema([
                                        TextInput::make('ETT')
                                            ->label('ETT')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('CVL')
                                            ->label('CVL')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('IVL')
                                            ->label('IVL')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('UC')
                                            ->label('UC')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                    ])
                                    ->columns(4),
                                Section::make('Infeksi RS')
                                    ->schema([
                                        TextInput::make('VAP')
                                            ->label('VAP')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('IAD')
                                            ->label('IAD')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('PLEB')
                                            ->label('PLEB')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('ISK')
                                            ->label('ISK')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('ILO')
                                            ->label('ILO')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('HAP')
                                            ->label('HAP')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('Tinea')
                                            ->label('Tinea')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                        TextInput::make('Scabies')
                                            ->label('Scabies')
                                            ->numeric()
                                            ->default('0')
                                            ->required(),
                                    ])
                                    ->columns(4),
                                TextInput::make('ANTIBIOTIK')
                                    ->label('Antibiotik')
                                    ->default('')
                                    ->required(),
                                // TextInput::make('TIRAH')
                                //     ->label('Tirah Baring')
                                //     ->default('1')
                                //     ->required(),
                            ]),
                        ])
                            ->from('md')
                    ])
                    ->modalWidth(MaxWidth::Full)
                    // ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Batal')
                    ->modalSubmitActionLabel('Simpan'),
            ], position: ActionsPosition::BeforeColumns);
    }
}
