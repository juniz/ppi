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
                        DB::raw('COUNT(SELECT DISTINCT * FROM kamar WHERE kd_bangsal = bangsal.kd_bangsal) / 100 as persentase')
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
                    ->formatStateUsing(fn($state) => number_format($state, 2)),
                TextColumn::make('persentase')
                    ->label('PERSENTASE VAP (%)')
                    ->alignCenter()
                // ->state(function ($record) {
                //     try {
                //         // Ambil tanggal dari filter yang aktif
                //         $filter = $this->getTableFilters()['tanggal']->getState();
                //         $startDate = $filter['start'] ?? null;
                //         $endDate = $filter['end'] ?? null;

                //         // Debug log untuk filter tanggal
                //         \Log::info('Filter dates:', ['start' => $startDate, 'end' => $endDate]);

                //         // Hitung jumlah ruang dari tabel audit_bundle_vap dengan filter tanggal
                //         $query = DB::table('audit_bundle_vap')
                //             ->distinct();

                //         // Tambahkan filter tanggal jika ada
                //         if ($startDate && $endDate) {
                //             $query->whereBetween('tanggal', [$startDate, $endDate]);
                //         }

                //         $jumlahRuang = $query->count('id_ruang');

                //         // Debug log untuk jumlah ruang
                //         \Log::info('Jumlah ruang:', ['count' => $jumlahRuang]);

                //         // Jika tidak ada ruang
                //         if ($jumlahRuang === 0) {
                //             \Log::warning('Tidak ada ruang ditemukan');
                //             return '0 %';
                //         }

                //         // Hitung persentase: 100% dibagi jumlah ruang
                //         $persentase = (int)(100 / $jumlahRuang);

                //         // Debug log untuk hasil perhitungan
                //         \Log::info('Hasil perhitungan:', [
                //             'jumlah_ruang' => $jumlahRuang,
                //             'persentase' => $persentase
                //         ]);

                //         return $persentase . ' %';
                //     } catch (\Exception $e) {
                //         \Log::error('Error calculating VAP percentage: ' . $e->getMessage());
                //         return '0 %';
                //     }
                // }),
            ])
            ->striped()
            ->defaultPaginationPageOption(25);
    }
}
