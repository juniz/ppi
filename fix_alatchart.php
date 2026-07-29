<?php
$f = '/Users/saifulumam/Developer/sihais/app/Filament/Resources/DataHaisResource/Widgets/HaisHarianAlatChart.php';
$c = file_get_contents($f);

// 1. Add InteractsWithPageFilters trait
$c = str_replace(
    "class HaisHarianAlatChart extends ApexChartWidget\n{",
    "use Filament\Widgets\Concerns\InteractsWithPageFilters;\n\nclass HaisHarianAlatChart extends ApexChartWidget\n{\n    use InteractsWithPageFilters;",
    $c
);

// 2. Remove public ?string $filter = 'today'; and getFilters()
$c = preg_replace("/\s+public \?string \\\$filter = 'today';\s+protected function getFilters\(\): \?array\s+\{[\s\S]*?\}\n/", "", $c);

// 3. Update getOptions() to use $this->filters
$c = preg_replace(
    "/\\\$dateRange = match \(\\\$this->filter\) \{[\s\S]*?\};\n/",
    "",
    $c
);

$c = preg_replace(
    "/\\\$data = DataHais::query\(\)[\s\S]*?->selectRaw\('/",
    "\$dari = \$this->filters['dari_tanggal'] ?? null;\n        \$sampai = \$this->filters['sampai_tanggal'] ?? null;\n\n        \$data = DataHais::query()\n            ->when(\$dari, fn(\$q) => \$q->whereDate('tanggal', '>=', \$dari))\n            ->when(\$sampai, fn(\$q) => \$q->whereDate('tanggal', '<=', \$sampai))\n            ->selectRaw('",
    $c
);

file_put_contents($f, $c);
echo "HaisHarianAlatChart.php updated.\n";
