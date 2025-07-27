<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AnalisaPemasanganAlatChart extends ApexChartWidget
{
    protected static ?string $chartId = 'analisaPemasanganAlatChart';
    protected static ?string $heading = 'Grafik Garis Pemasangan Alat';
    protected int | string | array $columnSpan = 1;

    protected function getOptions(): array
    {
        // Ambil filter dari session yang diset oleh halaman AnalisaLajuHAIs
        $tanggalMulai = session('tanggal_mulai');
        $tanggalSelesai = session('tanggal_selesai');
        $ruangan = session('ruangan');
        
        // Fallback jika session kosong
        if (!$tanggalMulai || !$tanggalSelesai) {
            $tanggalMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // Query data berdasarkan bulan
        $query = DB::table('data_HAIs')
            ->join('kamar', 'data_HAIs.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->whereBetween('data_HAIs.tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($ruangan && $ruangan !== 'all' && $ruangan !== null) {
            $query->where('bangsal.kd_bangsal', $ruangan);
        }

        $data = $query
            ->selectRaw('
                YEAR(data_HAIs.tanggal) as tahun,
                MONTH(data_HAIs.tanggal) as bulan,
                SUM(data_HAIs.ETT) as ett_total,
                SUM(data_HAIs.CVL) as cvl_total,
                SUM(data_HAIs.IVL) as ivl_total,
                SUM(data_HAIs.UC) as uc_total
            ')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Siapkan data untuk chart
        $categories = [];
        $ettData = [];
        $cvlData = [];
        $ivlData = [];
        $ucData = [];

        if ($data->isEmpty()) {
            // Jika tidak ada data, tampilkan pesan
            $categories = ['Tidak ada data'];
            $ettData = [0];
            $cvlData = [0];
            $ivlData = [0];
            $ucData = [0];
        } else {
            foreach ($data as $item) {
                $monthName = Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('M Y');
                $categories[] = $monthName;
                $ettData[] = $item->ett_total;
                $cvlData[] = $item->cvl_total;
                $ivlData[] = $item->ivl_total;
                $ucData[] = $item->uc_total;
            }
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
            ],
            'series' => [
                [
                    'name' => 'ETT',
                    'data' => $ettData,
                ],
                [
                    'name' => 'CVL',
                    'data' => $cvlData,
                ],
                [
                    'name' => 'IVL',
                    'data' => $ivlData,
                ],
                [
                    'name' => 'UC',
                    'data' => $ucData,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
            ],
            'colors' => ['#10b981', '#06b6d4', '#8b5cf6', '#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
            ],
            'legend' => [
                'show' => true,
                'position' => 'bottom',
                'horizontalAlign' => 'center',
                'fontSize' => '12px',
                'offsetY' => 0,
                'itemMargin' => [
                    'horizontal' => 10,
                    'vertical' => 30
                ]
            ],
        ];
    }
}