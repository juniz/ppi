<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Widgets/StatusInputHaisTable.php');

// Shorten labels
$content = str_replace("->label('JUMLAH PASIEN')", "->label('TOTAL')", $content);
$content = str_replace("->label('SUDAH INPUT')", "->label('SUDAH')", $content);
$content = str_replace("->label('BELUM INPUT')", "->label('BELUM')", $content);
$content = str_replace("->label('PERSENTASE')", "->label('%')", $content);

// Remove size('lg')
$content = str_replace("->size('lg')", "", $content);

// Remove contentGrid
$content = preg_replace("/\\s+->contentGrid\(\\[\\s+'md' => 2,\\s+'xl' => 3,\\s+\\]\)/", "", $content);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Widgets/StatusInputHaisTable.php', $content);
echo "Widget fixed.\n";
