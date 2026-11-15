<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditCuciTanganMedisResource\Pages;
use App\Filament\Resources\AuditCuciTanganMedisResource\Widgets\AuditCuciTanganStats;
use App\Models\AuditCuciTanganMedis;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn\IconColumnSize;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuditCuciTanganMedisResource extends Resource
{
    protected static ?string $model = AuditCuciTanganMedis::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $navigationGroup = 'Audit';
    protected static ?string $navigationLabel = 'Audit Cuci Tangan Medis';
    protected static ?string $modelLabel = 'Audit Cuci Tangan Medis';
    protected static ?string $pluralModelLabel = 'Audit Cuci Tangan Medis';
    protected static ?int $navigationSort = -10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(['default' => 2])
                    ->schema([
                        Forms\Components\DateTimePicker::make('tanggal')
                            ->label('Waktu')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->disabled(fn (string $context): bool => $context === 'edit')
                            ->dehydrated()
                            ->required(),

                        Forms\Components\Select::make('nik')
                            ->label('Petugas Medis')
                            ->relationship('pegawai', 'nama')
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->nama)
                            ->searchable(['nama', 'nik'])
                            ->preload()
                            ->required(),
                    ]),

                Forms\Components\Section::make('5 Momen Kebersihan Tangan (WHO)')
                    ->compact()
                    ->schema([
                        Forms\Components\ToggleButtons::make('sebelum_menyentuh_pasien')
                            ->label('1. Sblm Sentuh Pasien')
                            ->options([
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ])
                            ->colors([
                                'Ya' => 'success',
                                'Tidak' => 'danger',
                            ])
                            ->icons([
                                'Ya' => 'heroicon-m-check',
                                'Tidak' => 'heroicon-m-x-mark',
                            ])
                            ->inline()
                            ->inlineLabel()
                            ->default('Ya')
                            ->required(),

                        Forms\Components\ToggleButtons::make('sebelum_tehnik_aseptik')
                            ->label('2. Sblm Tindakan Aseptik')
                            ->options([
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ])
                            ->colors([
                                'Ya' => 'success',
                                'Tidak' => 'danger',
                            ])
                            ->icons([
                                'Ya' => 'heroicon-m-check',
                                'Tidak' => 'heroicon-m-x-mark',
                            ])
                            ->inline()
                            ->inlineLabel()
                            ->default('Ya')
                            ->required(),

                        Forms\Components\ToggleButtons::make('setelah_terpapar_cairan_tubuh_pasien')
                            ->label('3. Stlh Cairan Tubuh')
                            ->options([
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ])
                            ->colors([
                                'Ya' => 'success',
                                'Tidak' => 'danger',
                            ])
                            ->icons([
                                'Ya' => 'heroicon-m-check',
                                'Tidak' => 'heroicon-m-x-mark',
                            ])
                            ->inline()
                            ->inlineLabel()
                            ->default('Ya')
                            ->required(),

                        Forms\Components\ToggleButtons::make('setelah_kontak_dengan_pasien')
                            ->label('4. Stlh Sentuh Pasien')
                            ->options([
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ])
                            ->colors([
                                'Ya' => 'success',
                                'Tidak' => 'danger',
                            ])
                            ->icons([
                                'Ya' => 'heroicon-m-check',
                                'Tidak' => 'heroicon-m-x-mark',
                            ])
                            ->inline()
                            ->inlineLabel()
                            ->default('Ya')
                            ->required(),

                        Forms\Components\ToggleButtons::make('setelah_kontak_dengan_lingkungan_pasien')
                            ->label('5. Stlh Lingkungan Pasien')
                            ->options([
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ])
                            ->colors([
                                'Ya' => 'success',
                                'Tidak' => 'danger',
                            ])
                            ->icons([
                                'Ya' => 'heroicon-m-check',
                                'Tidak' => 'heroicon-m-x-mark',
                            ])
                            ->inline()
                            ->inlineLabel()
                            ->default('Ya')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AuditCuciTanganMedis::query()
                    ->with('pegawai')
                    ->select(
                        'audit_cuci_tangan_medis.*',
                        DB::raw("ROUND(((CASE WHEN sebelum_menyentuh_pasien = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN sebelum_tehnik_aseptik = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN setelah_terpapar_cairan_tubuh_pasien = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN setelah_kontak_dengan_pasien = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN setelah_kontak_dengan_lingkungan_pasien = 'Ya' THEN 1 ELSE 0 END)) / 5 * 100, 1) as ttl")
                    )
            )
            ->defaultSort('tanggal', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Waktu Observasi')
                    ->dateTime('d M Y, H:i')
                    ->description(fn ($record): string => Carbon::parse($record->tanggal)->diffForHumans())
                    ->sortable(),

                Tables\Columns\TextColumn::make('pegawai.nama')
                    ->label('Petugas Medis')
                    ->icon('heroicon-m-user-circle')
                    ->weight(FontWeight::SemiBold)
                    ->description(fn ($record): string => 'NIK: ' . ($record->nik ?? '-'))
                    ->searchable(['nama', 'nik'])
                    ->sortable(),

                ColumnGroup::make('5 Momen Kebersihan Tangan (WHO)', [
                    Tables\Columns\IconColumn::make('sebelum_menyentuh_pasien')
                        ->label('Momen 1')
                        ->tooltip('1. Sebelum Menyentuh Pasien')
                        ->color(fn (?string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (?string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('sebelum_tehnik_aseptik')
                        ->label('Momen 2')
                        ->tooltip('2. Sebelum Melakukan Prosedur Bersih / Aseptik')
                        ->color(fn (?string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (?string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('setelah_terpapar_cairan_tubuh_pasien')
                        ->label('Momen 3')
                        ->tooltip('3. Setelah Risiko Terpapar Cairan Tubuh Pasien')
                        ->color(fn (?string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (?string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('setelah_kontak_dengan_pasien')
                        ->label('Momen 4')
                        ->tooltip('4. Setelah Kontak dengan Pasien')
                        ->color(fn (?string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (?string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('setelah_kontak_dengan_lingkungan_pasien')
                        ->label('Momen 5')
                        ->tooltip('5. Setelah Kontak dengan Lingkungan Pasien')
                        ->color(fn (?string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (?string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),
                ]),

                Tables\Columns\TextColumn::make('ttl')
                    ->label('Kepatuhan')
                    ->badge()
                    ->formatStateUsing(function ($state, $record): string {
                        if (! $record) return '-';
                        $ya = (($record->sebelum_menyentuh_pasien ?? '') === 'Ya' ? 1 : 0)
                            + (($record->sebelum_tehnik_aseptik ?? '') === 'Ya' ? 1 : 0)
                            + (($record->setelah_terpapar_cairan_tubuh_pasien ?? '') === 'Ya' ? 1 : 0)
                            + (($record->setelah_kontak_dengan_pasien ?? '') === 'Ya' ? 1 : 0)
                            + (($record->setelah_kontak_dengan_lingkungan_pasien ?? '') === 'Ya' ? 1 : 0);
                        $pct = round(($ya / 5) * 100, 1);
                        return "{$ya}/5 ({$pct}%)";
                    })
                    ->color(fn ($state): string => match (true) {
                        (float) ($state ?? 0) >= 85 => 'success',
                        (float) ($state ?? 0) >= 75 => 'warning',
                        default => 'danger',
                    })
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Pilih tanggal awal')
                            ->closeOnDateSelection(),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Pilih tanggal akhir')
                            ->closeOnDateSelection(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators['dari_tanggal'] = 'Dari: ' . Carbon::parse($data['dari_tanggal'])->translatedFormat('d M Y');
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators['sampai_tanggal'] = 'Sampai: ' . Carbon::parse($data['sampai_tanggal'])->translatedFormat('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('nik')
                    ->label('Petugas Medis')
                    ->relationship('pegawai', 'nama')
                    ->searchable()
                    ->preload()
                    ->indicator('Petugas'),

                Tables\Filters\SelectFilter::make('kepatuhan')
                    ->label('Tingkat Kepatuhan (PPI)')
                    ->options([
                        '100' => 'Patuh Sempurna (100%)',
                        'target' => 'Sesuai Standar Mutu (≥ 85%)',
                        'kurang' => 'Perlu Pembinaan (< 85%)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $val = $data['value'] ?? null;
                        $formula = "((CASE WHEN sebelum_menyentuh_pasien = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN sebelum_tehnik_aseptik = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN setelah_terpapar_cairan_tubuh_pasien = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN setelah_kontak_dengan_pasien = 'Ya' THEN 1 ELSE 0 END) + (CASE WHEN setelah_kontak_dengan_lingkungan_pasien = 'Ya' THEN 1 ELSE 0 END))";
                        if ($val === '100') {
                            return $query->whereRaw("{$formula} = 5");
                        }
                        if ($val === 'target') {
                            return $query->whereRaw("ROUND({$formula} / 5 * 100, 1) >= 85");
                        }
                        if ($val === 'kurang') {
                            return $query->whereRaw("ROUND({$formula} / 5 * 100, 1) < 85");
                        }
                        return $query;
                    })
                    ->indicator('Kepatuhan'),
            ])
            ->filtersLayout(FiltersLayout::Dropdown)
            ->filtersFormWidth(MaxWidth::TwoExtraLarge)
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->modalHeading('Ubah Evaluasi Cuci Tangan')
                    ->modalWidth(MaxWidth::Medium),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-m-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            AuditCuciTanganStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuditCuciTanganMedis::route('/'),
        ];
    }
}
