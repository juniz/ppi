@if($getRecord())
<div class="p-4 bg-white rounded-lg shadow">
    <h3 class="text-lg font-medium text-gray-900 mb-3">Data Detail</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print:grid-cols-2">
        <!-- Kolom Kiri -->
        <div>
            @if($getRecord()->data_hap)
                <div class="mb-4">
                    <h4 class="font-semibold text-blue-800 mb-2 text-sm">Data Detail HAP (Hospital Acquired Pneumonia)</h4>
                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numerator</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Rawat</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laju</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(is_string($getRecord()->data_hap) ? json_decode($getRecord()->data_hap, true) : $getRecord()->data_hap as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $data['nama_bulan'] ?? ($data['bulan'] ?? '-') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['numerator'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['hari_rawat'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['laju'] ?? '0' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['persentase'] ?? '0' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($getRecord()->data_iad)
                <div class="mb-4">
                    <h4 class="font-semibold text-green-800 mb-2 text-sm">Data Detail IAD (Infeksi Aliran Darah)</h4>
                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numerator</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Terpasang</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laju</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(is_string($getRecord()->data_iad) ? json_decode($getRecord()->data_iad, true) : $getRecord()->data_iad as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $data['nama_bulan'] ?? ($data['bulan'] ?? '-') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['numerator'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['hari_terpasang'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['laju'] ?? '0' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['persentase'] ?? '0' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($getRecord()->data_ilo)
                <div class="mb-4">
                    <h4 class="font-semibold text-yellow-800 mb-2 text-sm">Data Detail ILO (Infeksi Luka Operasi)</h4>
                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numerator</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Operasi</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laju</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(is_string($getRecord()->data_ilo) ? json_decode($getRecord()->data_ilo, true) : $getRecord()->data_ilo as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $data['nama_bulan'] ?? ($data['bulan'] ?? '-') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['numerator'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['hari_operasi'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['laju'] ?? '0' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['persentase'] ?? '0' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan -->
        <div>
            @if($getRecord()->data_isk)
                <div class="mb-4">
                    <h4 class="font-semibold text-red-800 mb-2 text-sm">Data Detail ISK (Infeksi Saluran Kemih)</h4>
                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numerator</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Kateter</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laju</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(is_string($getRecord()->data_isk) ? json_decode($getRecord()->data_isk, true) : $getRecord()->data_isk as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $data['nama_bulan'] ?? ($data['bulan'] ?? '-') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['numerator'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['hari_kateter'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['laju'] ?? '0' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['persentase'] ?? '0' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($getRecord()->data_plebitis)
                <div class="mb-4">
                    <h4 class="font-semibold text-purple-800 mb-2 text-sm">Data Detail Plebitis</h4>
                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numerator</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Infus</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laju</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(is_string($getRecord()->data_plebitis) ? json_decode($getRecord()->data_plebitis, true) : $getRecord()->data_plebitis as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $data['nama_bulan'] ?? ($data['bulan'] ?? '-') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['numerator'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['hari_infus'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['laju'] ?? '0' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['persentase'] ?? '0' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($getRecord()->data_vap)
                <div class="mb-4">
                    <h4 class="font-semibold text-indigo-800 mb-2 text-sm">Data Detail VAP (Ventilator Associated Pneumonia)</h4>
                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numerator</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Ventilator</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laju</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(is_string($getRecord()->data_vap) ? json_decode($getRecord()->data_vap, true) : $getRecord()->data_vap as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $data['nama_bulan'] ?? ($data['bulan'] ?? '-') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['numerator'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['hari_ventilator'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['laju'] ?? '0' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-center">{{ $data['persentase'] ?? '0' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@else
<div class="text-gray-500 text-sm">Data tidak tersedia</div>
@endif