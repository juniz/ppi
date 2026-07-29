<?php

namespace App\Http\Controllers;

use App\Models\AnalisaRekomendasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

        $filename = 'analisa-rekomendasi-' . $record->tanggal_mulai->format('Y-m-d') . '-' . $record->tanggal_selesai->format('Y-m-d') . '.pdf';

        // Menggunakan array ['Attachment' => false] agar file dirender inline di browser (tab baru)
        return $pdf->stream($filename, ['Attachment' => false]);
    }
}
