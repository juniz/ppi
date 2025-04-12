<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPengelolaanLinenKotorResource\Pages;
use App\Filament\Resources\AuditPengelolaanLinenKotorResource\RelationManagers;
use App\Models\AuditPengelolaanLinenKotor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;

class AuditPengelolaanLinenKotorResource extends Resource
{
    protected static ?string $model = AuditPengelolaanLinenKotor::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->label('Ruang')
                    ->relationship('ruangAuditKepatuhan', 'nama_ruang')
                    ->required(),
                Forms\Components\Select::make('audit1')
                    ->label('1. Petugas ruangan segera mengambil linen kotor untuk dimasukkan ke dalam bak setelah pasien pulang')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit2')
                    ->label('2. Petugas laundry mengambil linen kotor untuk dimasukkan ke dalam bak linen kotor denagan cara yang benar')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit3')
                    ->label('3. Petugas ruangan menempatkan linen koroe sesuai dengan wadah yang ditentukan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit4')
                    ->label('4. Petugas memasukkan linen kotor ke dalam bak linen kotor dengan benar')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit5')
                    ->label('5. Fasilitas bak penampungan linen kotor tersedia dengan baik di ruangan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit6')
                    ->label('6. Plastik untuk linen kotor tersedia dengan baik di ruangan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit7')
                    ->label('7. Petugas linen laundry mengambil linen kotor sesuai jadwal')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('audit8')
                    ->label('8. Sudah tersedia trolli khusus untuk linen infeksius dan non infeksius')
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
                AuditPengelolaanLinenKotor::with('ruangAuditKepatuhan')
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_pengelolaan_linen_kotor.*', DB::raw('CONCAT(ROUND(((audit1 = "Ya") + (audit2 = "Ya") + (audit3 = "Ya") + (audit4 = "Ya") + (audit5 = "Ya") + (audit6 = "Ya") + (audit7 = "Ya") + (audit8 = "Ya")) / 8 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->label('Ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('audit1')
                    ->label('1. Petugas ruangan segera mengambil linen kotor untuk dimasukkan ke dalam bak setelah pasien pulang'),
                Tables\Columns\TextColumn::make('audit2')
                    ->label('2. Petugas laundry mengambil linen kotor untuk dimasukkan ke dalam bak linen kotor denagan cara yang benar'),
                Tables\Columns\TextColumn::make('audit3')
                    ->label('3. Petugas ruangan menempatkan linen koroe sesuai dengan wadah yang ditentukan'),
                Tables\Columns\TextColumn::make('audit4')
                    ->label('4. Petugas memasukkan linen kotor ke dalam bak linen kotor dengan benar'),
                Tables\Columns\TextColumn::make('audit5')
                    ->label('5. Fasilitas bak penampungan linen kotor tersedia dengan baik di ruangan'),
                Tables\Columns\TextColumn::make('audit6')
                    ->label('6. Plastik untuk linen kotor tersedia dengan baik di ruangan'),
                Tables\Columns\TextColumn::make('audit7')
                    ->label('7. Petugas linen laundry mengambil linen kotor sesuai jadwal'),
                Tables\Columns\TextColumn::make('audit8')
                    ->label('8. Sudah tersedia trolli khusus untuk linen infeksius dan non infeksius'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('audit1')
                //     ->summarize([
                //         Count::make()->label('Ya')->query(fn(Builder $query) => $query->where('audit1', 'Ya')),
                //         Count::make()->label('Tidak')->query(fn(Builder $query) => $query->where('audit1', 'Tidak')),
                //         Summarizer::make()->label('Rata-rata')->using(fn(Builder $query) => $query->where('audit1', 'Ya')->count() == 0 ? 0 : ($query->where('audit1', 'Ya')->count() / $query->count()) * 100),
                //     ]),
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
            'index' => Pages\ManageAuditPengelolaanLinenKotors::route('/'),
        ];
    }
}
