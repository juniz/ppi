<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Models/AuditBundleIsk.php');

$content = str_replace("        'perawatan_9_drainase_tertutup',\n", "", $content);

$oldQueryStr = <<<PHP
            ->selectRaw('CONCAT(ROUND( ((pemasangan_1_indikasi = "Ya") + (pemasangan_2_hand_hygiene = "Ya") + (pemasangan_3_teknik_aseptik = "Ya") + (pemasangan_4_alat_steril = "Ya") + (perawatan_1_hand_hygiene = "Ya") + (perawatan_2_genitalia_dibersihkan = "Ya") + (perawatan_3_fiksasi_kateter = "Ya") + (perawatan_4_tidak_diganti_rutin = "Ya") + (perawatan_5_aliran_steril_tertutup = "Ya") + (perawatan_6_hubungan_kateter_tertutup = "Ya") + (perawatan_7_urine_bag_tidak_di_lantai = "Ya") + (perawatan_8_selang_tidak_terlipat = "Ya") + (perawatan_9_drainase_tertutup = "Ya") + (perawatan_10_segera_dilepas = "Ya")) / NULLIF( ((pemasangan_1_indikasi != "NA") + (pemasangan_2_hand_hygiene != "NA") + (pemasangan_3_teknik_aseptik != "NA") + (pemasangan_4_alat_steril != "NA") + (perawatan_1_hand_hygiene != "NA") + (perawatan_2_genitalia_dibersihkan != "NA") + (perawatan_3_fiksasi_kateter != "NA") + (perawatan_4_tidak_diganti_rutin != "NA") + (perawatan_5_aliran_steril_tertutup != "NA") + (perawatan_6_hubungan_kateter_tertutup != "NA") + (perawatan_7_urine_bag_tidak_di_lantai != "NA") + (perawatan_8_selang_tidak_terlipat != "NA") + (perawatan_9_drainase_tertutup != "NA") + (perawatan_10_segera_dilepas != "NA")), 0) * 100, 2)) as ttl')
PHP;

$newQueryStr = <<<PHP
            ->selectRaw('CONCAT(ROUND( ((pemasangan_1_indikasi = "Ya") + (pemasangan_2_hand_hygiene = "Ya") + (pemasangan_3_teknik_aseptik = "Ya") + (pemasangan_4_alat_steril = "Ya") + (perawatan_1_hand_hygiene = "Ya") + (perawatan_2_genitalia_dibersihkan = "Ya") + (perawatan_3_fiksasi_kateter = "Ya") + (perawatan_4_tidak_diganti_rutin = "Ya") + (perawatan_5_aliran_steril_tertutup = "Ya") + (perawatan_6_hubungan_kateter_tertutup = "Ya") + (perawatan_7_urine_bag_tidak_di_lantai = "Ya") + (perawatan_8_selang_tidak_terlipat = "Ya") + (perawatan_10_segera_dilepas = "Ya")) / NULLIF( ((pemasangan_1_indikasi != "NA") + (pemasangan_2_hand_hygiene != "NA") + (pemasangan_3_teknik_aseptik != "NA") + (pemasangan_4_alat_steril != "NA") + (perawatan_1_hand_hygiene != "NA") + (perawatan_2_genitalia_dibersihkan != "NA") + (perawatan_3_fiksasi_kateter != "NA") + (perawatan_4_tidak_diganti_rutin != "NA") + (perawatan_5_aliran_steril_tertutup != "NA") + (perawatan_6_hubungan_kateter_tertutup != "NA") + (perawatan_7_urine_bag_tidak_di_lantai != "NA") + (perawatan_8_selang_tidak_terlipat != "NA") + (perawatan_10_segera_dilepas != "NA")), 0) * 100, 2)) as ttl')
PHP;

$content = str_replace($oldQueryStr, $newQueryStr, $content);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Models/AuditBundleIsk.php', $content);
echo "Fixed Model\n";
