<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Models\DataHais;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Database\Eloquent\Model;

class HaisPerBangsal extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'HAIs Per Bangsal';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.hais-per-bangsal';

    public function getTableRecordKey(Model $record): string
    {
        return $record->nm_bangsal;
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
                        \DB::raw('COUNT(DISTINCT data_HAIs.no_rawat) as jml_pasien'),
                        \DB::raw('SUM(data_HAIs.ETT) as ETT'),
                        \DB::raw('SUM(data_HAIs.CVL) as CVL'),
                        \DB::raw('SUM(data_HAIs.IVL) as IVL'),
                        \DB::raw('SUM(data_HAIs.UC) as UC'),
                        \DB::raw('SUM(data_HAIs.VAP) as VAP'),
                        \DB::raw('SUM(data_HAIs.IAD) as IAD'),
                        \DB::raw('SUM(data_HAIs.PLEB) as PLEB'),
                        \DB::raw('SUM(data_HAIs.ISK) as ISK'),
                        \DB::raw('SUM(data_HAIs.ILO) as ILO'),
                        \DB::raw('SUM(data_HAIs.HAP) as HAP'),
                        \DB::raw('SUM(data_HAIs.Tinea) as Tinea'),
                        \DB::raw('SUM(data_HAIs.Scabies) as Scabies'),
                        \DB::raw('SUM(data_HAIs.DEKU = "IYA") as DEKU'),
                        \DB::raw('SUM(data_HAIs.SPUTUM <> "") as SPUTUM'),
                        \DB::raw('SUM(data_HAIs.DARAH <> "") as DARAH'),
                        \DB::raw('SUM(data_HAIs.URINE <> "") as URINE'),
                        \DB::raw('SUM(data_HAIs.ANTIBIOTIK <> "") as ANTIBIOTIK')
                    ])
                    ->groupBy('bangsal.nm_bangsal')
            )
            ->filters([
                DateRangeFilter::make('tanggal')
                    ->label('Periode')
                    ->startDate(Carbon::now())
                    ->endDate(Carbon::now())
                    ->modifyQueryUsing(
                        fn(Builder $query, ?Carbon $startDate, ?Carbon $endDate, $dateString) =>
                        $query->when(
                            !empty($dateString),
                            fn(Builder $query, $date): Builder =>
                            $query->whereBetween('tanggal', [$startDate, $endDate])
                        )
                    )
                    ->autoApply(),
            ])
            ->columns([
                TextColumn::make('no')
                    ->label('NO.')
                    ->rowIndex()
                    ->alignCenter(),
                TextColumn::make('nm_bangsal')
                    ->label('KAMAR/BANGSAL')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jml_pasien')
                    ->label('JML.PASIEN')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('ETT')
                    ->label('ETT')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('CVL')
                    ->label('CVL')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('IVL')
                    ->label('IVL')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('UC')
                    ->label('UC')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('VAP')
                    ->label('VAP')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('IAD')
                    ->label('IAD')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('PLEB')
                    ->label('PLEB')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('ISK')
                    ->label('ISK')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('ILO')
                    ->label('ILO')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('HAP')
                    ->label('HAP')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('Tinea')
                    ->label('TINEA')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('Scabies')
                    ->label('SCABIES')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('DEKU')
                    ->label('DEKU')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('SPUTUM')
                    ->label('SPUTUM')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('DARAH')
                    ->label('DARAH')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('URINE')
                    ->label('URINE')
                    ->alignCenter()
                    ->summarize(Sum::make()),
                TextColumn::make('ANTIBIOTIK')
                    ->label('ANTIBIOTIK')
                    ->alignCenter()
                    ->summarize(Sum::make()),
            ])
            ->striped();
    }
} 