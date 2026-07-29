<?php
$f1 = '/Users/saifulumam/Developer/sihais/app/Filament/Resources/DataHaisResource/Widgets/HaisHarianInfeksiChart.php';
$c1 = file_get_contents($f1);
$c1 = preg_replace(
    "/protected int \| string \| array \\\$columnSpan = 'full';/",
    "protected int | string | array \$columnSpan = ['md' => 1, 'xl' => 1];",
    $c1
);
file_put_contents($f1, $c1);

$f2 = '/Users/saifulumam/Developer/sihais/app/Filament/Resources/DataHaisResource/Widgets/HaisHarianAlatChart.php';
$c2 = file_get_contents($f2);
$c2 = preg_replace(
    "/protected int \| string \| array \\\$columnSpan = 'full';/",
    "protected int | string | array \$columnSpan = ['md' => 1, 'xl' => 1];",
    $c2
);
file_put_contents($f2, $c2);

echo "Widget spans updated.\n";
