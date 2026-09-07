<?php

namespace App\Http\Controllers;

use App\Models\AnalisaRekomendasi;
use App\Models\Bangsal;
use App\Models\DataHais;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function exportPdfAnalisaRekomendasi(Request $request, $id)
    {
        $record = AnalisaRekomendasi::findOrFail($id);

        // Decode JSON data
        $dataHap = is_string($record->data_hap) ? json_decode($record->data_hap, true) : $record->data_hap;
        $dataIad = is_string($record->data_iad) ? json_decode($record->data_iad, true) : $record->data_iad;
        $dataIlo = is_string($record->data_ilo) ? json_decode($record->data_ilo, true) : $record->data_ilo;
        $dataIsk = is_string($record->data_isk) ? json_decode($record->data_isk, true) : $record->data_isk;
        $dataPlebitis = is_string($record->data_plebitis) ? json_decode($record->data_plebitis, true) : $record->data_plebitis;
        $dataVap = is_string($record->data_vap) ? json_decode($record->data_vap, true) : $record->data_vap;

        $data = [
            'record' => $record,
            'dataHap' => $dataHap ?? [],
            'dataIad' => $dataIad ?? [],
            'dataIlo' => $dataIlo ?? [],
            'dataIsk' => $dataIsk ?? [],
            'dataPlebitis' => $dataPlebitis ?? [],
            'dataVap' => $dataVap ?? [],
        ];

        $pdf = Pdf::loadView('filament.pages.pdf.analisa-rekomendasi', $data)
                  ->setPaper('a4', 'portrait');

        // Dapatkan nama ruangan asli
        $namaRuangan = $record->ruangan === 'all' 
            ? 'semua-ruangan' 
            : (\App\Models\Bangsal::where('kd_bangsal', $record->ruangan)->value('nm_bangsal') ?? $record->ruangan);
        
        // Buat format nama file yang rapi (hilangkan spasi atau ganti dengan strip)
        $namaRuanganFormatted = Str::slug($namaRuangan);

        $filename = 'analisa-rekomendasi-' . $namaRuanganFormatted . '-' . $record->tanggal_mulai->format('Y-m-d') . '-sd-' . $record->tanggal_selesai->format('Y-m-d') . '.pdf';

        // Menggunakan array ['Attachment' => false] agar file dirender inline di browser (tab baru)
        return $pdf->stream($filename, ['Attachment' => false]);
    }

    public function exportPdfHaisHarian(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal') ?: Carbon::today()->toDateString();
        $sampaiTanggal = $request->input('sampai_tanggal') ?: Carbon::today()->toDateString();
        $kdBangsal = $request->input('kd_bangsal');
        $search = $request->input('search');

        $query = DataHais::query()
            ->with(['regPeriksa.pasien', 'kamar.bangsal'])
            ->when(!empty($dariTanggal), fn($q) => $q->whereDate('tanggal', '>=', $dariTanggal))
            ->when(!empty($sampaiTanggal), fn($q) => $q->whereDate('tanggal', '<=', $sampaiTanggal))
            ->when(!empty($kdBangsal), fn($q) => $q->whereHas('kamar', fn($k) => $k->where('kd_bangsal', $kdBangsal)))
            ->when(!empty(trim($search ?? '')), function ($q) use ($search) {
                $search = trim($search);
                $q->where(function ($query) use ($search) {
                    $query->whereHas('regPeriksa.pasien', fn($p) => $p->where('nm_pasien', 'like', "%{$search}%"))
                          ->orWhereHas('regPeriksa', fn($r) => $r->where('no_rkm_medis', 'like', "%{$search}%"))
                          ->orWhere('no_rawat', 'like', "%{$search}%")
                          ->orWhereHas('kamar', fn($k) => $k->where('kd_kamar', 'like', "%{$search}%"));
                });
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('no_rawat', 'asc');

        $records = $query->get();

        $setting = Setting::first();
        $namaBangsal = $kdBangsal ? (Bangsal::where('kd_bangsal', $kdBangsal)->value('nm_bangsal') ?? $kdBangsal) : 'Semua Ruangan / Bangsal';

        $totals = [
            'ETT' => (int) $records->sum('ETT'),
            'CVL' => (int) $records->sum('CVL'),
            'IVL' => (int) $records->sum('IVL'),
            'UC' => (int) $records->sum('UC'),
            'VAP' => (int) $records->sum('VAP'),
            'IAD' => (int) $records->sum('IAD'),
            'PLEB' => (int) $records->sum('PLEB'),
            'ISK' => (int) $records->sum('ISK'),
            'ILO' => (int) $records->sum('ILO'),
            'HAP' => (int) $records->sum('HAP'),
            'Tinea' => (int) $records->sum('Tinea'),
            'Scabies' => (int) $records->sum('Scabies'),
            'Dekubitus' => (int) $records->where('Deku', 'IYA')->count(),
        ];

        $data = [
            'records' => $records,
            'setting' => $setting,
            'dariTanggal' => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal,
            'namaBangsal' => $namaBangsal,
            'totals' => $totals,
            'tanggalCetak' => Carbon::now()->translatedFormat('d F Y H:i'),
        ];

        $pdf = Pdf::loadView('filament.pages.pdf.hais-harian', $data)
            ->setPaper('a4', 'landscape');

        $bangsalSlug = Str::slug($namaBangsal);
        $filename = 'Laporan-HAIs-Harian-' . $bangsalSlug . '-' . Carbon::parse($dariTanggal)->format('Ymd') . '-sd-' . Carbon::parse($sampaiTanggal)->format('Ymd') . '.pdf';

        return $pdf->stream($filename, ['Attachment' => false]);
    }
}
