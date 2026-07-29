<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Widgets/StatusInputHaisTable.php');

$startStr = "            ->columns([\n";
$endStr = "            ->defaultSort('persentase', 'desc')";

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr, $startPos);

if ($startPos === false || $endPos === false) {
    echo "Could not find start or end pos\n";
    exit(1);
}

$newColumns = <<<PHP
            ->columns([
                TextColumn::make('nm_bangsal')
                    ->label('RUANGAN')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jumlah_pasien')
                    ->label('TOTAL')
                    ->alignCenter()
                    ->sortable()
                    ->badge(),
                TextColumn::make('sudah_input')
                    ->label('SUDAH')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('belum_input')
                    ->label('BELUM')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('danger'),
                TextColumn::make('persentase')
                    ->label('%')
                    ->alignCenter()
                    ->state(function (\$record) {
                        if (\$record->jumlah_pasien > 0) {
                            \$persentase = (\$record->sudah_input / \$record->jumlah_pasien) * 100;
                            return number_format(\$persentase, 1) . '%';
                        }
                        return '0%';
                    })
                    ->badge()
                    ->color(function (\$record) {
                        if (\$record->jumlah_pasien == 0) return 'gray';
                        \$persentase = (\$record->sudah_input / \$record->jumlah_pasien) * 100;
                        if (\$persentase >= 90) return 'success';
                        if (\$persentase >= 70) return 'warning';
                        return 'danger';
                    })
                    ->sortable(),
            ])
PHP;

$content = substr($content, 0, $startPos) . $newColumns . "\n" . substr($content, $endPos);

$content = str_replace("->poll('10s')\n            ->contentGrid([\n                'md' => 2,\n                'xl' => 3,\n            ]);", "->poll('10s');", $content);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Widgets/StatusInputHaisTable.php', $content);
echo "Widget reverted.\n";
