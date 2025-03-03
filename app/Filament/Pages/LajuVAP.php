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
            ->filters([
                DateRangeFilter::make('tanggal')
                    ->label('PERIODE')
                    ->startDate(Carbon::now())
                    ->endDate(Carbon::now())
                    ->modifyQueryUsing(
                        fn(Builder $query, ?Carbon $startDate, ?Carbon $endDate, $dateString) =>
                        $query->when(
                            !empty($dateString),
                            fn(Builder $query, $date): Builder =>
                            $query->whereBetween('data_HAIs.tanggal', [$startDate, $endDate])
                        )
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
                TextColumn::make('laju_vap')
                    ->label('LAJU VAP')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('persentase')
                    ->label('PERSENTASE VAP (%)')
                    ->alignCenter()
                    ->state(function ($record, $livewire) {
                        try {
                            // Jika ini baris rangkuman
                            if ($record->nm_bangsal === 'Rangkuman') {
                                return '100.00 %';
                            }

                            // Buat query dasar dengan filter yang aktif
                            $query = DataHais::query()
                                ->join('kamar', 'data_HAIs.kd_kamar', '=', 'kamar.kd_kamar')
                                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal');

                            // Terapkan filter tanggal terlebih dahulu
                            if (isset($livewire->tableFilters['tanggal'])) {
                                $dates = $livewire->tableFilters['tanggal'];
                                if (!empty($dates['start']) && !empty($dates['end'])) {
                                    $query->whereBetween('data_HAIs.tanggal', [
                                        $dates['start'],
                                        $dates['end']
                                    ]);
                                }
                            }

                            // Kemudian hitung total bangsal yang aktif dari data terfilter
                            $totalBangsal = $query
                                ->where('data_HAIs.VAP', '>', 0)
                                ->select('bangsal.nm_bangsal')
                                ->groupBy('bangsal.nm_bangsal')
                                ->get()
                                ->count();

                            // Debug info
                            \Log::info('Total bangsal aktif: ' . $totalBangsal);
                            \Log::info('Current bangsal: ' . $record->nm_bangsal);
                            \Log::info('Filter dates: ' . json_encode($livewire->tableFilters));

                            // Jika tidak ada data
                            if ($totalBangsal === 0) {
                                return '0.00 %';
                            }

                            // Hitung persentase: 100% / jumlah bangsal aktif
                            $persentase = 100 / $totalBangsal;
                            return number_format($persentase, 2) . ' %';

                        } catch (\Exception $e) {
                            \Log::error('Error calculating percentage: ' . $e->getMessage());
                            return '0.00 %';
                        }
                    }),
            ])
            ->striped()
            ->defaultPaginationPageOption(25);
    }
}