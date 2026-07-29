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
                \Filament\Tables\Columns\Layout\Stack::make([
                    TextColumn::make('nm_bangsal')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable()
                        ->sortable(),
                    \Filament\Tables\Columns\Layout\Split::make([
                        TextColumn::make('jumlah_pasien')
                            ->badge()
                            ->label('Total Pasien')
                            ->icon('heroicon-o-users'),
                        TextColumn::make('sudah_input')
                            ->badge()
                            ->label('Sudah')
                            ->color('success')
                            ->icon('heroicon-o-check-circle'),
                        TextColumn::make('belum_input')
                            ->badge()
                            ->label('Belum')
                            ->color('danger')
                            ->icon('heroicon-o-x-circle'),
                        TextColumn::make('persentase')
                            ->badge()
                            ->label('Kepatuhan')
                            ->icon('heroicon-o-chart-pie')
                            ->state(function (\$record) {
                                if (\$record->jumlah_pasien > 0) {
                                    \$persentase = (\$record->sudah_input / \$record->jumlah_pasien) * 100;
                                    return number_format(\$persentase, 1) . '%';
                                }
                                return '0%';
                            })
                            ->color(function (\$record) {
                                if (\$record->jumlah_pasien == 0) return 'gray';
                                \$persentase = (\$record->sudah_input / \$record->jumlah_pasien) * 100;
                                if (\$persentase >= 90) return 'success';
                                if (\$persentase >= 70) return 'warning';
                                return 'danger';
                            })
                            ->sortable(),
                    ]),
                ])->space(3)
            ])
PHP;

$content = substr($content, 0, $startPos) . $newColumns . "\n" . substr($content, $endPos);

$content = str_replace("->poll('10s');", "->poll('10s')\n            ->contentGrid([\n                'md' => 2,\n                'xl' => 3,\n            ]);", $content);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Widgets/StatusInputHaisTable.php', $content);
echo "Widget transformed.\n";
