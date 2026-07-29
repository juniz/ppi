<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Widgets/StatusInputHaisTable.php');

$content = str_replace(
    "->searchable(),\n                TextColumn::make('jumlah_pasien')",
    "->searchable()\n                    ->wrap(),\n                TextColumn::make('jumlah_pasien')",
    $content
);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Widgets/StatusInputHaisTable.php', $content);
echo "Wrap added.\n";
