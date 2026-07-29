<?php
$f = '/Users/saifulumam/Developer/sihais/app/Filament/Pages/HaisHarian.php';
$c = file_get_contents($f);

// We will find the start of ->columns([ and replace the whole array.
$start = strpos($c, '->columns([');
$end = strpos($c, ']);', $start) + 3;

$newColumns = <<<PHP
->columns([
                Tables\Columns\TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->dateTime('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('regPeriksa.pasien.jk')
                    ->label('JK')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ETT')->label('ETT')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('CVL')->label('CVL')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('IVL')->label('IVL')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('UC')->label('UC')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('VAP')->label('VAP')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('IAD')->label('IAD')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('PLEB')->label('PLEB')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('ISK')->label('CAUTI')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('ILO')->label('ILO')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('HAP')->label('HAP')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('Tinea')->label('Tinea')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('Scabies')->label('Scabies')->summarize([Sum::make()]),
                Tables\Columns\TextColumn::make('Deku')->label('Deku'),
                Tables\Columns\TextColumn::make('SPUTUM')->label('SPUTUM'),
                Tables\Columns\TextColumn::make('DARAH')->label('DARAH'),
                Tables\Columns\TextColumn::make('URINE')->label('URINE'),
                Tables\Columns\TextColumn::make('ANTIBIOTIK')->label('ANTIBIOTIK'),
                Tables\Columns\TextColumn::make('kamar.bangsal.nm_bangsal')->label('Bangsal'),
            ]);
PHP;

$c = substr_replace($c, $newColumns, $start, $end - $start);
file_put_contents($f, $c);
echo "Columns fixed!\n";
