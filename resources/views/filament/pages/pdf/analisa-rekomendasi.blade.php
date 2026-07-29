<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisa dan Rekomendasi HAIs</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #334155;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        .header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 10px 0;
            font-size: 11px;
            color: #475569;
        }
        .header h2 {
            margin: 0;
            color: #1e3a8a;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info {
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 10px 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
        }
        .info-label {
            font-weight: bold;
            width: 80px;
            color: #475569;
            display: inline-block;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        .analisa-content, .rekomendasi-content {
            background-color: #f8fafc;
            padding: 12px 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
            margin-bottom: 15px;
            color: #334155;
        }
        .rekomendasi-content {
            border-left-color: #10b981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: center;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .table-title {
            font-weight: bold;
            margin: 10px 0 5px 0;
            color: #1e293b;
            font-size: 11px;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .chart-container {
            width: 100%;
            margin-top: 10px;
            text-align: center;
        }
        .chart-box {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .chart-box h4 {
            margin: 0 0 8px 0;
            color: #1e293b;
            font-size: 12px;
        }
        .chart-box img {
            max-width: 100%;
            height: auto;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 5px;
            background: #fff;
        }
    </style>
</head>
<body>
    @php
        $setting = \App\Models\Setting::first();
        $namaRuangan = $record->ruangan === 'all' 
            ? 'Semua Ruangan' 
            : (\App\Models\Bangsal::where('kd_bangsal', $record->ruangan)->value('nm_bangsal') ?? $record->ruangan);
    @endphp

    <div class="header">
        @if($setting && $setting->nama_instansi)
            <h1>{{ strtoupper($setting->nama_instansi) }}</h1>
            <p>
                {{ $setting->alamat_instansi ?? '' }} 
                @if($setting->kabupaten) - {{ $setting->kabupaten }} @endif
                @if($setting->propinsi) - {{ $setting->propinsi }} @endif
                <br>
                @if($setting->kontak) Telp: {{ $setting->kontak }} @endif
                @if($setting->email) | Email: {{ $setting->email }} @endif
            </p>
        @endif
        <h2>Laporan Analisa dan Rekomendasi HAIs</h2>
    </div>

    <div class="info">
        <div class="info-row">
            <span class="info-label">Periode:</span>
            <span>{{ \Carbon\Carbon::parse($record->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($record->tanggal_selesai)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Ruangan:</span>
            <span>{{ $namaRuangan }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dibuat:</span>
            <span>{{ $record->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>

    @php
        $infeksiPath = $record->chart_infeksi_image ? storage_path('app/public/' . $record->chart_infeksi_image) : null;
        $infeksiBase64 = $infeksiPath && file_exists($infeksiPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($infeksiPath)) : null;

        $pemasanganPath = $record->chart_pemasangan_image ? storage_path('app/public/' . $record->chart_pemasangan_image) : null;
        $pemasanganBase64 = $pemasanganPath && file_exists($pemasanganPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pemasanganPath)) : null;
    @endphp

    @if($infeksiBase64 || $pemasanganBase64)
    <div class="section">
        <div class="section-title">Grafik HAIs</div>
        <div class="chart-container" style="display: table; width: 100%;">
            <div style="display: table-row;">
                @if($infeksiBase64)
                    <div class="chart-box" style="display: table-cell; width: 50%; padding-right: 10px; vertical-align: top;">
                        <h4>Grafik Infeksi HAIs</h4>
                        <img src="{{ $infeksiBase64 }}" alt="Grafik Infeksi HAIs">
                    </div>
                @endif

                @if($pemasanganBase64)
                    <div class="chart-box" style="display: table-cell; width: 50%; padding-left: 10px; vertical-align: top;">
                        <h4>Grafik Pemasangan Alat</h4>
                        <img src="{{ $pemasanganBase64 }}" alt="Grafik Pemasangan Alat">
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Data Detail HAIs</div>

        @if(!empty($dataHap))
        <div class="table-title">Data HAP (Hospital Acquired Pneumonia)</div>
        <table>
            <thead>
                <tr>
                    <th>Ruangan</th>
                    <th>Jumlah Pasien</th>
                    <th>Hari Rawat</th>
                    <th>HAP</th>
                    <th>Laju</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataHap as $data)
                <tr>
                    <td>{{ $data['nm_bangsal'] ?? '' }}</td>
                    <td>{{ $data['numerator'] ?? 0 }}</td>
                    <td>{{ $data['hari_rawat'] ?? 0 }}</td>
                    <td>{{ $data['denumerator'] ?? 0 }}</td>
                    <td>{{ $data['laju'] ?? '0 ‰' }}</td>
                    <td>{{ $data['persentase'] ?? '0 %' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(!empty($dataIad))
        <div class="table-title">Data IAD (Infeksi Aliran Darah)</div>
        <table>
            <thead>
                <tr>
                    <th>Ruangan</th>
                    <th>Jumlah Pasien</th>
                    <th>Hari Terpasang</th>
                    <th>IAD</th>
                    <th>Laju</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataIad as $data)
                <tr>
                    <td>{{ $data['nm_bangsal'] ?? '' }}</td>
                    <td>{{ $data['numerator'] ?? 0 }}</td>
                    <td>{{ $data['hari_terpasang'] ?? 0 }}</td>
                    <td>{{ $data['denumerator'] ?? 0 }}</td>
                    <td>{{ $data['laju'] ?? '0 ‰' }}</td>
                    <td>{{ $data['persentase'] ?? '0 %' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(!empty($dataIlo))
        <div class="table-title">Data ILO (Infeksi Luka Operasi)</div>
        <table>
            <thead>
                <tr>
                    <th>Ruangan</th>
                    <th>Jumlah Pasien</th>
                    <th>Hari Operasi</th>
                    <th>ILO</th>
                    <th>Laju</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataIlo as $data)
                <tr>
                    <td>{{ $data['nm_bangsal'] ?? '' }}</td>
                    <td>{{ $data['numerator'] ?? 0 }}</td>
                    <td>{{ $data['hari_operasi'] ?? 0 }}</td>
                    <td>{{ $data['denumerator'] ?? 0 }}</td>
                    <td>{{ $data['laju'] ?? '0 ‰' }}</td>
                    <td>{{ $data['persentase'] ?? '0 %' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(!empty($dataIsk))
        <div class="table-title">Data ISK (Infeksi Saluran Kemih)</div>
        <table>
            <thead>
                <tr>
                    <th>Ruangan</th>
                    <th>Jumlah Pasien</th>
                    <th>Hari Kateter</th>
                    <th>ISK</th>
                    <th>Laju</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataIsk as $data)
                <tr>
                    <td>{{ $data['nm_bangsal'] ?? '' }}</td>
                    <td>{{ $data['numerator'] ?? 0 }}</td>
                    <td>{{ $data['hari_kateter'] ?? 0 }}</td>
                    <td>{{ $data['denumerator'] ?? 0 }}</td>
                    <td>{{ $data['laju'] ?? '0 ‰' }}</td>
                    <td>{{ $data['persentase'] ?? '0 %' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(!empty($dataPlebitis))
        <div class="table-title">Data Plebitis</div>
        <table>
            <thead>
                <tr>
                    <th>Ruangan</th>
                    <th>Jumlah Pasien</th>
                    <th>Hari Infus</th>
                    <th>PLEB</th>
                    <th>Laju</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataPlebitis as $data)
                <tr>
                    <td>{{ $data['nm_bangsal'] ?? '' }}</td>
                    <td>{{ $data['numerator'] ?? 0 }}</td>
                    <td>{{ $data['hari_infus'] ?? 0 }}</td>
                    <td>{{ $data['denumerator'] ?? 0 }}</td>
                    <td>{{ $data['laju'] ?? '0 ‰' }}</td>
                    <td>{{ $data['persentase'] ?? '0 %' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if(!empty($dataVap))
        <div class="table-title">Data VAP (Ventilator Associated Pneumonia)</div>
        <table>
            <thead>
                <tr>
                    <th>Ruangan</th>
                    <th>Jumlah Pasien</th>
                    <th>Hari Ventilator</th>
                    <th>VAP</th>
                    <th>Laju</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataVap as $data)
                <tr>
                    <td>{{ $data['nm_bangsal'] ?? '' }}</td>
                    <td>{{ $data['numerator'] ?? 0 }}</td>
                    <td>{{ $data['hari_ventilator'] ?? 0 }}</td>
                    <td>{{ $data['denumerator'] ?? 0 }}</td>
                    <td>{{ $data['laju'] ?? '0 ‰' }}</td>
                    <td>{{ $data['persentase'] ?? '0 %' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Buat Analisis dan Rekomendasi</div>
        
        <div class="analisa-content">
            <h4 style="margin-top: 0; color: #333; margin-bottom: 8px;">Analisis</h4>
            {!! nl2br(e($record->analisa)) !!}
        </div>
        
        <div class="rekomendasi-content">
            <h4 style="margin-top: 0; color: #333; margin-bottom: 8px;">Rekomendasi</h4>
            {!! nl2br(e($record->rekomendasi)) !!}
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi HAIs</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>