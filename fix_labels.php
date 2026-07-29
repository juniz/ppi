<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php');

$replacements = [
    "Tables\Columns\TextColumn::make('pemasangan_1_indikasi')" => "Tables\Columns\TextColumn::make('pemasangan_1_indikasi')->label('1. Indikasi pemasangan kateter urin menetap sesuai kebutuhan')",
    "Tables\Columns\TextColumn::make('pemasangan_2_hand_hygiene')" => "Tables\Columns\TextColumn::make('pemasangan_2_hand_hygiene')->label('2. Hand hygiene dilakukan sebelum dan sesudah tindakan')",
    "Tables\Columns\TextColumn::make('pemasangan_3_teknik_aseptik')" => "Tables\Columns\TextColumn::make('pemasangan_3_teknik_aseptik')->label('3. Menggunakan teknik aseptik')",
    "Tables\Columns\TextColumn::make('pemasangan_4_alat_steril')" => "Tables\Columns\TextColumn::make('pemasangan_4_alat_steril')->label('4. Menggunakan alat steril, pelumas steril, dan area kerja bersih')",
    "Tables\Columns\TextColumn::make('perawatan_1_hand_hygiene')" => "Tables\Columns\TextColumn::make('perawatan_1_hand_hygiene')->label('1. Hand hygiene sebelum dan sesudah memanipulasi kateter/perangkat')",
    "Tables\Columns\TextColumn::make('perawatan_2_genitalia_dibersihkan')" => "Tables\Columns\TextColumn::make('perawatan_2_genitalia_dibersihkan')->label('2. Daerah genitalia dibersihkan')",
    "Tables\Columns\TextColumn::make('perawatan_3_fiksasi_kateter')" => "Tables\Columns\TextColumn::make('perawatan_3_fiksasi_kateter')->label('3. Kateter diberikan fiksasi dengan baik')",
    "Tables\Columns\TextColumn::make('perawatan_4_tidak_diganti_rutin')" => "Tables\Columns\TextColumn::make('perawatan_4_tidak_diganti_rutin')->label('4. Tidak dilakukan penggantian kateter secara rutin kecuali terjadi infeksi')",
    "Tables\Columns\TextColumn::make('perawatan_5_aliran_steril_tertutup')" => "Tables\Columns\TextColumn::make('perawatan_5_aliran_steril_tertutup')->label('5. Sistem aliran urin dipertahankan steril dan tertutup')",
    "Tables\Columns\TextColumn::make('perawatan_6_hubungan_kateter_tertutup')" => "Tables\Columns\TextColumn::make('perawatan_6_hubungan_kateter_tertutup')->label('6. Hubungan kateter dengan pipa drainase tidak dibuka kecuali atas indikasi')",
    "Tables\Columns\TextColumn::make('perawatan_7_urine_bag_tidak_di_lantai')" => "Tables\Columns\TextColumn::make('perawatan_7_urine_bag_tidak_di_lantai')->label('7. Urine bag tidak diletakkan di lantai')",
    "Tables\Columns\TextColumn::make('perawatan_8_selang_tidak_terlipat')" => "Tables\Columns\TextColumn::make('perawatan_8_selang_tidak_terlipat')->label('8. Selang tidak terlipat/tertekuk')",
    "Tables\Columns\TextColumn::make('perawatan_10_segera_dilepas')" => "Tables\Columns\TextColumn::make('perawatan_10_segera_dilepas')->label('9. Kateter segera dilepas jika sudah tidak dibutuhkan')",
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php', $content);
echo "Labels added.\n";
