<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q = \App\Models\DataHais::query()
    ->with('regPeriksa.pasien')
    ->with('kamar.bangsal')
    ->whereDate('data_HAIs.tanggal', '>=', '2024-07-01')
    ->whereDate('data_HAIs.tanggal', '<=', '2026-07-31')
    ->orderByDesc('data_HAIs.tanggal');

echo "Table Query Count: " . $q->count() . "\n";

$qc = \App\Models\DataHais::query()
    ->whereDate('data_HAIs.tanggal', '>=', '2024-07-01')
    ->whereDate('data_HAIs.tanggal', '<=', '2026-07-31')
    ->selectRaw('SUM(PLEB) as pleb, SUM(IVL) as ivl')->first();

echo "Chart PLEB: " . $qc->pleb . "\n";
