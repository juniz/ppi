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
use Illuminate\Support\HtmlString;

class LajuVAP extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?string $navigationLabel = 'Laju VAP';
    protected static ?string $title = 'Laju VAP';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.laju-vap';

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
                        DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.VAP != 0 THEN data_HAIs.no_rawat END) as numerator'),
                        DB::raw('SUM(data_HAIs.VAP) as hari_ventilator'),
                        DB::raw('COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END) as denumerator'),
                        DB::raw("CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END)/NULLIF(SUM(data_HAIs.VAP),0))*1000), ' ‰') as laju_vap"),
                        DB::raw('CONCAT(ROUND((COUNT(CASE WHEN data_HAIs.VAP > 0 THEN 1 END)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.VAP != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
                    ])
                    ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal')
            )
            ->filters([
                DateRangeFilter::make('tanggal')
                    ->label('PERIODE')
                    ->startDate(Carbon::now())
                    ->endDate(Carbon::now())
                    ->modifyQueryUsing(
                        function (Builder $query, ?Carbon $startDate, ?Carbon $endDate, $dateString) {
                            if ($startDate && $endDate) {
                                $this->startDate = $startDate->format('Y-m-d');
                                $this->endDate = $endDate->format('Y-m-d');
                                
                                $query->whereBetween('data_HAIs.tanggal', [
                                    $startDate->startOfDay(),
                                    $endDate->endOfDay(),
                                ]);
                            }
                        }
                    )
                    ->autoApply(),
            ])
            ->columns([
                TextColumn::make('nm_bangsal')
                    ->label('KAMAR/BANGSAL')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->grow(false),
                TextColumn::make('numerator')
                    ->label(fn () => new HtmlString('JUMLAH PASIEN<br>TERPASANG VENTILATOR'))
                    ->alignCenter()
                    ->summarize(Sum::make()->label('Total Pasien'))
                    ->badge()
                    ->color('primary')
                    ->grow(false),
                TextColumn::make('hari_ventilator')
                    ->label(fn () => new HtmlString('JUMLAH HARI<br>TERPASANG VENTILATOR'))
                    ->alignCenter()
                    ->summarize(Sum::make()->label('Total Hari'))
                    ->badge()
                    ->color('info')
                    ->grow(false),
                TextColumn::make('denumerator')
                    ->label('VAP')
                    ->alignCenter()
                    ->summarize(Sum::make()->label('Total'))
                    ->badge()
                    ->color('warning')
                    ->grow(false),
                TextColumn::make('laju_vap')
                    ->label('LAJU VAP')
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->grow(false),
                TextColumn::make('persentase')
                    ->label('PERSENTASE')
                    ->alignCenter()
                    ->badge()
                    ->color('danger')
                    ->grow(false),
            ])
            ->striped()
            ->defaultPaginationPageOption(25)
            ->contentGrid([
                'md' => 2,
                'xl' => 6,
            ])
            ->paginated([25, 50, 100])
            ->poll('10s')
            ->defaultSort('nm_bangsal', 'asc');
    }
}
