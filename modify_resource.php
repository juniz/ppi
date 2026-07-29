<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php');

$startStr = "            ->query(\n                AuditBundleIsk::query()";
$endStr = "                    ]),\n            ])";

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr, $startPos);

if ($startPos === false || $endPos === false) {
    echo "Could not find start or end pos\n";
    exit(1);
}

$endPos += strlen($endStr);

$columnsContent = file_get_contents('columns.txt');

$newQueryContent = <<<PHP
            ->query(
                AuditBundleIsk::query()
                    ->with(['ruangAuditKepatuhan'])
                    ->orderBy('tanggal', 'desc')
                    ->select('audit_bundle_isk.*', DB::raw('CONCAT(ROUND( ((pemasangan_1_indikasi = "Ya") + (pemasangan_2_hand_hygiene = "Ya") + (pemasangan_3_teknik_aseptik = "Ya") + (pemasangan_4_alat_steril = "Ya") + (perawatan_1_hand_hygiene = "Ya") + (perawatan_2_genitalia_dibersihkan = "Ya") + (perawatan_3_fiksasi_kateter = "Ya") + (perawatan_4_tidak_diganti_rutin = "Ya") + (perawatan_5_aliran_steril_tertutup = "Ya") + (perawatan_6_hubungan_kateter_tertutup = "Ya") + (perawatan_7_urine_bag_tidak_di_lantai = "Ya") + (perawatan_8_selang_tidak_terlipat = "Ya") + (perawatan_9_drainase_tertutup = "Ya") + (perawatan_10_segera_dilepas = "Ya")) / NULLIF( ((pemasangan_1_indikasi != "NA") + (pemasangan_2_hand_hygiene != "NA") + (pemasangan_3_teknik_aseptik != "NA") + (pemasangan_4_alat_steril != "NA") + (perawatan_1_hand_hygiene != "NA") + (perawatan_2_genitalia_dibersihkan != "NA") + (perawatan_3_fiksasi_kateter != "NA") + (perawatan_4_tidak_diganti_rutin != "NA") + (perawatan_5_aliran_steril_tertutup != "NA") + (perawatan_6_hubungan_kateter_tertutup != "NA") + (perawatan_7_urine_bag_tidak_di_lantai != "NA") + (perawatan_8_selang_tidak_terlipat != "NA") + (perawatan_9_drainase_tertutup != "NA") + (perawatan_10_segera_dilepas != "NA")), 0) * 100, 2)) as ttl'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangAuditKepatuhan.nama_ruang')
                    ->searchable(),
PHP;
$newQueryContent .= "\n" . rtrim($columnsContent) . "\n            ])";

$newContent = substr($content, 0, $startPos) . $newQueryContent . substr($content, $endPos);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php', $newContent);
echo "Successfully updated Resource\n";
