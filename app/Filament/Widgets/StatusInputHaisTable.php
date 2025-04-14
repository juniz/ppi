<?php

namespace App\Filament\Widgets;

use App\Models\Bangsal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class StatusInputHaisTable extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Status Input HAIs Per Ruangan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Bangsal::query()
                    ->selectRaw('
                        bangsal.kd_bangsal,
                        bangsal.nm_bangsal,
                        COUNT(DISTINCT CASE WHEN kamar_inap.no_rawat IS NOT NULL THEN kamar_inap.no_rawat END) as jumlah_pasien,
                        COUNT(DISTINCT CASE WHEN data_HAIs.no_rawat IS NOT NULL THEN data_HAIs.no_rawat END) as sudah_input,
                        COUNT(DISTINCT CASE WHEN kamar_inap.no_rawat IS NOT NULL AND data_HAIs.no_rawat IS NULL THEN kamar_inap.no_rawat END) as belum_input
                    ')
                    ->leftJoin('kamar', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
                    ->leftJoin('kamar_inap', function($join) {
                        $join->on('kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
                            ->where('kamar_inap.stts_pulang', '=', '-');
                    })
                    ->leftJoin('data_HAIs', function($join) {
                        $join->on('kamar_inap.no_rawat', '=', 'data_HAIs.no_rawat')
                            ->whereDate('data_HAIs.tanggal', now());
                    })
                    ->where('bangsal.status', '=', '1')
                    ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            )
            ->columns([
                TextColumn::make('nm_bangsal')
                    ->label('RUANGAN')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jumlah_pasien')
                    ->label('JUMLAH PASIEN')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('sudah_input')
                    ->label('SUDAH INPUT')
                    ->alignCenter()
                    ->sortable()
                    ->color('success'),
                TextColumn::make('belum_input')
                    ->label('BELUM INPUT')
                    ->alignCenter()
                    ->sortable()
                    ->color('danger'),
                TextColumn::make('persentase')
                    ->label('PERSENTASE')
                    ->alignCenter()
                    ->state(function ($record) {
                        if ($record->jumlah_pasien > 0) {
                            $persentase = ($record->sudah_input / $record->jumlah_pasien) * 100;
                            return number_format($persentase, 1) . '%';
                        }
                        return '0%';
                    })
                    ->color(function ($record) {
                        if ($record->jumlah_pasien == 0) return 'gray';
                        $persentase = ($record->sudah_input / $record->jumlah_pasien) * 100;
                        if ($persentase >= 90) return 'success';
                        if ($persentase >= 70) return 'warning';
                        return 'danger';
                    })
                    ->sortable(),
            ])
            ->defaultSort('nm_bangsal', 'asc')
            ->striped();
    }

    public function getTableRecordKey(mixed $record): string
    {
        return $record->kd_bangsal;
    }
} 