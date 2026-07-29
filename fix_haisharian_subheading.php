<?php
$f = '/Users/saifulumam/Developer/sihais/app/Filament/Pages/HaisHarian.php';
$c = file_get_contents($f);

// Add getSubheading
$insert = <<<PHP

    public function getSubheading(): \Illuminate\Contracts\Support\Htmlable|string|null
    {
        return view('filament.pages.partials.hais-harian-filter');
    }
PHP;

$c = str_replace(
    "protected static string \$view = 'filament.pages.hais-harian';",
    "protected static string \$view = 'filament.pages.hais-harian';\n" . $insert,
    $c
);

file_put_contents($f, $c);

// create the partial view
$dir = '/Users/saifulumam/Developer/sihais/resources/views/filament/pages/partials';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$partial = <<<HTML
<div class="mb-4">
    <form wire:submit="applyFilters">
        {{ \$this->form }}
    </form>
</div>
HTML;
file_put_contents($dir . '/hais-harian-filter.blade.php', $partial);

echo "Subheading added.\n";
