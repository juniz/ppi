<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AnalisaInfeksiChart extends ApexChartWidget
{
    protected static ?string $chartId = 'analisaInfeksiChart';
    protected static ?string $heading = 'Grafik Garis Infeksi HAIs';
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
                SUM(data_HAIs.VAP) as vap_count,
                SUM(data_HAIs.IAD) as iad_count,
                SUM(data_HAIs.PLEB) as pleb_count,
                SUM(data_HAIs.ISK) as isk_count,
                SUM(data_HAIs.ILO) as ilo_count,
                SUM(data_HAIs.HAP) as hap_count
            ')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Siapkan data untuk chart
        $categories = [];
        $vapData = [];
        $iadData = [];
        $plebData = [];
        $iskData = [];
        $iloData = [];
        $hapData = [];

        if ($data->isEmpty()) {
            // Jika tidak ada data, tampilkan pesan
            $categories = ['Tidak ada data'];
            $vapData = [0];
            $iadData = [0];
            $plebData = [0];
            $iskData = [0];
            $iloData = [0];
            $hapData = [0];
        } else {
            foreach ($data as $item) {
                $monthName = Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('M Y');
                $categories[] = $monthName;
                $vapData[] = $item->vap_count;
                $iadData[] = $item->iad_count;
                $plebData[] = $item->pleb_count;
                $iskData[] = $item->isk_count;
                $iloData[] = $item->ilo_count;
                $hapData[] = $item->hap_count;
            }
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
            ],
            'series' => [
                [
                    'name' => 'VAP',
                    'data' => $vapData,
                ],
                [
                    'name' => 'IAD',
                    'data' => $iadData,
                ],
                [
                    'name' => 'PLEB',
                    'data' => $plebData,
                ],
                [
                    'name' => 'ISK',
                    'data' => $iskData,
                ],
                [
                    'name' => 'ILO',
                    'data' => $iloData,
                ],
                [
                    'name' => 'HAP',
                    'data' => $hapData,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
            ],
            'colors' => ['#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6'],
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