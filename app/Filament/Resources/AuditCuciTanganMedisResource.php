<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditCuciTanganMedisResource\Pages;
use App\Filament\Resources\AuditCuciTanganMedisResource\Widgets;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

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
                    ->description('Evaluasi kepatuhan praktik cuci tangan petugas medis sesuai panduan baku WHO dan standar Akreditasi PPI.')
                    ->icon('heroicon-m-hand-raised')
                    ->compact()
                    ->schema([
                        Forms\Components\ToggleButtons::make('sebelum_menyentuh_pasien')
                            ->label(new HtmlString('
                                <div class="flex items-center gap-3 text-left">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-xs font-black text-sky-800 ring-1 ring-sky-700/20 dark:bg-sky-950/60 dark:text-sky-300 dark:ring-sky-500/30">1</span>
                                    <span class="text-sm font-bold text-gray-950 dark:text-white leading-tight">Sebelum Menyentuh Pasien</span>
                                </div>
                            '))
                            ->extraAttributes(['class' => 'audit-momen-card'])
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
                            ->label(new HtmlString('
                                <div class="flex items-center gap-3 text-left">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-xs font-black text-indigo-800 ring-1 ring-indigo-700/20 dark:bg-indigo-950/60 dark:text-indigo-300 dark:ring-indigo-500/30">2</span>
                                    <span class="text-sm font-bold text-gray-950 dark:text-white leading-tight">Sebelum Tindakan Aseptik</span>
                                </div>
                            '))
                            ->extraAttributes(['class' => 'audit-momen-card'])
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
                            ->label(new HtmlString('
                                <div class="flex items-center gap-3 text-left">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-xs font-black text-amber-800 ring-1 ring-amber-700/20 dark:bg-amber-950/60 dark:text-amber-300 dark:ring-amber-500/30">3</span>
                                    <span class="text-sm font-bold text-gray-950 dark:text-white leading-tight">Setelah Terpapar Cairan Tubuh Pasien</span>
                                </div>
                            '))
                            ->extraAttributes(['class' => 'audit-momen-card'])
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
                            ->label(new HtmlString('
                                <div class="flex items-center gap-3 text-left">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-xs font-black text-emerald-800 ring-1 ring-emerald-700/20 dark:bg-emerald-950/60 dark:text-emerald-300 dark:ring-emerald-500/30">4</span>
                                    <span class="text-sm font-bold text-gray-950 dark:text-white leading-tight">Setelah Menyentuh Pasien</span>
                                </div>
                            '))
                            ->extraAttributes(['class' => 'audit-momen-card'])
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
                            ->label(new HtmlString('
                                <div class="flex items-center gap-3 text-left">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-teal-100 text-xs font-black text-teal-800 ring-1 ring-teal-700/20 dark:bg-teal-950/60 dark:text-teal-300 dark:ring-teal-500/30">5</span>
                                    <span class="text-sm font-bold text-gray-950 dark:text-white leading-tight">Setelah Kontak dengan Lingkungan Pasien</span>
                                </div>
                            '))
                            ->extraAttributes(['class' => 'audit-momen-card'])
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
                    ->orderBy('tanggal', 'desc')
                    ->select(
                        'audit_cuci_tangan_medis.*',
                        DB::raw('ROUND(((sebelum_menyentuh_pasien = "Ya") + (sebelum_tehnik_aseptik = "Ya") + (setelah_terpapar_cairan_tubuh_pasien = "Ya") + (setelah_kontak_dengan_pasien = "Ya") + (setelah_kontak_dengan_lingkungan_pasien = "Ya")) / 5 * 100, 1) as ttl')
                    )
            )
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
                        ->color(fn (string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('sebelum_tehnik_aseptik')
                        ->label('Momen 2')
                        ->tooltip('2. Sebelum Melakukan Prosedur Bersih / Aseptik')
                        ->color(fn (string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('setelah_terpapar_cairan_tubuh_pasien')
                        ->label('Momen 3')
                        ->tooltip('3. Setelah Risiko Terpapar Cairan Tubuh Pasien')
                        ->color(fn (string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('setelah_kontak_dengan_pasien')
                        ->label('Momen 4')
                        ->tooltip('4. Setelah Kontak dengan Pasien')
                        ->color(fn (string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'Ya' => 'heroicon-s-check-circle',
                            default => 'heroicon-s-x-circle',
                        })
                        ->size(IconColumnSize::Large)
                        ->alignCenter(),

                    Tables\Columns\IconColumn::make('setelah_kontak_dengan_lingkungan_pasien')
                        ->label('Momen 5')
                        ->tooltip('5. Setelah Kontak dengan Lingkungan Pasien')
                        ->color(fn (string $state): string => match ($state) {
                            'Ya' => 'success',
                            default => 'danger',
                        })
                        ->icon(fn (string $state): string => match ($state) {
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
                        if ($state === null) return '-';
                        $ya = ($record->sebelum_menyentuh_pasien === 'Ya' ? 1 : 0)
                            + ($record->sebelum_tehnik_aseptik === 'Ya' ? 1 : 0)
                            + ($record->setelah_terpapar_cairan_tubuh_pasien === 'Ya' ? 1 : 0)
                            + ($record->setelah_kontak_dengan_pasien === 'Ya' ? 1 : 0)
                            + ($record->setelah_kontak_dengan_lingkungan_pasien === 'Ya' ? 1 : 0);
                        return "{$ya}/5 ({$state}%)";
                    })
                    ->color(fn ($state): string => match (true) {
                        (float) $state >= 85 => 'success',
                        (float) $state >= 75 => 'warning',
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
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
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
                    }),

                Tables\Filters\SelectFilter::make('nik')
                    ->label('Petugas Medis')
                    ->relationship('pegawai', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->modalHeading('Ubah Evaluasi Cuci Tangan')
                    ->modalWidth(MaxWidth::ExtraLarge),
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
            Widgets\AuditCuciTanganStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAuditCuciTanganMedis::route('/'),
        ];
    }
}
