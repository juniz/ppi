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
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;

class LajuIAD extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $title = 'Laju IAD';
    protected static ?string $slug = 'laju-iad';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.laju-iad';

    public $startDate;
    public $endDate;

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
                        DB::raw('SUM(data_HAIs.IAD) as denumerator'),
                        DB::raw('ROUND((COUNT(DISTINCT data_HAIs.no_rawat)/SUM(data_HAIs.IAD))*1000,2) as laju_iad'),
                        // DB::raw('ROUND(100/(SELECT COUNT(DISTINCT data_HAIs.kd_kamar) FROM data_HAIs WHERE data_HAIs.IAD > 0),2) as persentase')
                    ])
                    ->where('data_HAIs.IAD', '>', 0)
                    ->groupBy('bangsal.kd_bangsal', 'bangsal.kd_bangsal')
            )
            ->filters([
                DateRangeFilter::make('tanggal')
                    ->label('PERIODE')
                    ->startDate(Carbon::now())
                    ->endDate(Carbon::now())
                    ->modifyQueryUsing(
                        function (Builder $query, ?Carbon $startDate, ?Carbon $endDate, $dateString) {
                            $this->startDate = $startDate?->format('Y-m-d');
                            $this->endDate = $endDate?->format('Y-m-d');
                            $query->when(
                                !empty($dateString),
                                fn(Builder $query, $date): Builder =>
                                $query->whereBetween('data_HAIs.tanggal', [$startDate, $endDate])
                            );
                        }

                    )
                    ->autoApply(),
            ])
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
                TextColumn::make('laju_iad')
                    ->label('LAJU IAD')
                    ->alignCenter(),
                // ->formatStateUsing(fn($state) => number_format($state, 2)),
                TextColumn::make('persentase')
                    ->label('PERSENTASE IAD (%)')
                    ->alignCenter()
                    ->state(function ($record) {
                        $presentase = 0;
                        if ($this->startDate != null || $this->endDate != null) {
                            $data = DataHais::query()
                                ->select([
                                    DB::raw('ROUND(100/(SELECT COUNT(DISTINCT data_HAIs.kd_kamar)),2) as persentase')
                                ])
                                ->where('data_HAIs.IAD', '>', 0)
                                ->whereBetween('data_HAIs.tanggal', [$this->startDate, $this->endDate])
                                ->first();
                            $presentase = $data->persentase;
                        } else {
                            $data = DataHais::query()
                                ->select([
                                    DB::raw('ROUND(100/(SELECT COUNT(DISTINCT data_HAIs.kd_kamar)),2) as persentase')
                                ])
                                ->where('data_HAIs.IAD', '>', 0)
                                ->first();
                            $presentase = $data->persentase;
                        }
                        return $presentase  . ' %';
                    }),
            ])
            ->striped()
            ->defaultPaginationPageOption(25);
    }
}