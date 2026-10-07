<?php

namespace App\Console\Commands;

use App\Models\BridgingSetting;
use App\Services\SimrsBridgeService;
use Illuminate\Console\Command;
use Throwable;

class SyncSimrsPasien extends Command
{
    protected $signature = 'simrs:sync-pasien
        {--force : Jalankan walau bridging atau auto-sync nonaktif}
        {--aktif : Hanya sinkronkan pasien rawat inap aktif}
        {--pulang : Hanya sinkronkan pasien yang sudah pulang}';

    protected $description = 'Sinkronkan data pasien rawat inap dari SIMRS ke SiHAI\'s';

    public function handle(): int
    {
        $setting = BridgingSetting::current();

        if (! $this->option('force') && ! $setting->is_active) {
            $this->warn('Bridging SIMRS tidak aktif. Gunakan --force untuk menjalankan paksa.');

            return self::SUCCESS;
        }

        if (empty($setting->base_url)) {
            $this->error('Base URL Bridging belum diatur.');

            return self::FAILURE;
        }

        $service = new SimrsBridgeService($setting);
        $onlyAktif = $this->option('aktif');
        $onlyPulang = $this->option('pulang');

        try {
            if (! $onlyPulang) {
                $aktif = $service->syncPasienRanapAktif();
                $this->info("Pasien ranap aktif: {$aktif['imported']} baru, {$aktif['updated']} diperbarui, {$aktif['unmapped']} ruangan belum dipetakan.");
            }

            if (! $onlyAktif) {
                $pulang = $service->syncPasienPulang([
                    'tanggal_awal' => now()->subDays(7)->toDateString(),
                    'tanggal_akhir' => now()->toDateString(),
                ]);
                $this->info("Pasien pulang: {$pulang['updated']} diperbarui.");
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $setting->update([
                'last_sync_at' => now(),
                'last_sync_status' => 'failed',
                'last_sync_message' => 'Sinkronisasi pasien gagal. Periksa koneksi dan log aplikasi.',
            ]);
            report($exception);
            $this->error('Sinkronisasi pasien gagal: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
