<?php
$fields = [
    'pemasangan_1_indikasi',
    'pemasangan_2_hand_hygiene',
    'pemasangan_3_teknik_aseptik',
    'pemasangan_4_alat_steril',
    'perawatan_1_hand_hygiene',
    'perawatan_2_genitalia_dibersihkan',
    'perawatan_3_fiksasi_kateter',
    'perawatan_4_tidak_diganti_rutin',
    'perawatan_5_aliran_steril_tertutup',
    'perawatan_6_hubungan_kateter_tertutup',
    'perawatan_7_urine_bag_tidak_di_lantai',
    'perawatan_8_selang_tidak_terlipat',
    'perawatan_9_drainase_tertutup',
    'perawatan_10_segera_dilepas',
];

$out = "";
foreach ($fields as $field) {
    $out .= "                Tables\Columns\TextColumn::make('$field')\n";
    $out .= "                    ->summarize([\n";
    $out .= "                        Count::make()->label('Ya')->query(fn(Builder \$query) => \$query->where('$field', 'Ya')),\n";
    $out .= "                        Count::make()->label('Tidak')->query(fn(Builder \$query) => \$query->where('$field', 'Tidak')),\n";
    $out .= "                        Count::make()->label('NA')->query(fn(Builder \$query) => \$query->where('$field', 'NA')),\n";
    $out .= "                        Summarizer::make()->label('Rata-rata')->using(function (Builder \$query) {\n";
    $out .= "                            \$total = \$query->where('$field', '!=', 'NA')->count();\n";
    $out .= "                            \$ya = \$query->where('$field', 'Ya')->count();\n";
    $out .= "                            return round(\$total == 0 ? 0 : (\$ya / \$total) * 100, 2);\n";
    $out .= "                        })\n";
    $out .= "                    ]),\n";
}

$out .= "                Tables\Columns\TextColumn::make('ttl')\n";
$out .= "                    ->label('Ttl. Nilai (%)')\n";
$out .= "                    ->summarize([\n";
$out .= "                        Summarizer::make()->label('Ya')->using(function (Builder \$query) {\n";
$out .= "                            \$ttl = 0;\n";
$out .= "                            foreach (\$query->get() as \$item) {\n";
foreach ($fields as $field) {
    $out .= "                                if (\$item->$field == 'Ya') \$ttl++;\n";
}
$out .= "                            }\n";
$out .= "                            return \$ttl;\n";
$out .= "                        }),\n";
$out .= "                        Summarizer::make()->label('Tidak')->using(function (Builder \$query) {\n";
$out .= "                            \$ttl = 0;\n";
$out .= "                            foreach (\$query->get() as \$item) {\n";
foreach ($fields as $field) {
    $out .= "                                if (\$item->$field == 'Tidak') \$ttl++;\n";
}
$out .= "                            }\n";
$out .= "                            return \$ttl;\n";
$out .= "                        }),\n";
$out .= "                        Summarizer::make()->label('Rata-rata')->using(function (Builder \$query) {\n";
$out .= "                            \$total = 0;\n";
$out .= "                            \$ttl = 0;\n";
$out .= "                            foreach (\$query->get() as \$item) {\n";
foreach ($fields as $field) {
    $out .= "                                if (\$item->$field != 'NA') \$total++;\n";
    $out .= "                                if (\$item->$field == 'Ya') \$ttl++;\n";
}
$out .= "                            }\n";
$out .= "                            return round(\$total == 0 ? 0 : ((\$ttl / \$total) * 100), 2);\n";
$out .= "                        })\n";
$out .= "                            ->suffix('%'),\n";
$out .= "                    ]),\n";

file_put_contents('columns.txt', $out);
echo "Done\n";
