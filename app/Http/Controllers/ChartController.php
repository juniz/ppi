<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ChartImageService;
use App\Models\AnalisaRekomendasi;

class ChartController extends Controller
{
    protected $chartImageService;

    public function __construct(ChartImageService $chartImageService)
    {
        $this->chartImageService = $chartImageService;
    }

    public function infeksiChart(Request $request)
    {
        $analisaId = $request->get('analisa_id');
        
        if ($analisaId) {
            // Ambil data dari record yang sudah disimpan
            $analisa = AnalisaRekomendasi::find($analisaId);
            if ($analisa) {
                $data = $this->getInfeksiDataFromAnalisa($analisa);
            } else {
                $data = $this->chartImageService->getInfeksiData();
            }
        } else {
            $data = $this->chartImageService->getInfeksiData();
        }

        return view('charts.infeksi-chart', compact('data', 'analisaId'));
    }

    public function pemasanganChart(Request $request)
    {
        $analisaId = $request->get('analisa_id');
        
        if ($analisaId) {
            // Ambil data dari record yang sudah disimpan
            $analisa = AnalisaRekomendasi::find($analisaId);
            if ($analisa) {
                $data = $this->getPemasanganDataFromAnalisa($analisa);
            } else {
                $data = $this->chartImageService->getPemasanganData();
            }
        } else {
            $data = $this->chartImageService->getPemasanganData();
        }

        return view('charts.pemasangan-chart', compact('data', 'analisaId'));
    }

    public function saveChartImage(Request $request)
    {
        $imageData = $request->input('image');
        $chartType = $request->input('chart_type');
        $analisaId = $request->input('analisa_id');

        // Remove data:image/png;base64, prefix
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        
        // Generate filename
        $filename = 'chart_' . $chartType . '_' . $analisaId . '_' . time() . '.png';
        $path = 'charts/' . $filename;
        
        // Save to storage
        Storage::disk('public')->put($path, base64_decode($imageData));
        
        // Update analisa record dengan path gambar
        if ($analisaId) {
            $analisa = AnalisaRekomendasi::find($analisaId);
            if ($analisa) {
                if ($chartType === 'infeksi') {
                    $analisa->chart_infeksi_image = $path;
                } elseif ($chartType === 'pemasangan') {
                    $analisa->chart_pemasangan_image = $path;
                }
                $analisa->save();
            }
        }
        
        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    }

    private function getInfeksiDataFromAnalisa($analisa)
    {
        // Konversi data JSON menjadi format yang dibutuhkan chart
        $categories = [];
        $vapData = [];
        $iadData = [];
        $plebData = [];
        $iskData = [];
        $iloData = [];
        $hapData = [];

        // Ambil data dari JSON yang tersimpan
        $dataVAP = $analisa->data_vap ?? [];
        $dataIAD = $analisa->data_iad ?? [];
        $dataPLEB = $analisa->data_plebitis ?? [];
        $dataISK = $analisa->data_isk ?? [];
        $dataILO = $analisa->data_ilo ?? [];
        $dataHAP = $analisa->data_hap ?? [];

        // Buat categories dari salah satu data (asumsi semua data memiliki periode yang sama)
        foreach ($dataVAP as $item) {
            $categories[] = $item['bulan'] ?? '';
            $vapData[] = (float) str_replace(['‰', ' '], '', $item['laju'] ?? '0');
        }

        foreach ($dataIAD as $item) {
            $iadData[] = (float) str_replace(['‰', ' '], '', $item['laju'] ?? '0');
        }

        foreach ($dataPLEB as $item) {
            $plebData[] = (float) str_replace(['‰', ' '], '', $item['laju'] ?? '0');
        }

        foreach ($dataISK as $item) {
            $iskData[] = (float) str_replace(['‰', ' '], '', $item['laju'] ?? '0');
        }

        foreach ($dataILO as $item) {
            $iloData[] = (float) str_replace(['‰', ' '], '', $item['laju'] ?? '0');
        }

        foreach ($dataHAP as $item) {
            $hapData[] = (float) str_replace(['‰', ' '], '', $item['laju'] ?? '0');
        }

        return [
            'categories' => $categories,
            'series' => [
                'VAP' => $vapData,
                'IAD' => $iadData,
                'PLEB' => $plebData,
                'ISK' => $iskData,
                'ILO' => $iloData,
                'HAP' => $hapData,
            ],
            'colors' => ['#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6']
        ];
    }

    private function getPemasanganDataFromAnalisa($analisa)
    {
        // Konversi data JSON menjadi format yang dibutuhkan chart
        $categories = [];
        $ettData = [];
        $cvlData = [];
        $ivlData = [];
        $ucData = [];

        // Ambil data dari JSON yang tersimpan
        $dataVAP = $analisa->data_vap ?? [];
        $dataIAD = $analisa->data_iad ?? [];
        $dataISK = $analisa->data_isk ?? [];
        $dataPLEB = $analisa->data_plebitis ?? [];

        // Buat categories dari salah satu data
        foreach ($dataVAP as $item) {
            $categories[] = $item['bulan'] ?? '';
            $ettData[] = (int) ($item['hari_ventilator'] ?? 0);
        }

        foreach ($dataIAD as $item) {
            $cvlData[] = (int) ($item['hari_terpasang'] ?? 0);
        }

        foreach ($dataISK as $item) {
            $ucData[] = (int) ($item['hari_kateter'] ?? 0);
        }

        foreach ($dataPLEB as $item) {
            $ivlData[] = (int) ($item['hari_infus'] ?? 0);
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => 'ETT', 'data' => $ettData],
                ['name' => 'CVL', 'data' => $cvlData],
                ['name' => 'IVL', 'data' => $ivlData],
                ['name' => 'UC', 'data' => $ucData],
            ],
            'colors' => ['#10b981', '#06b6d4', '#8b5cf6', '#f59e0b']
        ];
    }
}
