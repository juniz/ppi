<?php
$f = '/Users/saifulumam/Developer/sihais/app/Filament/Pages/HaisHarian.php';
$c = file_get_contents($f);

// 1. Add Select and TextInput to imports if not present
if (!str_contains($c, 'use Filament\Forms\Components\TextInput;')) {
    $c = str_replace("use Filament\Forms\Components\DatePicker;", "use Filament\Forms\Components\DatePicker;\nuse Filament\Forms\Components\TextInput;\nuse Filament\Forms\Components\Select;", $c);
}

// 2. Update form schema
$newSchema = <<<PHP
Section::make('Filter Master (Semua Data)')
                    ->schema([
                        TextInput::make('search')->label('Cari Data (Nama/No.Rawat/Kamar)')->placeholder('Ketik pencarian...')->columnSpan(1),
                        Select::make('kd_bangsal')->label('Bangsal')->options(\App\Models\Bangsal::pluck('nm_bangsal', 'kd_bangsal'))->searchable()->columnSpan(1),
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal')->native(false)->required()->columnSpan(1),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal')->native(false)->required()->columnSpan(1),
                    ])
                    ->columns(4)
PHP;

$c = preg_replace(
    "/Section::make\('Filter Master \(Semua Data\)'\)[\s\S]*?->columns\(2\)/",
    $newSchema,
    $c
);

// 3. Update table query
$newQuery = <<<PHP
->query(
                \App\Models\DataHais::query()
                    ->with('regPeriksa.pasien')
                    ->with('kamar.bangsal')
                    ->when(!empty(\$this->filters['dari_tanggal']), fn(\$q) => \$q->whereDate('data_HAIs.tanggal', '>=', \$this->filters['dari_tanggal']))
                    ->when(!empty(\$this->filters['sampai_tanggal']), fn(\$q) => \$q->whereDate('data_HAIs.tanggal', '<=', \$this->filters['sampai_tanggal']))
                    ->when(!empty(\$this->filters['kd_bangsal']), fn(\$q) => \$q->whereHas('kamar', fn(\$k) => \$k->where('kd_bangsal', \$this->filters['kd_bangsal'])))
                    ->when(!empty(\$this->filters['search']), function (\$q) {
                        \$search = \$this->filters['search'];
                        \$q->where(function (\$query) use (\$search) {
                            \$query->whereHas('regPeriksa.pasien', fn(\$p) => \$p->where('nm_pasien', 'like', "%{\$search}%"))
                                  ->orWhere('data_HAIs.no_rawat', 'like', "%{\$search}%")
                                  ->orWhereHas('kamar', fn(\$k) => \$k->where('kd_kamar', 'like', "%{\$search}%"));
                        });
                    })
                    ->orderByDesc('data_HAIs.tanggal')
            )
PHP;

$c = preg_replace(
    "/->query\([\s\S]*?->orderByDesc\('tanggal'\)\n\s+\)/",
    $newQuery,
    $c
);

// 4. Remove searchable() from columns
$c = str_replace("->searchable()", "", $c);

// 5. Remove filters from table
$c = preg_replace("/->filters\(\[[\s\S]*?\]\)/", "->filters([])", $c);

file_put_contents($f, $c);
echo "Updated HaisHarian.php\n";
