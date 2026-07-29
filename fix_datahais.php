<?php
$f = '/Users/saifulumam/Developer/sihais/app/Filament/Resources/DataHaisResource.php';
$c = file_get_contents($f);

$c = str_replace("TextInput::make('ISK')", "TextInput::make('ISK')->label('CAUTI')", $c);
$c = str_replace("TextColumn::make('ISK')", "TextColumn::make('ISK')->label('CAUTI')", $c);

file_put_contents($f, $c);
echo "DataHaisResource updated.\n";
