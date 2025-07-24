<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laju HAIs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Laju HAIs</h2>
    </div>

    <div class="info">
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d/m/Y') }}</p>
        <p><strong>Ruangan:</strong> {{ $ruangan }}</p>
    </div>

    <div class="section-title">Laju HAP (Healthcare-Associated Pneumonia)</div>
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
            @foreach($dataHAP as $data)
            <tr>
                <td>{{ $data->nm_bangsal }}</td>
                <td>{{ $data->numerator }}</td>
                <td>{{ $data->hari_rawat }}</td>
                <td>{{ $data->denumerator }}</td>
                <td>{{ $data->laju }}</td>
                <td>{{ $data->persentase }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Laju IAD (Infeksi Aliran Darah)</div>
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
            @foreach($dataIAD as $data)
            <tr>
                <td>{{ $data->nm_bangsal }}</td>
                <td>{{ $data->numerator }}</td>
                <td>{{ $data->hari_terpasang }}</td>
                <td>{{ $data->denumerator }}</td>
                <td>{{ $data->laju }}</td>
                <td>{{ $data->persentase }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Laju ILO (Infeksi Luka Operasi)</div>
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
            @foreach($dataILO as $data)
            <tr>
                <td>{{ $data->nm_bangsal }}</td>
                <td>{{ $data->numerator }}</td>
                <td>{{ $data->hari_operasi }}</td>
                <td>{{ $data->denumerator }}</td>
                <td>{{ $data->laju }}</td>
                <td>{{ $data->persentase }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Laju ISK (Infeksi Saluran Kemih)</div>
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
            @foreach($dataISK as $data)
            <tr>
                <td>{{ $data->nm_bangsal }}</td>
                <td>{{ $data->numerator }}</td>
                <td>{{ $data->hari_kateter }}</td>
                <td>{{ $data->denumerator }}</td>
                <td>{{ $data->laju }}</td>
                <td>{{ $data->persentase }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Laju PLEBITIS</div>
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
            @foreach($dataPLEB as $data)
            <tr>
                <td>{{ $data->nm_bangsal }}</td>
                <td>{{ $data->numerator }}</td>
                <td>{{ $data->hari_infus }}</td>
                <td>{{ $data->denumerator }}</td>
                <td>{{ $data->laju }}</td>
                <td>{{ $data->persentase }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Laju VAP (Ventilator-Associated Pneumonia)</div>
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
            @foreach($dataVAP as $data)
            <tr>
                <td>{{ $data->nm_bangsal }}</td>
                <td>{{ $data->numerator }}</td>
                <td>{{ $data->hari_ventilator }}</td>
                <td>{{ $data->denumerator }}</td>
                <td>{{ $data->laju }}</td>
                <td>{{ $data->persentase }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>