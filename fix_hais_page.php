<?php
$f = '/Users/saifulumam/Developer/sihais/app/Filament/Pages/HaisPerPasien.php';
$c = file_get_contents($f);
$insert = "    protected static ?string \$navigationGroup = 'Laporan HAIs';\n    protected static ?int \$navigationSort = 3;\n    protected static string \$view = 'filament.pages.hais-per-pasien';\n";
$c = preg_replace("/\s+protected static \?string \\\$navigationGroup = 'Laporan HAIs';\n\s+protected static \?int \\\$navigationSort = 3;\n/", "\n" . $insert, $c);
file_put_contents($f, $c);
echo "Page updated.\n";

$v = '/Users/saifulumam/Developer/sihais/resources/views/filament/pages/hais-per-pasien.blade.php';
$html = <<<'HTML'
<x-filament-panels::page>
    {{ $this->table }}
</x-filament-panels::page>
HTML;
file_put_contents($v, $html);
echo "View created.\n";

