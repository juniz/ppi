<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisa dan Rekomendasi HAIs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .info {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .analisa-content, .rekomendasi-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .rekomendasi-content {
            border-left-color: #28a745;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 12px;
            background-color: #fff;
        }
        .summary-card h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #333;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            font-size: 10px;
        }
        .stat-item {
            text-align: center;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 3px;
        }
        .stat-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 2px;
        }
        .stat-value {
            font-size: 11px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
        }
        .table-title {
            font-weight: bold;
            margin: 15px 0 8px 0;
            color: #333;
            font-size: 11px;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .hap-color { background-color: #e3f2fd; }
        .iad-color { background-color: #e8f5e8; }
        .ilo-color { background-color: #fff3e0; }
        .isk-color { background-color: #f3e5f5; }
        .plebitis-color { background-color: #ffebee; }
        .vap-color { background-color: #e0f2f1; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Analisa dan Rekomendasi HAIs</h1>
        <p style="margin: 5px 0 0 0; color: #666;">Healthcare-Associated Infections Analysis & Recommendations</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span class="info-label">Periode:</span>
            <span>{{ \Carbon\Carbon::parse($record->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($record->tanggal_selesai)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Ruangan:</span>
            <span>{{ $record->ruangan }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dibuat:</span>
            <span>{{ $record->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Analisa</div>
        <div class="analisa-content">
            {!! nl2br(e($record->analisa)) !!}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Rekomendasi</div>
        <div class="rekomendasi-content">
            {!! nl2br(e($record->rekomendasi)) !!}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Data HAIs</div>
        <div class="summary-grid">
            <!-- HAP -->
            <div class="summary-card hap-color">
                <h4>Hospital Acquired Pneumonia (HAP)</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-label">Total Kasus</div>
                        <div class="stat-value">{{ $record->total_hap_kasus ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Hari Rawat</div>
                        <div class="stat-value">{{ $record->total_hap_hari_rawat ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata Laju</div>
                        <div class="stat-value">{{ number_format($record->rata_hap_laju ?? 0, 2) }}‰</div>
                    </div>
                </div>
            </div>

            <!-- IAD -->
            <div class="summary-card iad-color">
                <h4>Infeksi Aliran Darah (IAD)</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-label">Total Kasus</div>
                        <div class="stat-value">{{ $record->total_iad_kasus ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Hari Terpasang</div>
                        <div class="stat-value">{{ $record->total_iad_hari_terpasang ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata Laju</div>
                        <div class="stat-value">{{ number_format($record->rata_iad_laju ?? 0, 2) }}‰</div>
                    </div>
                </div>
            </div>

            <!-- ILO -->
            <div class="summary-card ilo-color">
                <h4>Infeksi Luka Operasi (ILO)</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-label">Total Kasus</div>
                        <div class="stat-value">{{ $record->total_ilo_kasus ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Hari Operasi</div>
                        <div class="stat-value">{{ $record->total_ilo_hari_operasi ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata Laju</div>
                        <div class="stat-value">{{ number_format($record->rata_ilo_laju ?? 0, 2) }}%</div>
                    </div>
                </div>
            </div>

            <!-- ISK -->
            <div class="summary-card isk-color">
                <h4>Infeksi Saluran Kemih (ISK)</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-label">Total Kasus</div>
                        <div class="stat-value">{{ $record->total_isk_kasus ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Hari Kateter</div>
                        <div class="stat-value">{{ $record->total_isk_hari_kateter ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata Laju</div>
                        <div class="stat-value">{{ number_format($record->rata_isk_laju ?? 0, 2) }}‰</div>
                    </div>
                </div>
            </div>

            <!-- Plebitis -->
            <div class="summary-card plebitis-color">
                <h4>Plebitis</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-label">Total Kasus</div>
                        <div class="stat-value">{{ $record->total_plebitis_kasus ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Hari Infus</div>
                        <div class="stat-value">{{ $record->total_plebitis_hari_infus ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata Laju</div>
                        <div class="stat-value">{{ number_format($record->rata_plebitis_laju ?? 0, 2) }}‰</div>
                    </div>
                </div>
            </div>

            <!-- VAP -->
            <div class="summary-card vap-color">
                <h4>Ventilator Associated Pneumonia (VAP)</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-label">Total Kasus</div>
                        <div class="stat-value">{{ $record->total_vap_kasus ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Hari Ventilator</div>
                        <div class="stat-value">{{ $record->total_vap_hari_ventilator ?? 0 }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata Laju</div>
                        <div class="stat-value">{{ number_format($record->rata_vap_laju ?? 0, 2) }}‰</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

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

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Informasi HAIs</p>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>