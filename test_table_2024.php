<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Filament\Pages\HaisHarian;
use Livewire\Livewire;

$component = Livewire::test(HaisHarian::class);

$component->set('filters.dari_tanggal', '2024-07-01');
$component->set('filters.sampai_tanggal', '2026-07-31');

$tableQuery = $component->instance()->getTableQueryForExport(); 
$page = $component->instance();
$table = $page->table(new \Filament\Tables\Table($page));
$query = $table->getQuery();

echo "SQL: " . $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";
echo "Count: " . $query->count() . "\n";
