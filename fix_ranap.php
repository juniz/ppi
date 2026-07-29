<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Pages/Ranap.php');
// The dangling lines are:
//                                         ->label('9. Drainase tetap tertutup')
//                                         ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'])
//                                         ->default('Ya')->required(),

$content = preg_replace("/\s+->label\('9\. Drainase tetap tertutup'\)\s+->options\(\['Ya' => 'Ya', 'Tidak' => 'Tidak', 'NA' => 'NA'\]\)\s+->default\('Ya'\)->required\(\),/", "", $content);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Pages/Ranap.php', $content);
echo "Fixed Ranap.php\n";
