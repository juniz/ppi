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
use Illuminate\Support\HtmlString;

class LajuISK extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $title = 'Laju ISK';
    protected static ?string $slug = 'laju-isk';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.laju-isk';

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
                        DB::raw('COUNT(DISTINCT CASE WHEN data_HAIs.UC != 0 THEN data_HAIs.no_rawat END) as numerator'),
                        DB::raw('SUM(data_HAIs.UC) as hari_uc'),
                        DB::raw('SUM(data_HAIs.ISK) as denumerator'),
                        DB::raw("CONCAT(ROUND((SUM(data_HAIs.ISK)/NULLIF(SUM(data_HAIs.UC),0))*1000), ' ‰') as laju_isk"),
                        DB::raw('CONCAT(ROUND((SUM(data_HAIs.ISK)/NULLIF(COUNT(DISTINCT CASE WHEN data_HAIs.UC != 0 THEN data_HAIs.no_rawat END),0))*100, 2), " %") as persentase')
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
                    ->label(fn () => new HtmlString('JUMLAH PASIEN<br>TERPASANG UC'))
                    ->alignCenter()
                    ->summarize(Sum::make()->label('Total Pasien'))
                    ->badge()
                    ->color('primary')
                    ->grow(false),
                TextColumn::make('hari_uc')
                    ->label(new HtmlString('<div style="line-height: 1.2">JUMLAH HARI<br>TERPASANG UC</div>'))
                    ->html()
                    ->alignCenter()
                    ->summarize(Sum::make()->label('Total Hari'))
                    ->badge()
                    ->color('info')
                    ->grow(false),
                TextColumn::make('denumerator')
                    ->label('ISK')
                    ->alignCenter()
                    ->summarize(Sum::make()->label('Total'))
                    ->badge()
                    ->color('warning')
                    ->grow(false),
                TextColumn::make('laju_isk')
                    ->label('LAJU ISK')
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

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
} 