<?php
$f1 = '/Users/saifulumam/Developer/sihais/app/Filament/Widgets/HaisHarianInfeksiChart.php';
$c1 = file_get_contents($f1);
$c1 = str_replace("'ISK'", "'CAUTI'", $c1);
file_put_contents($f1, $c1);

$f2 = '/Users/saifulumam/Developer/sihais/app/Filament/Widgets/AnalisaInfeksiChart.php';
$c2 = file_get_contents($f2);
$c2 = str_replace("'name' => 'ISK'", "'name' => 'CAUTI'", $c2);
file_put_contents($f2, $c2);

$f3 = '/Users/saifulumam/Developer/sihais/app/Filament/Widgets/BundleAuditChart.php';
$c3 = file_get_contents($f3);
$c3 = str_replace("'ISK'", "'CAUTI'", $c3);
file_put_contents($f3, $c3);

echo "Chart labels updated.\n";
