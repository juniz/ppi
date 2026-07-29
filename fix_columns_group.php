<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php');

// We need to inject ColumnGroup namespace if not exists
if (strpos($content, 'use Filament\Tables\Columns\ColumnGroup;') === false) {
    $content = str_replace('use Filament\Tables;', "use Filament\Tables;\nuse Filament\Tables\Columns\ColumnGroup;", $content);
}

// In the columns array, we will group the columns
$search = "Tables\Columns\TextColumn::make('pemasangan_1_indikasi')";
$replace = "ColumnGroup::make('Pemasangan')->columns([\n                Tables\Columns\TextColumn::make('pemasangan_1_indikasi')";
$content = str_replace($search, $replace, $content);

$search2 = "Tables\Columns\TextColumn::make('perawatan_1_hand_hygiene')";
$replace2 = "            ]),\n            ColumnGroup::make('Perawatan')->columns([\n                Tables\Columns\TextColumn::make('perawatan_1_hand_hygiene')";
$content = str_replace($search2, $replace2, $content);

$search3 = "Tables\Columns\TextColumn::make('ttl')";
$replace3 = "            ]),\n                Tables\Columns\TextColumn::make('ttl')";
$content = str_replace($search3, $replace3, $content);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php', $content);
echo "ColumnGroups added.\n";
