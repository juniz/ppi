@php
    $data = collect($data);
    $dayField = $type === 'HAP' ? 'hari_rawat' : ($type === 'IAD' ? 'hari_terpasang' : ($type === 'ILO' ? 'hari_operasi' : ($type === 'ISK' ? 'hari_kateter' : ($type === 'PLEB' ? 'hari_infus' : 'hari_ventilator'))));
    $totalNumerator = $data->sum('numerator');
    $totalDays = $data->sum($dayField);
    $totalDenumerator = $data->sum('denumerator');
    $totalLaju = '0 ‰';
    $totalPersentase = '0 %';

    if ($totalDays > 0) {
        $totalLaju = number_format(round(($totalDenumerator / $totalDays) * 1000), 0, '.', ',') . ' ‰';
    }

    if ($totalNumerator > 0) {
        $totalPersentase = number_format(round(($totalDenumerator / $totalNumerator) * 100, 2), 2, '.', ',') . ' %';
    }
@endphp

<div class="section-title">Laju {{ $title }}</div>
<table>
    <thead>
        <tr>
            <th style="text-align: left; width: 25%;">Ruangan</th>
            <th>
                @if($type == 'HAP') Pasien Dirawat
                @elseif($type == 'IAD') Pasien Terpasang IAD
                @elseif($type == 'ILO') Pasien Operasi
                @elseif($type == 'ISK') Pasien Terpasang UC
                @elseif($type == 'PLEB') Pasien Terpasang Infus
                @else Pasien Terpasang Ventilator
                @endif
            </th>
            <th>
                @if($type == 'HAP') Hari Rawat
                @elseif($type == 'IAD') Hari Terpasang
                @elseif($type == 'ILO') Hari Operasi
                @elseif($type == 'ISK') Hari UC
                @elseif($type == 'PLEB') Hari Infus
                @else Hari Ventilator
                @endif
            </th>
            <th>{{ $type }}</th>
            <th>Laju</th>
            <th>Persentase</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr>
            <td style="text-align: left;">{{ $item->nm_bangsal }}</td>
            <td>{{ $item->numerator }}</td>
            <td>{{ $item->{$dayField} }}</td>
            <td>{{ $item->denumerator }}</td>
            <td>{{ $item->laju }}</td>
            <td>{{ $item->persentase }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right;">Total / Rata-rata</td>
            <td>{{ $totalNumerator }}</td>
            <td>{{ $totalDays }}</td>
            <td>{{ $totalDenumerator }}</td>
            <td>{{ $totalLaju }}</td>
            <td>{{ $totalPersentase }}</td>
        </tr>
    </tfoot>
</table>
