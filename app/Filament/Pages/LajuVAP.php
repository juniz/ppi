<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DataHais;
use Filament\Tables\Filters\Filter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Facades\DB;

class LajuVAP extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laju VAP';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.laju-vap';

    public function getTableRecordKey($record): string
    {
        return (string) $record['nm_bangsal'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataHais::query()
                    ->join('kamar', 'data_HAIs.kd_kamar', '=', 'kamar.kd_kamar')
                    ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                    ->select([
                        'bangsal.nm_bangsal',
                        DB::raw('COUNT(DISTINCT data_HAIs.no_rawat) as numerator'),
                        DB::raw('SUM(data_HAIs.VAP) as denumerator'),
                        DB::raw('ROUND((COUNT(DISTINCT data_HAIs.no_rawat)/SUM(data_HAIs.VAP))*1000,2) as laju_vap'),
                    ])
                    ->where('data_HAIs.VAP', '>', 0)
                    ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            )
            ->columns([
                TextColumn::make('nm_bangsal')
                    ->label('KAMAR/BANGSAL')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('numerator')
                    ->label('JUMLAH PASIEN (NUMERATOR)')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('denumerator')
                    ->label('JUMLAH HARI (DENUMERATOR)')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('laju_vap')
                    ->label('LAJU VAP')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('persentase')
                    ->label('PERSENTASE VAP (%)')
                    ->alignCenter()
                    ->formatStateUsing(function ($record) {
                        $total = DataHais::query()
                            ->where('VAP', '>', 0)
                            ->sum(DB::raw('ROUND((COUNT(DISTINCT no_rawat)/SUM(VAP))*1000,2)'));
                        return number_format(($record->laju_vap / $total) * 100, 2) . ' %';
                    }),
            ])
            ->filters([
                DateRangeFilter::make('tanggal')
                    ->label('PERIODE')
            ])
            ->striped()
            ->defaultPaginationPageOption(25);
    }
}