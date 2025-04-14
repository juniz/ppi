<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class HaisBulanan extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan HAIs';
    protected static ?string $navigationLabel = 'HAIs Bulanan';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.hais-bulanan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\DataHais::query()
                    // ->with('kamar.bangsal')
                    ->with('regPeriksa.pasien')
                    ->join('kamar', 'data_HAIs.kd_kamar', '=', 'kamar.kd_kamar')
                    ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                    ->orderByDesc('tanggal')
                    ->groupBy('tanggal')
                    ->selectRaw('tanggal,
                        bangsal.nm_bangsal, 
                        COUNT(no_rawat) AS jml,
                        SUM(ETT) AS ETT,
                        SUM(CVL) AS CVL,
                        SUM(IVL) AS IVL,
                        SUM(UC) AS UC,
                        SUM(VAP) AS VAP,
                        SUM(IAD) AS IAD,
                        SUM(PLEB) AS PLEB,
                        SUM(ISK) AS ISK,
                        SUM(ILO) AS ILO,
                        SUM(HAP) AS HAP,
                        SUM(Tinea) AS Tinea,
                        SUM(Scabies) AS Scabies,
                        SUM(DEKU = "IYA") AS DEKU,
                        SUM(SPUTUM <> "") AS SPUTUM,
                        SUM(DARAH <> "") AS DARAH,
                        SUM(URINE <> "") AS URINE,
                        SUM(ANTIBIOTIK <> "") AS ANTIBIOTIK')
            )
            ->filters([
                DateRangeFilter::make('tanggal')
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
                SelectFilter::make('bangsal.kd_bangsal')
                    ->label('Bangsal')
                    ->relationship('kamar.bangsal', 'nm_bangsal'),
            ])
            ->actions([])
            ->columns([
                // Tables\Columns\TextColumn::make('regPeriksa.pasien.nm_pasien')
                //     ->label('Nama Pasien')
                //     ->searchable()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime('d-m-Y')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('regPeriksa.pasien.jk')
                //     ->label('JK')
                //     ->sortable(),
                Tables\Columns\TextColumn::make('jml')
                    ->label('Jml. Pasien'),
                Tables\Columns\TextColumn::make('ETT')
                    ->label('ETT'),
                Tables\Columns\TextColumn::make('CVL')
                    ->label('CVL'),
                Tables\Columns\TextColumn::make('IVL')
                    ->label('IVL'),
                Tables\Columns\TextColumn::make('UC')
                    ->label('UC'),
                Tables\Columns\TextColumn::make('VAP')
                    ->label('VAP'),
                Tables\Columns\TextColumn::make('IAD')
                    ->label('IAD'),
                Tables\Columns\TextColumn::make('PLEB')
                    ->label('PLEB'),
                Tables\Columns\TextColumn::make('ISK')
                    ->label('ISK'),
                Tables\Columns\TextColumn::make('ILO')
                    ->label('ILO'),
                Tables\Columns\TextColumn::make('HAP')
                    ->label('HAP'),
                Tables\Columns\TextColumn::make('Tinea')
                    ->label('Tinea'),
                Tables\Columns\TextColumn::make('Scabies')
                    ->label('Scabies'),
                Tables\Columns\TextColumn::make('DEKU')
                    ->label('Deku'),
                Tables\Columns\TextColumn::make('SPUTUM')
                    ->label('SPUTUM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('DARAH')
                    ->label('DARAH')
                    ->searchable(),
                Tables\Columns\TextColumn::make('URINE')
                    ->label('URINE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ANTIBIOTIK')
                    ->label('ANTIBIOTIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jml')
                    ->label('Jml. Pasien')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('ETT')
                    ->label('ETT')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('CVL')
                    ->label('CVL')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('IVL')
                    ->label('IVL')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('UC')
                    ->label('UC')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('VAP')
                    ->label('VAP')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('IAD')
                    ->label('IAD')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('PLEB')
                    ->label('PLEB')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('ISK')
                    ->label('ISK')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('ILO')
                    ->label('ILO')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('HAP')
                    ->label('HAP')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('Tinea')
                    ->label('Tinea')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('Scabies')
                    ->label('Scabies')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('DEKU')
                    ->label('DEKU')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('SPUTUM')
                    ->label('SPUTUM')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('DARAH')
                    ->label('DARAH')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('URINE')
                    ->label('URINE')
                    ->summarize([
                        Sum::make(),
                    ]),
                Tables\Columns\TextColumn::make('ANTIBIOTIK')
                    ->label('ANTIBIOTIK')
                    ->summarize([
                        Sum::make(),
                    ]),
            ]);
    }
}
