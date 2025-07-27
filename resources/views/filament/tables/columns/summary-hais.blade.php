@if($getRecord())
<div class="space-y-1">
    <!-- Baris 1: HAP, IAD, ILO -->
    <div class="flex gap-3 text-xs">
        <!-- HAP -->
        <div class="flex items-center gap-1 bg-blue-50 px-2 py-1 rounded border">
            <span class="font-semibold text-blue-800">HAP:</span>
            <span class="text-blue-600">{{ $getRecord()->total_hap_kasus ?? 0 }}/{{ $getRecord()->total_hap_hari_rawat ?? 0 }} ({{ number_format($getRecord()->rata_hap_laju ?? 0, 2) }}‰)</span>
        </div>

        <!-- IAD -->
        <div class="flex items-center gap-1 bg-green-50 px-2 py-1 rounded border">
            <span class="font-semibold text-green-800">IAD:</span>
            <span class="text-green-600">{{ $getRecord()->total_iad_kasus ?? 0 }}/{{ $getRecord()->total_iad_hari_terpasang ?? 0 }} ({{ number_format($getRecord()->rata_iad_laju ?? 0, 2) }}‰)</span>
        </div>

        <!-- ILO -->
        <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded border">
            <span class="font-semibold text-yellow-800">ILO:</span>
            <span class="text-yellow-600">{{ $getRecord()->total_ilo_kasus ?? 0 }}/{{ $getRecord()->total_ilo_hari_operasi ?? 0 }} ({{ number_format($getRecord()->rata_ilo_laju ?? 0, 2) }}%)</span>
        </div>
    </div>

    <!-- Baris 2: ISK, Plebitis, VAP -->
    <div class="flex gap-3 text-xs">
        <!-- ISK -->
        <div class="flex items-center gap-1 bg-red-50 px-2 py-1 rounded border">
            <span class="font-semibold text-red-800">ISK:</span>
            <span class="text-red-600">{{ $getRecord()->total_isk_kasus ?? 0 }}/{{ $getRecord()->total_isk_hari_kateter ?? 0 }} ({{ number_format($getRecord()->rata_isk_laju ?? 0, 2) }}‰)</span>
        </div>

        <!-- Plebitis -->
        <div class="flex items-center gap-1 bg-purple-50 px-2 py-1 rounded border">
            <span class="font-semibold text-purple-800">Plebitis:</span>
            <span class="text-purple-600">{{ $getRecord()->total_plebitis_kasus ?? 0 }}/{{ $getRecord()->total_plebitis_hari_infus ?? 0 }} ({{ number_format($getRecord()->rata_plebitis_laju ?? 0, 2) }}‰)</span>
        </div>

        <!-- VAP -->
        <div class="flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded border">
            <span class="font-semibold text-indigo-800">VAP:</span>
            <span class="text-indigo-600">{{ $getRecord()->total_vap_kasus ?? 0 }}/{{ $getRecord()->total_vap_hari_ventilator ?? 0 }} ({{ number_format($getRecord()->rata_vap_laju ?? 0, 2) }}‰)</span>
        </div>
    </div>
</div>
@else
<div class="text-gray-500 text-sm">Data tidak tersedia</div>
@endif