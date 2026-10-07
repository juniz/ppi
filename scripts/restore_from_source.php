<?php
/**
 * Skrip pemulihan data SiHAI's.
 *
 * Menyalin tabel & data dari database sumber (sik_ppi_ses) ke database
 * aktif aplikasi (sik_ppi) TANPA menimpa tabel aplikasi yang sudah ada
 * seperti `users` dan `migrations`.
 *
 * Jalankan: php scripts/restore_from_source.php
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

if (! in_array($source, $argv, true) && ! in_array('--source='.$source, $argv, true)) {
    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--source=')) {
            $source = substr($arg, 9);
        }
    }
}

echo "Sumber : {$source}\n";
echo "Target : {$target}\n\n";

$pdo = new PDO("mysql:host={$host};dbname={$target};charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Tabel aplikasi yang TIDAK boleh ditimpa.
$protectedTables = ['users', 'migrations', 'password_reset_tokens', 'personal_access_tokens', 'failed_jobs', 'sessions'];

// Tabel yang strukturnya berbeda jauh antara sumber & target sehingga
// datanya tidak kompatibel bila disalin (dilewati).
$skipTables = ['audit_bundle_isk'];

$getTables = fn (string $db): array => $pdo
    ->query("SELECT table_name FROM information_schema.tables WHERE table_schema=".$pdo->quote($db))
    ->fetchAll(PDO::FETCH_COLUMN);

$sourceTables = $getTables($source);
$targetTables = $getTables($target);

$onlyInSource = array_values(array_diff($sourceTables, $targetTables));
$inBoth = array_values(array_intersect($sourceTables, $targetTables));

$totalRows = 0;

// Matikan pemeriksaan foreign key selama proses (urutan pembuatan tabel
// tidak dijamin, dan kita menyalin apa adanya dari sumber).
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

// 1) Tabel yang hanya ada di sumber: buat struktur + salin data.
echo "=== Membuat ".count($onlyInSource)." tabel baru ===\n";
foreach ($onlyInSource as $table) {
    if (in_array($table, $protectedTables, true) || in_array($table, $skipTables, true)) {
        continue;
    }

    $createSql = $pdo->query("SHOW CREATE TABLE `{$source}`.`{$table}`")->fetch(PDO::FETCH_ASSOC);
    $ddl = $createSql['Create Table'];
    // Hilangkan prefix nama database sumber agar tabel dibuat di database target.
    $ddl = preg_replace('/CREATE TABLE `'.preg_quote($source, '/').'`\.`'.preg_quote($table, '/').'`/', "CREATE TABLE `{$table}`", $ddl);

    // Buang klausa FOREIGN KEY karena tipe kolom di target bisa berbeda
    // (mis. id_ruang varchar vs bigint) dan urutan pembuatan tabel tidak dijamin.
    $ddl = preg_replace('/,\s*CONSTRAINT `[^`]+` FOREIGN KEY \([^)]+\) REFERENCES `[^`]+` \([^)]+\)( ON DELETE [A-Z ]+)?( ON UPDATE [A-Z ]+)?/i', '', $ddl);

    $pdo->exec("DROP TABLE IF EXISTS `{$target}`.`{$table}`");
    $pdo->exec($ddl);

    $rows = copyTableData($pdo, $source, $target, $table);
    $totalRows += $rows;
    echo sprintf("  + %-45s %5d baris\n", $table, $rows);
}

// 2) Tabel di keduanya: salin data (kecuali tabel terproteksi & yang dilewati).
echo "\n=== Menyalin data tabel yang sudah ada ===\n";
foreach ($inBoth as $table) {
    if (in_array($table, $skipTables, true)) {
        echo sprintf("  ! %-45s (DILEWATI, struktur berbeda)\n", $table);
        continue;
    }

    // Tambahkan data tanpa menghapus yang sudah ada (hindari konflik primary key).
    $rows = copyTableData($pdo, $source, $target, $table, append: true, skipExisting: true);
    $totalRows += $rows;
    echo sprintf("  ~ %-45s %5d baris\n", $table, $rows);
}

// 3) Tabel `users`: gabungkan user dari sumber tanpa menghapus user yang ada.
echo "\n=== Menggabungkan tabel users ===\n";
$userRows = mergeUsers($pdo, $source, $target);
$totalRows += $userRows;
echo sprintf("  ~ %-45s %5d user ditambahkan\n", 'users', $userRows);

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

echo "\nSELESAI. Total {$totalRows} baris disalin.\n";

/**
 * Gabungkan user dari sumber ke target berdasarkan email (tidak menimpa yang ada).
 */
function mergeUsers(PDO $pdo, string $source, string $target): int
{
    $sourceCols = getColumns($pdo, $source, 'users');
    $targetCols = getColumns($pdo, $target, 'users');
    $common = array_values(array_intersect($sourceCols, $targetCols));

    if (empty($common)) {
        return 0;
    }

    $colList = '`'.implode('`, `', $common).'`';

    $rows = $pdo->query("SELECT {$colList} FROM `{$source}`.`users`")->fetchAll(PDO::FETCH_ASSOC);

    // Email yang sudah ada di target.
    $existing = $pdo->query("SELECT email FROM `{$target}`.`users`")->fetchAll(PDO::FETCH_COLUMN);
    $existing = array_flip(array_map('strtolower', $existing));

    $insert = $pdo->prepare("INSERT IGNORE INTO `{$target}`.`users` ({$colList}) VALUES (".implode(',', array_fill(0, count($common), '?')).")");

    $count = 0;
    $pdo->beginTransaction();
    try {
        foreach ($rows as $row) {
            $email = strtolower($row['email'] ?? '');
            if (isset($existing[$email])) {
                continue;
            }
            $insert->execute(array_values($row));
            if ($insert->rowCount() > 0) {
                $existing[$email] = true;
                $count++;
            }
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    return $count;
}

/**
 * Salin seluruh data dari tabel sumber ke tabel target.
 * Mengisi hanya kolom yang sama-sama ada di kedua tabel.
 */
function copyTableData(PDO $pdo, string $source, string $target, string $table, bool $append = false, bool $skipExisting = false): int
{
    $sourceCols = getColumns($pdo, $source, $table);
    $targetCols = getColumns($pdo, $target, $table);

    $common = array_values(array_intersect($sourceCols, $targetCols));
    if (empty($common)) {
        echo "    (tidak ada kolom yang sama di {$table}, dilewati)\n";

        return 0;
    }

    $colList = '`'.implode('`, `', $common).'`';

    // Kosongkan target agar tidak terjadi duplikasi/konflik primary key
    // (kecuali mode append untuk menggabungkan data).
    if (! $append) {
        $pdo->exec("DELETE FROM `{$target}`.`{$table}`");
    }

    $select = $pdo->query("SELECT {$colList} FROM `{$source}`.`{$table}`");
    $verb = $skipExisting ? 'INSERT IGNORE' : 'INSERT';
    $insert = $pdo->prepare("{$verb} INTO `{$target}`.`{$table}` ({$colList}) VALUES (".implode(',', array_fill(0, count($common), '?')).")");

    $count = 0;
    $pdo->beginTransaction();
    try {
        while ($row = $select->fetch(PDO::FETCH_NUM)) {
            $insert->execute($row);
            $count += $insert->rowCount();
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
