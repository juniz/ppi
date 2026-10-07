<?php
/**
 * Samakan struktur tabel SIMRS dengan sumber (sik_ppi_ses) lalu salin data penuh.
 *
 * Tabel aplikasi SiHAI's dilindungi (tidak diganti strukturnya):
 *  migrations, users, password_reset_tokens, personal_access_tokens,
 *  failed_jobs, sessions, bridging_settings, bridging_mappings,
 *  analisa_rekomendasi_hais, institusi_settings, user_ruang_audit_kepatuhan
 *
 * Jalankan: php scripts/sync_simrs_schema.php
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$env = [];
foreach (file($root.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim(trim($v), "\"'");
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';
$target = $env['DB_DATABASE'] ?? 'sik_ppi';
$source = 'sik_ppi_ses';

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--source=')) {
        $source = substr($arg, 9);
    }
}

$protected = [
    'migrations', 'users', 'password_reset_tokens', 'personal_access_tokens',
    'failed_jobs', 'sessions', 'bridging_settings', 'bridging_mappings',
    'analisa_rekomendasi_hais', 'institusi_settings', 'user_ruang_audit_kepatuhan',
];

echo "Sumber : {$source}\nTarget : {$target}\n\n";

$pdo = new PDO("mysql:host={$host};dbname={$target};charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$getTables = fn (string $db): array => $pdo
    ->query("SELECT table_name FROM information_schema.tables WHERE table_schema=".$pdo->quote($db))
    ->fetchAll(PDO::FETCH_COLUMN);

$sourceTables = $getTables($source);
$targetTables = $getTables($target);

// Tabel yang akan disamakan strukturnya: ada di sumber DAN target, bukan tabel app.
$common = array_values(array_intersect($sourceTables, $targetTables));
$toSync = array_values(array_filter($common, fn ($t) => ! in_array($t, $protected, true)));

// Tabel yang hanya di sumber juga ikut dibuat.
$onlySource = array_values(array_filter(
    array_diff($sourceTables, $targetTables),
    fn ($t) => ! in_array($t, $protected, true),
));

$all = array_values(array_unique(array_merge($toSync, $onlySource)));

echo 'Menyingkronkan '.count($all)." tabel SIMRS...\n\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

$grandTotal = 0;
foreach ($all as $table) {
    try {
        $ddl = buildCreateTable($pdo, $source, $target, $table);

        $pdo->exec("DROP TABLE IF EXISTS `{$target}`.`{$table}`");
        $pdo->exec($ddl);

        $rows = copyData($pdo, $source, $target, $table);
        $grandTotal += $rows;

        echo sprintf("  ✓ %-45s %6d baris\n", $table, $rows);
    } catch (Throwable $e) {
        echo sprintf("  ✗ %-45s GAGAL: %s\n", $table, substr($e->getMessage(), 0, 80));
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

echo "\nSELESAI. Total {$grandTotal} baris.\n";

/**
 * Bangun DDL CREATE TABLE dari sumber, dibersihkan agar aman di target.
 */
function buildCreateTable(PDO $pdo, string $source, string $target, string $table): string
{
    $ddl = $pdo->query("SHOW CREATE TABLE `{$source}`.`{$table}`")->fetch(PDO::FETCH_ASSOC)['Create Table'];

    // Buang prefix database sumber.
    $ddl = preg_replace(
        '/CREATE TABLE `'.preg_quote($source, '/').'`\.`'.preg_quote($table, '/').'`/',
        "CREATE TABLE `{$table}`",
        $ddl
    );

    // Buang FOREIGN KEY (tipe kolom antar-DB bisa beda & urutan tak dijamin).
    $ddl = preg_replace(
        '/,\s*CONSTRAINT `[^`]+` FOREIGN KEY \([^)]+\) REFERENCES `[^`]+` \([^)]+\)( ON DELETE [A-Z ]+)?( ON UPDATE [A-Z ]+)?/i',
        '',
        $ddl
    );

    return $ddl;
}

/**
 * Salin seluruh data dari sumber ke target (kolom yang sama saja).
 */
function copyData(PDO $pdo, string $source, string $target, string $table): int
{
    $sourceCols = getColumns($pdo, $source, $table);
    $targetCols = getColumns($pdo, $target, $table);
    $common = array_values(array_intersect($sourceCols, $targetCols));

    if (empty($common)) {
        return 0;
    }

    $colList = '`'.implode('`, `', $common).'`';
    $select = $pdo->query("SELECT {$colList} FROM `{$source}`.`{$table}`");
    $insert = $pdo->prepare("INSERT INTO `{$target}`.`{$table}` ({$colList}) VALUES (".implode(',', array_fill(0, count($common), '?')).")");

    $count = 0;
    $pdo->beginTransaction();
    try {
        while ($row = $select->fetch(PDO::FETCH_NUM)) {
            $insert->execute($row);
            $count++;
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    return $count;
}

function getColumns(PDO $pdo, string $db, string $table): array
{
    return $pdo
        ->query("SELECT column_name FROM information_schema.columns WHERE table_schema=".$pdo->quote($db)." AND table_name=".$pdo->quote($table))
        ->fetchAll(PDO::FETCH_COLUMN);
}
