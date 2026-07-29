<?php

$dir = new RecursiveDirectoryIterator('/Users/saifulumam/Developer/sihais/app/');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    "->label('ISK')" => "->label('CAUTI')",
    "->label(\"ISK\")" => "->label(\"CAUTI\")",
    "->heading('ISK')" => "->heading('CAUTI')",
    "->title('Laju ISK')" => "->title('Laju CAUTI')",
    "->title('Laju ISK')" => "->title('Laju CAUTI')",
    "title = 'Laju ISK'" => "title = 'Laju CAUTI'",
    "title = 'Bundle ISK'" => "title = 'Bundle CAUTI'",
    "->label('Laju ISK')" => "->label('Laju CAUTI')",
    "->label('Bundle ISK')" => "->label('Bundle CAUTI')",
    "=> 'Bundle ISK'" => "=> 'Bundle CAUTI'",
    "=> 'Laju ISK'" => "=> 'Laju CAUTI'",
    "->label('Laporan ISK')" => "->label('Laporan CAUTI')",
    "->description('Audit Bundle ISK')" => "->description('Audit Bundle CAUTI')",
    "Audit Bundle ISK" => "Audit Bundle CAUTI",
    "Bundle ISK" => "Bundle CAUTI",
    "'label' => 'ISK'" => "'label' => 'CAUTI'",
    "label('LAJU ISK')" => "label('LAJU CAUTI')",
];

$count = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;
    
    foreach($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Also, we can replace some generic 'ISK' when it's clearly a label string.
    $content = str_replace("['label' => 'ISK'", "['label' => 'CAUTI'", $content);
    
    if($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated: $path\n";
        $count++;
    }
}
echo "Total files updated: $count\n";

