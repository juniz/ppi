<table class="w-full">
    <thead>
        <tr class="bg-gray-50 border-b border-gray-200">
            <th class="p-3 text-left font-medium text-gray-600">Ruangan</th>
            <th class="p-3 text-center font-medium text-gray-600">
                @if($type == 'HAP')
                    Jumlah Pasien<br>Dirawat
                @elseif($type == 'IAD')
                    Jumlah Pasien<br>Terpasang IAD
                @elseif($type == 'ILO')
                    Jumlah Pasien<br>Operasi
                @elseif($type == 'ISK')
                    Jumlah Pasien<br>Terpasang UC
                @elseif($type == 'PLEB')
                    Jumlah Pasien<br>Terpasang Infus
                @else
                    Jumlah Pasien<br>Terpasang Ventilator
                @endif
            </th>
            <th class="p-3 text-center font-medium text-gray-600">
                @if($type == 'HAP')
                    Hari Rawat
                @elseif($type == 'IAD')
                    Hari Terpasang
                @elseif($type == 'ILO')
                    Hari Operasi
                @elseif($type == 'ISK')
                    Hari UC
                @elseif($type == 'PLEB')
                    Hari Infus
                @else
                    Hari Ventilator
                @endif
            </th>
            <th class="p-3 text-center font-medium text-gray-600">{{ $type }}</th>
            <th class="p-3 text-center font-medium text-gray-600">Laju</th>
            <th class="p-3 text-center font-medium text-gray-600">Persentase</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @forelse($data as $item)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="p-3 text-gray-900 font-medium">{{ $item->nm_bangsal }}</td>
                <td class="p-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                        {{ $item->numerator }}
                    </span>
                </td>
                <td class="p-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-800">
                        {{ $item->{$type == 'HAP' ? 'hari_rawat' : ($type == 'IAD' ? 'hari_terpasang' : ($type == 'ILO' ? 'hari_operasi' : ($type == 'ISK' ? 'hari_kateter' : ($type == 'PLEB' ? 'hari_infus' : 'hari_ventilator'))))} }}
                    </span>
                </td>
                <td class="p-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800">
                        {{ $item->denumerator }}
                    </span>
                </td>
                <td class="p-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                        {{ $item->laju }}
                    </span>
                </td>
                <td class="p-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                        {{ $item->persentase }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-3 text-center text-gray-500">
                    Tidak ada data
                </td>
            </tr>
        @endforelse
    </tbody>
</table>