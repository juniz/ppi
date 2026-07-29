<?php

$dir = new RecursiveDirectoryIterator('/Users/saifulumam/Developer/sihais/resources/views/');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    "Data ISK (Infeksi Saluran Kemih)" => "Data CAUTI (Catheter-Associated Urinary Tract Infection)",
    "ISK (Infeksi Saluran Kemih)" => "CAUTI (Catheter-Associated Urinary Tract Infection)",
    "<th>ISK</th>" => "<th>CAUTI</th>",
    "Laju ISK" => "Laju CAUTI",
    "text-red-800\">ISK:</span>" => "text-red-800\">CAUTI:</span>",
    "name: 'ISK'" => "name: 'CAUTI'",
    "Data Detail ISK" => "Data Detail CAUTI",
];

$count = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;
    
    foreach($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    if($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated view: $path\n";
        $count++;
    }
}
echo "Total views updated: $count\n";

