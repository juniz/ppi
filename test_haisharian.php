<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Filament\Pages\HaisHarian;
use Livewire\Livewire;

$component = Livewire::test(HaisHarian::class);

$filters = $component->get('filters');
echo "Filters from component: " . json_encode($filters) . "\n";

$tableQuery = $component->instance()->getTableQueryForExport(); 
// getTableQueryForExport doesn't exist, we can just call table() directly.

$page = $component->instance();
$table = $page->table(new \Filament\Tables\Table($page));
$query = $table->getQuery();

echo "SQL: " . $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";
echo "Count: " . $query->count() . "\n";
