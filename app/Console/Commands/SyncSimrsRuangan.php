<?php

namespace App\Console\Commands;

use App\Models\BridgingSetting;
use App\Services\SimrsBridgeService;
use Illuminate\Console\Command;
use Throwable;

class SyncSimrsRuangan extends Command
{
    protected $signature = 'simrs:sync-ruangan {--force : Jalankan walau bridging atau auto-sync nonaktif}';

    protected $description = 'Sinkronkan master ruangan dari SIMRS';

    public function handle(): int
    {
        $setting = BridgingSetting::current();

        if (! $this->option('force') && (! $setting->is_active || ! $setting->auto_sync)) {
            return self::SUCCESS;
        }

        $interval = max(5, (int) $setting->auto_sync_interval);
        if (! $this->option('force') && $setting->last_sync_at?->gt(now()->subMinutes($interval))) {
            return self::SUCCESS;
        }

        try {
            $result = (new SimrsBridgeService($setting))->syncRuangan();
            $this->info("{$result['count']} ruangan berhasil disinkronkan.");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $setting->update([
                'last_sync_at' => now(),
                'last_sync_status' => 'failed',
                'last_sync_message' => 'Sinkronisasi gagal. Periksa koneksi dan log aplikasi.',
            ]);
            report($exception);
            $this->error('Sinkronisasi ruangan gagal.');

            return self::FAILURE;
        }
    }
}
