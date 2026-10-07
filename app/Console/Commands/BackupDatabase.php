<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Throwable;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup
        {--path= : Folder tujuan backup (default: storage/app/backups)}
        {--keep=14 : Jumlah file backup terbaru yang disimpan}
        {--no-prune : Jangan hapus backup lama}';

    protected $description = 'Buat backup database (dump) sebelum menjalankan migrasi';

    public function handle(): int
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver !== 'mysql') {
            $this->error("Backup otomatis hanya mendukung MySQL. Driver saat ini: {$driver}.");

            return self::FAILURE;
        }

        $config = config("database.connections.{$connection}");

        $dumpBinary = $this->locateDumpBinary();
        if (! $dumpBinary) {
            $this->error('mysqldump / mariadb-dump tidak ditemukan di sistem.');

            return self::FAILURE;
        }

        $directory = $this->option('path') ?: storage_path('app/backups');
        File::ensureDirectoryExists($directory);

        $database = $config['database'];
        $timestamp = Carbon::now()->format('Ymd_His');
        $filename = "{$database}_{$timestamp}.sql";
        $target = rtrim($directory, '/').'/'.$filename;

        $this->info("Membuat backup database '{$database}'...");

        try {
            $command = $this->buildCommand($dumpBinary, $config, $database, $target);
            $exitCode = $this->runProcess($command);

            if ($exitCode !== 0 || ! File::exists($target) || File::size($target) === 0) {
                $this->error('Backup gagal dibuat. Periksa kredensial database.');

                return self::FAILURE;
            }

            $size = $this->humanSize(File::size($target));
            $this->info("✅ Backup berhasil: {$target} ({$size})");
        } catch (Throwable $e) {
            $this->error('Backup gagal: '.$e->getMessage());

            return self::FAILURE;
        }

        if (! $this->option('no-prune')) {
            $this->pruneOldBackups($directory, (int) $this->option('keep'));
        }

        return self::SUCCESS;
    }

    protected function locateDumpBinary(): ?string
    {
        foreach (['mysqldump', 'mariadb-dump'] as $binary) {
            $path = trim((string) @shell_exec('command -v '.$binary.' 2>/dev/null'));
            if ($path !== '') {
                return $path;
            }
        }

        return null;
    }

    protected function buildCommand(string $binary, array $config, string $database, string $target): string
    {
        $parts = [
            escapeshellarg($binary),
            '--host='.escapeshellarg($config['host'] ?? '127.0.0.1'),
            '--port='.escapeshellarg((string) ($config['port'] ?? 3306)),
            '--user='.escapeshellarg($config['username'] ?? 'root'),
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--routines',
            '--events',
        ];

        $password = $config['password'] ?? '';
        if ($password !== '' && $password !== null) {
            // Gunakan variabel env agar password tidak tampil di daftar proses.
            $parts[] = '--password=\"$MYSQL_PWD\"';
        }

        $parts[] = escapeshellarg($database);
        $parts[] = '> '.escapeshellarg($target);

        $prefix = $password !== '' && $password !== null
            ? 'MYSQL_PWD='.escapeshellarg($password).' '
            : '';

        return $prefix.implode(' ', $parts);
    }

    protected function runProcess(string $command): int
    {
        $output = [];
        $exitCode = 0;
        exec($command.' 2>&1', $output, $exitCode);

        if ($exitCode !== 0 && ! empty($output)) {
            $this->warn(implode(PHP_EOL, array_slice($output, 0, 5)));
        }

        return $exitCode;
    }

    protected function pruneOldBackups(string $directory, int $keep): void
    {
        if ($keep <= 0) {
            return;
        }

        $files = collect(File::files($directory))
            ->filter(fn ($file) => $file->getExtension() === 'sql')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        $files->slice($keep)->each(function ($file) {
            File::delete($file->getPathname());
            $this->line('🗑️  Hapus backup lama: '.$file->getFilename());
        });
    }

    protected function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
