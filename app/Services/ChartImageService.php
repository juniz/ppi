<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChartImageService
{
    public function generateChartImages($tanggalMulai, $tanggalSelesai, $ruangan)
    {
        $infeksiChartData = $this->getInfeksiChartData($tanggalMulai, $tanggalSelesai, $ruangan);
        $pemasanganChartData = $this->getPemasanganChartData($tanggalMulai, $tanggalSelesai, $ruangan);
        
        // Generate unique filenames
        $timestamp = now()->format('Y-m-d_H-i-s');
        $infeksiFilename = "chart_infeksi_{$timestamp}.png";
        $pemasanganFilename = "chart_pemasangan_{$timestamp}.png";
        
        // Create chart HTML templates
        $infeksiHtml = $this->createInfeksiChartHtml($infeksiChartData);
        $pemasanganHtml = $this->createPemasanganChartHtml($pemasanganChartData);
        
        // For now, we'll return the data structure that can be used to generate images on frontend
        return [
            'infeksi_data' => $infeksiChartData,
            'pemasangan_data' => $pemasanganChartData,
            'infeksi_filename' => $infeksiFilename,
            'pemasangan_filename' => $pemasanganFilename,
        ];
    }
    
    private function getInfeksiChartData($tanggalMulai, $tanggalSelesai, $ruangan)
    {
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

        $categories = [];
        $series = [
            'VAP' => [],
            'IAD' => [],
            'PLEB' => [],
            'ISK' => [],
            'ILO' => [],
            'HAP' => []
        ];

        if ($data->isEmpty()) {
            $categories = ['Tidak ada data'];
            foreach ($series as $key => $value) {
                $series[$key] = [0];
            }
        } else {
            foreach ($data as $item) {
                $monthName = Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('M Y');
                $categories[] = $monthName;
                $series['VAP'][] = $item->vap_count;
                $series['IAD'][] = $item->iad_count;
                $series['PLEB'][] = $item->pleb_count;
                $series['ISK'][] = $item->isk_count;
                $series['ILO'][] = $item->ilo_count;
                $series['HAP'][] = $item->hap_count;
            }
        }

        return [
            'categories' => $categories,
            'series' => $series,
            'colors' => ['#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6']
        ];
    }
    
    private function getPemasanganChartData($tanggalMulai, $tanggalSelesai, $ruangan)
    {
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

        $categories = [];
        $series = [
            'ETT' => [],
            'CVL' => [],
            'IVL' => [],
            'UC' => []
        ];

        if ($data->isEmpty()) {
            $categories = ['Tidak ada data'];
            foreach ($series as $key => $value) {
                $series[$key] = [0];
            }
        } else {
            foreach ($data as $item) {
                $monthName = Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('M Y');
                $categories[] = $monthName;
                $series['ETT'][] = $item->ett_total;
                $series['CVL'][] = $item->cvl_total;
                $series['IVL'][] = $item->ivl_total;
                $series['UC'][] = $item->uc_total;
            }
        }

        return [
            'categories' => $categories,
            'series' => $series,
            'colors' => ['#10b981', '#06b6d4', '#8b5cf6', '#f59e0b']
        ];
    }
    
    private function createInfeksiChartHtml($data)
    {
        return view('charts.infeksi-chart', compact('data'))->render();
    }
    
    private function createPemasanganChartHtml($data)
    {
        return view('charts.pemasangan-chart', compact('data'))->render();
    }

    public function getInfeksiData()
    {
        // Return default/empty data structure for infeksi chart
        return [
            'categories' => ['Tidak ada data'],
            'series' => [
                'VAP' => [0],
                'IAD' => [0],
                'PLEB' => [0],
                'ISK' => [0],
                'ILO' => [0],
                'HAP' => [0],
            ],
            'colors' => ['#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6']
        ];
    }

    public function getPemasanganData()
    {
        // Return default/empty data structure for pemasangan chart
        return [
            'categories' => ['Tidak ada data'],
            'series' => [
                ['name' => 'ETT', 'data' => [0]],
                ['name' => 'CVL', 'data' => [0]],
                ['name' => 'IVL', 'data' => [0]],
                ['name' => 'UC', 'data' => [0]],
            ],
            'colors' => ['#10b981', '#06b6d4', '#8b5cf6', '#f59e0b']
        ];
    }
}