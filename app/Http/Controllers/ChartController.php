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
                $data = $this->chartImageService->getInfeksiChartData($analisa->tanggal_mulai, $analisa->tanggal_selesai, $analisa->ruangan);
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
                $data = $this->chartImageService->getPemasanganChartData($analisa->tanggal_mulai, $analisa->tanggal_selesai, $analisa->ruangan);
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
}
