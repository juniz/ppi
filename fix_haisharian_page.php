<?php
$f = '/Users/saifulumam/Developer/sihais/app/Filament/Pages/HaisHarian.php';
$c = file_get_contents($f);

// 1. Add HasForms and InteractsWithForms
$c = str_replace(
    "use Filament\Tables\Contracts\HasTable;\n",
    "use Filament\Tables\Contracts\HasTable;\nuse Filament\Forms\Contracts\HasForms;\nuse Filament\Forms\Concerns\InteractsWithForms;\nuse Filament\Forms\Form;\nuse Filament\Forms\Components\DatePicker;\nuse Filament\Forms\Components\Section;\n",
    $c
);

$c = str_replace(
    "class HaisHarian extends Page implements HasTable\n{\n    use InteractsWithTable;",
    "class HaisHarian extends Page implements HasTable, HasForms\n{\n    use InteractsWithTable, InteractsWithForms;\n\n    public ?array \$filters = [];\n\n    public function mount(): void\n    {\n        \$this->form->fill([\n            'dari_tanggal' => Carbon::now()->startOfMonth()->toDateString(),\n            'sampai_tanggal' => Carbon::now()->endOfMonth()->toDateString(),\n        ]);\n    }\n\n    public function form(Form \$form): Form\n    {\n        return \$form\n            ->schema([\n                Section::make('Filter Master (Semua Data)')\n                    ->schema([\n                        DatePicker::make('dari_tanggal')->label('Dari Tanggal')->native(false)->required(),\n                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal')->native(false)->required(),\n                    ])\n                    ->columns(2)\n            ])\n            ->statePath('filters')\n            ->live();\n    }",
    $c
);

// 2. Modify the table query to use the master filter
// Wait, currently the query is:
// ->query(\App\Models\DataHais::query()->with(...)->orderByDesc('tanggal'))
$c = preg_replace(
    "/->query\(\s+\\\\App\\\\Models\\\\DataHais::query\(\)\s+->with\('regPeriksa\.pasien'\)\s+->with\('kamar\.bangsal'\)\s+->orderByDesc\('tanggal'\)\s+\)/",
    "->query(
                \App\Models\DataHais::query()
                    ->with('regPeriksa.pasien')
                    ->with('kamar.bangsal')
                    ->when(!empty(\$this->filters['dari_tanggal']), fn(\$q) => \$q->whereDate('tanggal', '>=', \$this->filters['dari_tanggal']))
                    ->when(!empty(\$this->filters['sampai_tanggal']), fn(\$q) => \$q->whereDate('tanggal', '<=', \$this->filters['sampai_tanggal']))
                    ->orderByDesc('tanggal')
            )",
    $c
);

// 3. Remove DateRangeFilter from table filters
$c = preg_replace(
    "/->filters\(\[\s+DateRangeFilter::make\('tanggal'\)[\s\S]*?\)\s+\]\)/",
    "->filters([])",
    $c
);

file_put_contents($f, $c);
echo "HaisHarian.php updated.\n";
