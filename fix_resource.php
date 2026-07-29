<?php
$content = file_get_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php');

$content = preg_replace('/\\s+if \(\\$item->perawatan_9_drainase_tertutup == \'Ya\'\) \\$ttl\+\+;/', '', $content);
$content = preg_replace('/\\s+if \(\\$item->perawatan_9_drainase_tertutup == \'Tidak\'\) \\$ttl\+\+;/', '', $content);
$content = preg_replace('/\\s+if \(\\$item->perawatan_9_drainase_tertutup != \'NA\'\) \\$total\+\+;/', '', $content);

file_put_contents('/Users/saifulumam/Developer/sihais/app/Filament/Resources/AuditBundleIskResource.php', $content);
echo "Fixed Resource\n";
