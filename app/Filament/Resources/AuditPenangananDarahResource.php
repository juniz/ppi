<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditPenangananDarahResource\Pages;
use App\Filament\Resources\AuditPenangananDarahResource\RelationManagers;
use App\Models\AuditPenangananDarah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\RuangAuditKepatuhan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class AuditPenangananDarahResource extends Resource
{
    protected static ?string $model = AuditPenangananDarah::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Audit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ruang')
                    ->options(
                        RuangAuditKepatuhan::pluck('nama_ruang', 'id_ruang')
                    )
                    ->getOptionLabelUsing(fn($value): ?string => RuangAuditKepatuhan::find($value)?->nama_ruangan)
                    ->required(),
                Forms\Components\Select::make('menggunakan_apd_waktu_membuang_darah')
                    ->label('1. Menggunakan APD waktu membuang darah / komponen darah')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('komponen_darah_tidak_ada_dilantai')
                    ->label('2. Komponen darah tidak ada di lantai')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('membuang_darah_pada_tempat_ditentukan')
                    ->label('3. Membuang darah / komponen darah pada tempat yang ditentukan')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('pembersihan_areal_tumbahan_darah')
                    ->label('4. Pembersihan areal tumpahan darah dengan clorin / spill kit')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('apd_dibuang_di_limbah_infeksius')
                    ->label('5. APD yang digunakan dibuang di limbah infeksius')
                    ->options([
                        'Ya' => 'Ya',
                        'Tidak' => 'Tidak',
                    ])
                    ->default('Ya')
                    ->required(),
                Forms\Components\Select::make('melakukan_kebersihan_tangan_setelah_prosedur')
                    ->label('6. Melakukan kebersihan tangan setelah prosedur tersebut')
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
                AuditPenangananDarah::query()
                    ->with(['ruangAuditKepatuhan'])
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_penanganan_darah.*', DB::raw('CONCAT(ROUND(((menggunakan_apd_waktu_membuang_darah = "Ya") + (komponen_darah_tidak_ada_dilantai = "Ya") + (membuang_darah_pada_tempat_ditentukan = "Ya") + (pembersihan_areal_tumbahan_darah = "Ya") + (apd_dibuang_di_limbah_infeksius = "Ya") + (melakukan_kebersihan_tangan_setelah_prosedur = "Ya")) / 6 * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('menggunakan_apd_waktu_membuang_darah'),
                Tables\Columns\TextColumn::make('komponen_darah_tidak_ada_dilantai'),
                Tables\Columns\TextColumn::make('membuang_darah_pada_tempat_ditentukan'),
                Tables\Columns\TextColumn::make('pembersihan_areal_tumbahan_darah'),
                Tables\Columns\TextColumn::make('apd_dibuang_di_limbah_infeksius'),
                Tables\Columns\TextColumn::make('melakukan_kebersihan_tangan_setelah_prosedur'),
                Tables\Columns\TextColumn::make('ttl')
                    ->label('Ttl. Nilai (%)'),
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
            'index' => Pages\ManageAuditPenangananDarahs::route('/'),
        ];
    }
}
