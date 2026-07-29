<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laju HAIs</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #334155;
            font-size: 11px;
        }
        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        .header h2 {
            margin: 0;
            color: #1e3a8a;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info {
            margin-bottom: 25px;
            background-color: #f8fafc;
            padding: 10px 15px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        .info p {
            margin: 4px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 8px 10px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #cbd5e1;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        tfoot th, tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #cbd5e1;
            border-bottom: none;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
            color: #0f172a;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    @php
        $setting = \App\Models\Setting::first();
    @endphp
    <div class="header">
        @if($setting && $setting->nama_instansi)
            <h1 style="margin: 0; color: #0f172a; font-size: 22px;">{{ strtoupper($setting->nama_instansi) }}</h1>
            <p style="margin: 4px 0 15px 0; font-size: 11px; color: #475569;">
                {{ $setting->alamat_instansi ?? '' }} 
                @if($setting->kabupaten) - {{ $setting->kabupaten }} @endif
                @if($setting->propinsi) - {{ $setting->propinsi }} @endif
                <br>
                @if($setting->kontak) Telp: {{ $setting->kontak }} @endif
                @if($setting->email) | Email: {{ $setting->email }} @endif
            </p>
        @endif
        <h2>Laporan Analisa Laju HAIs</h2>
    </div>

    <div class="info">
        <p><strong>Periode Laporan:</strong> {{ \Carbon\Carbon::parse($tanggal_mulai)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($tanggal_selesai)->translatedFormat('d F Y') }}</p>
        <p><strong>Ruangan:</strong> {{ $ruangan }}</p>
    </div>

    @include('filament.pages.pdf.partials.table', ['data' => $dataHAP, 'type' => 'HAP', 'title' => 'HAP (Healthcare-Associated Pneumonia)'])
    @include('filament.pages.pdf.partials.table', ['data' => $dataIAD, 'type' => 'IAD', 'title' => 'IAD (Infeksi Aliran Darah)'])
    @include('filament.pages.pdf.partials.table', ['data' => $dataILO, 'type' => 'ILO', 'title' => 'ILO (Infeksi Luka Operasi)'])
    @include('filament.pages.pdf.partials.table', ['data' => $dataISK, 'type' => 'ISK', 'title' => 'ISK (Infeksi Saluran Kemih)'])
    @include('filament.pages.pdf.partials.table', ['data' => $dataPLEB, 'type' => 'PLEB', 'title' => 'PLEBITIS'])
    @include('filament.pages.pdf.partials.table', ['data' => $dataVAP, 'type' => 'VAP', 'title' => 'VAP (Ventilator-Associated Pneumonia)'])

</body>
</html>
