<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Surveilans HAIs Harian</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
            font-size: 8.5pt;
            line-height: 1.2;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border-bottom: 2.5px solid #0284c7;
            padding-bottom: 6px;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .instansi-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .instansi-subtitle {
            font-size: 8pt;
            color: #475569;
            margin-top: 3px;
            line-height: 1.3;
        }
        .report-title {
            text-align: center;
            margin: 8px 0 10px 0;
        }
        .report-title h2 {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        .meta-table td {
            padding: 5px 10px;
            font-size: 8pt;
            border: none;
        }
        .meta-label {
            color: #64748b;
            font-weight: 500;
        }
        .meta-val {
            color: #0f172a;
            font-weight: bold;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 12px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
        }
        .data-table th {
            font-weight: bold;
            color: #0f172a;
        }
        .th-group-alat {
            background-color: #e0f2fe;
            color: #0369a1;
            font-size: 8pt;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #0284c7 !important;
        }
        .th-group-infeksi {
            background-color: #ffe4e6;
            color: #be123c;
            font-size: 8pt;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e11d48 !important;
        }
        .th-group-deku {
            background-color: #fef3c7;
            color: #b45309;
            font-size: 8pt;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #f59e0b !important;
        }
        .th-group-kultur {
            background-color: #ecfdf5;
            color: #047857;
            font-size: 8pt;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #059669 !important;
        }
        .th-sub {
            background-color: #f1f5f9;
            font-size: 7pt;
            text-transform: uppercase;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .td-pasien {
            text-align: left !important;
            padding-left: 5px !important;
        }
        .badge-alat {
            display: inline-block;
            background-color: #dbeafe;
            color: #000000;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 3px;
            border: 1px solid #93c5fd;
        }
        .badge-infeksi {
            display: inline-block;
            background-color: #fecdd3;
            color: #000000;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 3px;
            border: 1px solid #f43f5e;
        }
        .badge-deku {
            display: inline-block;
            background-color: #fed7aa;
            color: #000000;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 3px;
        }
        .dash {
            color: #94a3b8;
        }
        .tfoot-total td {
            background-color: #e2e8f0;
            font-weight: bold;
            color: #000000;
            border-top: 2px solid #94a3b8;
            font-size: 8pt;
        }

        /* Signature section */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .signature-table td {
            border: none;
            padding: 0;
            vertical-align: top;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <!-- KOP SURAT INSTANSI -->
    <table class="kop-table">
        <tr>
            <td style="text-align: center;">
                <div class="instansi-title">{{ $setting->nama_instansi ?? 'RUMAH SAKIT UMUM DAERAH' }}</div>
                <div class="instansi-subtitle">
                    {{ $setting->alamat_instansi ?? '' }}
                    @if(!empty($setting->kabupaten)) • {{ $setting->kabupaten }} @endif
                    @if(!empty($setting->propinsi)) • {{ $setting->propinsi }} @endif
                    @if(!empty($setting->kontak)) • Telp: {{ $setting->kontak }} @endif
                    @if(!empty($setting->email)) • Email: {{ $setting->email }} @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- JUDUL LAPORAN -->
    <div class="report-title">
        <h2>LAPORAN SURVEILANS HARIAN HEALTHCARE-ASSOCIATED INFECTIONS (HAIs)</h2>
    </div>

    <!-- METADATA LAPORAN -->
    <table class="meta-table">
        <tr>
            <td style="width: 35%;">
                <span class="meta-label">Periode:</span> 
                <span class="meta-val">{{ \Carbon\Carbon::parse($dariTanggal)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampaiTanggal)->translatedFormat('d F Y') }}</span>
            </td>
            <td style="width: 35%;">
                <span class="meta-label">Ruangan / Bangsal:</span> 
                <span class="meta-val">{{ $namaBangsal }}</span>
            </td>
            <td style="width: 30%; text-align: right;">
                <span class="meta-label">Dicetak Pada:</span> 
                <span class="meta-val">{{ $tanggalCetak }}</span>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA HAIs HARIAN -->
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2.5%;">No</th>
                <th rowspan="2" style="width: 7%;">Tanggal</th>
                <th rowspan="2" style="width: 10%;">No. RM / Rawat</th>
                <th rowspan="2" style="width: 13%;">Nama Pasien</th>
                <th rowspan="2" style="width: 3%;">JK</th>
                <th rowspan="2" style="width: 8%;">Ruangan</th>
                <th colspan="4" class="th-group-alat">PEMASANGAN ALAT</th>
                <th colspan="8" class="th-group-infeksi">INFEKSI HAIs</th>
                <th rowspan="2" class="th-group-deku" style="width: 4%;">Deku</th>
                <th colspan="3" class="th-group-kultur">KULTUR & ANTIBIOTIK</th>
            </tr>
            <tr>
                <!-- Pemasangan Alat -->
                <th class="th-sub" style="width: 3.2%;">ETT</th>
                <th class="th-sub" style="width: 3.2%;">CVL</th>
                <th class="th-sub" style="width: 3.2%;">IVL</th>
                <th class="th-sub" style="width: 3.2%;">UC</th>
                <!-- Infeksi HAIs -->
                <th class="th-sub" style="width: 3.2%;">VAP</th>
                <th class="th-sub" style="width: 3.2%;">IAD</th>
                <th class="th-sub" style="width: 3.2%;">PLEB</th>
                <th class="th-sub" style="width: 3.5%;">CAUTI</th>
                <th class="th-sub" style="width: 3.2%;">ILO</th>
                <th class="th-sub" style="width: 3.2%;">HAP</th>
                <th class="th-sub" style="width: 3.2%;">Tinea</th>
                <th class="th-sub" style="width: 3.2%;">Scab</th>
                <!-- Kultur -->
                <th class="th-sub" style="width: 4.5%;">Sputum</th>
                <th class="th-sub" style="width: 4.5%;">Darah</th>
                <th class="th-sub" style="width: 4.5%;">Urine</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
                @php
                    $pasien = $row->regPeriksa?->pasien;
                    $noRm = $row->regPeriksa?->no_rkm_medis ?? '-';
                    $nmPasien = $pasien?->nm_pasien ?? '-';
                    $jk = $pasien?->jk ?? '-';
                    $nmBangsalRow = $row->kamar?->bangsal?->nm_bangsal ?? ($row->kd_kamar ?? '-');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td style="font-size: 7pt;">
                        <strong>{{ $noRm }}</strong><br>
                        <span style="color: #64748b;">{{ $row->no_rawat }}</span>
                    </td>
                    <td class="td-pasien">
                        <strong>{{ $nmPasien }}</strong>
                    </td>
                    <td>{{ $jk }}</td>
                    <td style="font-size: 7pt; text-align: left; padding-left: 4px;">{{ Str::limit($nmBangsalRow, 15) }}</td>
                    
                    <!-- Pemasangan Alat -->
                    <td>
                        @if((int)$row->ETT > 0)
                            <span class="badge-alat">{{ $row->ETT }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->CVL > 0)
                            <span class="badge-alat">{{ $row->CVL }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->IVL > 0)
                            <span class="badge-alat">{{ $row->IVL }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->UC > 0)
                            <span class="badge-alat">{{ $row->UC }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>

                    <!-- Infeksi HAIs -->
                    <td>
                        @if((int)$row->VAP > 0)
                            <span class="badge-infeksi">{{ $row->VAP }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->IAD > 0)
                            <span class="badge-infeksi">{{ $row->IAD }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->PLEB > 0)
                            <span class="badge-infeksi">{{ $row->PLEB }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->ISK > 0)
                            <span class="badge-infeksi">{{ $row->ISK }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->ILO > 0)
                            <span class="badge-infeksi">{{ $row->ILO }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->HAP > 0)
                            <span class="badge-infeksi">{{ $row->HAP }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->Tinea > 0)
                            <span class="badge-infeksi">{{ $row->Tinea }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if((int)$row->Scabies > 0)
                            <span class="badge-infeksi">{{ $row->Scabies }}</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>

                    <!-- Dekubitus -->
                    <td>
                        @if($row->Deku === 'IYA')
                            <span class="badge-deku">IYA</span>
                        @else
                            <span class="dash">—</span>
                        @endif
                    </td>

                    <!-- Kultur -->
                    <td style="font-size: 6.5pt;">{{ !empty($row->SPUTUM) ? Str::limit($row->SPUTUM, 10) : '—' }}</td>
                    <td style="font-size: 6.5pt;">{{ !empty($row->DARAH) ? Str::limit($row->DARAH, 10) : '—' }}</td>
                    <td style="font-size: 6.5pt;">{{ !empty($row->URINE) ? Str::limit($row->URINE, 10) : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="22" style="padding: 15px; color: #64748b; font-style: italic;">
                        Tidak ada data surveilans HAIs harian pada periode dan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="tfoot-total">
                <td colspan="6" style="text-align: right; padding-right: 8px;">TOTAL / RANGKUMAN:</td>
                <td>{{ $totals['ETT'] }}</td>
                <td>{{ $totals['CVL'] }}</td>
                <td>{{ $totals['IVL'] }}</td>
                <td>{{ $totals['UC'] }}</td>
                <td>{{ $totals['VAP'] }}</td>
                <td>{{ $totals['IAD'] }}</td>
                <td>{{ $totals['PLEB'] }}</td>
                <td>{{ $totals['ISK'] }}</td>
                <td>{{ $totals['ILO'] }}</td>
                <td>{{ $totals['HAP'] }}</td>
                <td>{{ $totals['Tinea'] }}</td>
                <td>{{ $totals['Scabies'] }}</td>
                <td>{{ $totals['Dekubitus'] }}</td>
                <td colspan="3" style="background-color: #e2e8f0;">—</td>
            </tr>
        </tfoot>
    </table>

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td style="width: 40%; text-align: center;">
                <div>Mengetahui / Melaporkan,</div>
                <div style="font-weight: bold; margin-top: 3px;">IPCLN (Perawat Penghubung PPI)</div>
                <div style="color: #64748b; font-size: 7.5pt;">{{ $namaBangsal !== 'Semua Ruangan / Bangsal' ? $namaBangsal : 'Ruangan / Bangsal Perawatan' }}</div>
                <div style="height: 50px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">( .................................................... )</div>
                <div style="color: #64748b; font-size: 7pt; margin-top: 2px;">NIP. ....................................................</div>
            </td>
            <td style="width: 20%; text-align: center; vertical-align: bottom;">
                <div style="font-size: 7pt; color: #94a3b8; padding-bottom: 8px;">
                    <em>Dokumen surveilans resmi<br>SI-HAIs Rumah Sakit</em>
                </div>
            </td>
            <td style="width: 40%; text-align: center;">
                <div>{{ $setting->kabupaten ?? 'Madiun' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 3px;">Petugas Surveilans IPCN / PPI</div>
                <div style="color: #64748b; font-size: 7.5pt;">Komite Pencegahan & Pengendalian Infeksi</div>
                <div style="height: 50px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">( .................................................... )</div>
                <div style="color: #64748b; font-size: 7pt; margin-top: 2px;">NIP. ....................................................</div>
            </td>
        </tr>
    </table>

    <!-- PAGE NUMBER SCRIPT FOR DOMPDF -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 7.5;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $pdf->page_text(842 - 12 - $width, 580, $text, $font, $size, array(0.4, 0.4, 0.4));
            $pdf->page_text(12, 580, "SI-HAIs • Dokumen Resmi Surveilans Pencegahan dan Pengendalian Infeksi", $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>
</body>
</html>
