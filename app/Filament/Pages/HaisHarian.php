<?php

namespace App\Filament\Pages;

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
use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianChart;
use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianInfeksiChart;
use App\Filament\Resources\DataHaisResource\Widgets\HaisHarianAlatChart;
use App\Models\DataHais;

class HaisHarian extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'HAIs Harian';
    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.hais-harian';

    protected function getHeaderWidgets(): array
    {
        return [
            HaisHarianInfeksiChart::class,
            HaisHarianAlatChart::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\DataHais::query()
                    ->with('regPeriksa.pasien')
                    ->with('kamar.bangsal')
                    ->orderByDesc('tanggal')
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
                Tables\Columns\TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime('d-m-Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regPeriksa.pasien.jk')
                    ->label('JK')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('Deku')
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
                Tables\Columns\TextColumn::make('kamar.bangsal.nm_bangsal')
                    ->label('Bangsal')
                    ->searchable(),
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
            ]);
    }
}
