<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPenempatanPasienResource\Pages;
use App\Filament\Resources\AuditPenempatanPasienResource\RelationManagers;
use App\Models\AuditPenempatanPasien;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;

class AuditPenempatanPasienResource extends Resource
{
    protected static ?string $model = AuditPenempatanPasien::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->label('Ruang')
                    ->relationship('ruangAuditKepatuhan', 'nama_ruang')
                    ->required(),
                Forms\Components\Select::make('audit1')
                    ->label('1. Petugas melakukan kebersihan tangan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit2')
                    ->label('2. Petugas mengunakan APD sesuai dengan pola transmisi infeksi pasien')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit3')
                    ->label('3. Petugas menempatkan pasien sesuai dengan pola transmisi infeksi penyakit pasien (kontak, udara, droplet)')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit4')
                    ->label('4. Petugas menempatkan pasien dengan sesuai kecurigaan penularan udara di ruang isolasi dengan ventilasi negatif (-)')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit5')
                    ->label('5. Petugas memberi informasi kepada penunggu pasien pintu ruang isolasi selalu tertutup')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit6')
                    ->label('6. Petugas konsultasi terlebih dahulu dengan komite PPI untuk menentukan pasien yang dapat sidatukan dalam 1 ruangan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit7')
                    ->label('7. Petugas memberikan edukasi berdasarkan jenis transmisinya (kontak, droplet, udara) untuk semua ruangan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit8')
                    ->label('8. Petugas melepaskan segera APD')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit9')
                    ->label('9. Petugas melakukan kebersihan tangan setelah kontak')
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
                AuditPenempatanPasien::with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_penempatan_pasien.*', DB::raw('CONCAT(ROUND(((audit1 = "Ya") + (audit2 = "Ya") + (audit3 = "Ya") + (audit4 = "Ya") + (audit5 = "Ya") + (audit6 = "Ya") + (audit7 = "Ya") + (audit8 = "Ya") + (audit9 = "Ya")) / 9 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->label('Ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('audit1')
                    ->label('1. Petugas melakukan kebersihan tangan'),
                Tables\Columns\TextColumn::make('audit2')
                    ->label('2. Petugas mengunakan APD sesuai dengan pola transmisi infeksi pasien'),
                Tables\Columns\TextColumn::make('audit3')
                    ->label('3. Petugas menempatkan pasien sesuai dengan pola transmisi infeksi penyakit pasien (kontak, udara, droplet)'),
                Tables\Columns\TextColumn::make('audit4')
                    ->label('4. Petugas menempatkan pasien dengan sesuai kecurigaan penularan udara di ruang isolasi dengan ventilasi negatif (-)'),
                Tables\Columns\TextColumn::make('audit5')
                    ->label('5. Petugas memberi informasi kepada penunggu pasien pintu ruang isolasi selalu tertutup'),
                Tables\Columns\TextColumn::make('audit6')
                    ->label('6. Petugas konsultasi terlebih dahulu dengan komite PPI untuk menentukan pasien yang dapat sidatukan dalam 1 ruangan'),
                Tables\Columns\TextColumn::make('audit7')
                    ->label('7. Petugas memberikan edukasi berdasarkan jenis transmisinya (kontak, droplet, udara) untuk semua ruangan'),
                Tables\Columns\TextColumn::make('audit8')
                    ->label('8. Petugas melepaskan segera APD'),
                Tables\Columns\TextColumn::make('audit9')
                    ->label('9. Petugas melakukan kebersihan tangan setelah kontak'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('audit1')
                    ->label('1. Petugas melakukan kebersihan tangan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit1', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit1', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit1', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit2')
                    ->label('2. Petugas mengunakan APD sesuai dengan pola transmisi infeksi pasien')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit2', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit2', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit2', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit3')
                    ->label('3. Petugas menempatkan pasien sesuai dengan pola transmisi infeksi penyakit pasien (kontak, udara, droplet)')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit3', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit3', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit3', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit4')
                    ->label('4. Petugas menempatkan pasien dengan sesuai kecurigaan penularan udara di ruang isolasi dengan ventilasi negatif (-)')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit4', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit4', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit4', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit5')
                    ->label('5. Petugas memberi informasi kepada penunggu pasien pintu ruang isolasi selalu tertutup')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit5', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit5', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit5', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit6')
                    ->label('6. Petugas konsultasi terlebih dahulu dengan komite PPI untuk menentukan pasien yang dapat sidatukan dalam 1 ruangan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit6', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit6', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit6', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit7')
                    ->label('7. Petugas memberikan edukasi berdasarkan jenis transmisinya (kontak, droplet, udara) untuk semua ruangan')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit7', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit7', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit7', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit8')
                    ->label('8. Petugas melepaskan segera APD')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit8', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit8', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit8', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('audit9')
                    ->label('9. Petugas melakukan kebersihan tangan setelah kontak')
                    ->summarize([
                        Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit9', 'Ya')),
                        Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit9', 'Tidak')),
                        Summarizer::make()->using(function (Builder $query) {
                            $total = $query->count();
                            $ya = $query->where('audit9', 'Ya')->count();
                            return $total == 0 ? 0 : ($ya / $total) * 100;
                        })
                            ->label('Rata-rata')
                            ->suffix('%'),
                    ]),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->summarize([
                        Summarizer::make()->label('Ya')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->audit1 == 'Ya') $ttl++;
                                if ($item->audit2 == 'Ya') $ttl++;
                                if ($item->audit3 == 'Ya') $ttl++;
                                if ($item->audit4 == 'Ya') $ttl++;
                                if ($item->audit5 == 'Ya') $ttl++;
                                if ($item->audit6 == 'Ya') $ttl++;
                                if ($item->audit7 == 'Ya') $ttl++;
                                if ($item->audit8 == 'Ya') $ttl++;
                                if ($item->audit9 == 'Ya') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Tidak')->using(function (Builder $query) {
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->audit1 == 'Tidak') $ttl++;
                                if ($item->audit2 == 'Tidak') $ttl++;
                                if ($item->audit3 == 'Tidak') $ttl++;
                                if ($item->audit4 == 'Tidak') $ttl++;
                                if ($item->audit5 == 'Tidak') $ttl++;
                                if ($item->audit6 == 'Tidak') $ttl++;
                                if ($item->audit7 == 'Tidak') $ttl++;
                                if ($item->audit8 == 'Tidak') $ttl++;
                                if ($item->audit9 == 'Tidak') $ttl++;
                            }
                            return $ttl;
                        }),
                        Summarizer::make()->label('Rata-rata')->using(function (Builder $query) {
                            $total = $query->count() * 9;
                            $ttl = 0;
                            foreach ($query->get() as $item) {
                                if ($item->audit1 == 'Ya') $ttl++;
                                if ($item->audit2 == 'Ya') $ttl++;
                                if ($item->audit3 == 'Ya') $ttl++;
                                if ($item->audit4 == 'Ya') $ttl++;
                                if ($item->audit5 == 'Ya') $ttl++;
                                if ($item->audit6 == 'Ya') $ttl++;
                                if ($item->audit7 == 'Ya') $ttl++;
                                if ($item->audit8 == 'Ya') $ttl++;
                                if ($item->audit9 == 'Ya') $ttl++;
                            }
                            return round((($ttl / $total) * 100), 2);
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
            'index' => Pages\ManageAuditPenempatanPasiens::route('/'),
        ];
    }
}
