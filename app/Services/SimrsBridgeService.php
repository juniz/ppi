<?php

namespace App\Services;

use App\Models\Bangsal;
use App\Models\BridgingMapping;
use App\Models\BridgingSetting;
use App\Models\Dokter;
use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\Pasien;
use App\Models\Penjab;
use App\Models\Poliklinik;
use App\Models\RegPeriksa;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class SimrsBridgeService
{
    protected BridgingSetting $setting;

    public function __construct(?BridgingSetting $setting = null)
    {
        $this->setting = $setting ?? BridgingSetting::current();
    }

    protected function client(): PendingRequest
    {
        $baseUrl = rtrim($this->setting->base_url ?? '', '/').'/';
        $timeout = $this->setting->timeout ?: 30;

        $client = Http::baseUrl($baseUrl)
            ->timeout($timeout)
            ->acceptJson();

        switch ($this->setting->auth_type) {
            case 'bearer':
                if (! empty($this->setting->api_token)) {
                    $client = $client->withToken($this->setting->api_token);
                }
                break;
            case 'api_key':
                $headerName = $this->setting->api_key_header ?: 'X-API-KEY';
                if (! empty($this->setting->api_token)) {
                    $client = $client->withHeaders([$headerName => $this->setting->api_token]);
                }
                break;
            case 'basic':
                // Format token username:password
                if (! empty($this->setting->api_token)) {
                    $parts = explode(':', $this->setting->api_token, 2);
                    $user = $parts[0] ?? '';
                    $pass = $parts[1] ?? '';
                    $client = $client->withBasicAuth($user, $pass);
                }
                break;
            case 'none':
            default:
                break;
        }

        return $client;
    }

    public function testConnection(): array
    {
        if (empty($this->setting->base_url)) {
            return [
                'success' => false,
                'message' => 'Base URL belum diatur.',
                'status_code' => null,
                'latency_ms' => null,
            ];
        }

        $startTime = microtime(true);
        try {
            // Ruangan adalah endpoint wajib, sehingga tes ini memvalidasi kontrak API nyata.
            $response = $this->client()->get('ruangan');
            $latency = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $payload = $this->extractData($response->json());

                if (! is_array($payload)) {
                    return $this->connectionResult(false, 'Respons endpoint ruangan bukan JSON yang valid.', $response->status(), $latency);
                }

                return $this->connectionResult(true, 'Koneksi dan kontrak endpoint ruangan valid.', $response->status(), $latency);
            }

            return $this->connectionResult(
                false,
                "Endpoint ruangan merespons HTTP {$response->status()}.",
                $response->status(),
                $latency,
            );
        } catch (Throwable $e) {
            $latency = round((microtime(true) - $startTime) * 1000);

            return $this->connectionResult(false, 'Server SIMRS tidak dapat dihubungi. Periksa URL, autentikasi, dan jaringan.', null, $latency);
        }
    }

    protected function connectionResult(bool $success, string $message, ?int $statusCode, int $latency): array
    {
        $connectionState = [
            'last_connection_at' => now(),
            'last_connection_status' => $success ? 'success' : 'failed',
            'last_connection_latency_ms' => $latency,
            'last_connection_message' => $message,
        ];

        if ($this->setting->exists) {
            $this->setting->update($connectionState);
        } else {
            $this->setting->fill($connectionState);
        }

        return [
            'success' => $success,
            'message' => $message,
            'status_code' => $statusCode,
            'latency_ms' => $latency,
        ];
    }

    public function getRuangan(): array
    {
        $response = $this->client()->get('ruangan');
        if ($response->successful()) {
            return $this->extractData($response->json());
        }
        throw new RuntimeException("Gagal mengambil data ruangan: HTTP {$response->status()}.");
    }

    public function syncRuangan(): array
    {
        $data = $this->getRuangan();
        $mappings = BridgingMapping::query()
            ->where('mapping_type', 'ruangan')
            ->where('is_active', true)
            ->whereNotNull('local_code')
            ->get()
            ->keyBy('external_code');
        $synced = 0;
        $unmapped = 0;

        foreach ($data as $item) {
            $kd = $item['kode_ruangan'] ?? $item['kd_bangsal'] ?? $item['id'] ?? null;
            $mapping = $kd ? $mappings->get((string) $kd) : null;

            if (! $mapping) {
                $unmapped++;

                continue;
            }

            Bangsal::query()->whereKey($mapping->local_code)->update(['status' => '1']);
            $synced++;
        }

        $this->updateSyncStatus('success', "{$synced} ruangan terpetakan; {$unmapped} belum dipetakan.");

        return ['count' => $synced, 'unmapped' => $unmapped];
    }

    public function discoverRuanganMappings(): array
    {
        $data = $this->getRuangan();
        $localCodes = Bangsal::query()
            ->pluck('kd_bangsal')
            ->mapWithKeys(fn (string $code): array => [$code => true]);

        DB::transaction(function () use ($data, $localCodes): void {
            foreach ($data as $item) {
                $externalCode = $item['kode_ruangan'] ?? $item['kd_bangsal'] ?? $item['id'] ?? null;
                $externalName = $item['nama_ruangan'] ?? $item['nm_bangsal'] ?? $item['nama'] ?? null;

                if (blank($externalCode)) {
                    continue;
                }

                $mapping = BridgingMapping::firstOrNew([
                    'mapping_type' => 'ruangan',
                    'external_code' => (string) $externalCode,
                ]);
                $mapping->external_name = $externalName;
                $mapping->last_seen_at = now();
                $mapping->is_active ??= true;

                if (! $mapping->exists && $localCodes->has((string) $externalCode)) {
                    $mapping->local_code = (string) $externalCode;
                }

                $mapping->save();
            }
        });

        $query = BridgingMapping::query()->where('mapping_type', 'ruangan');

        return [
            'total' => (clone $query)->count(),
            'unmapped' => (clone $query)->whereNull('local_code')->count(),
        ];
    }

    public function getPasienRanapAktif(array $params = []): array
    {
        $response = $this->client()->get('pasien-ranap/aktif', $params);
        if ($response->successful()) {
            return $this->extractData($response->json());
        }
        throw new RuntimeException("Gagal mengambil data rawat inap aktif: HTTP {$response->status()}.");
    }

    public function searchPasien(string $keyword): array
    {
        $response = $this->client()->get('pasien/search', ['keyword' => $keyword]);
        if ($response->successful()) {
            return $this->extractData($response->json());
        }

        return [];
    }

    public function getPasienPulang(array $params = []): array
    {
        $response = $this->client()->get('pasien-ranap/pulang', $params);
        if ($response->successful()) {
            return $this->extractData($response->json());
        }
        throw new RuntimeException("Gagal mengambil data pasien pulang: HTTP {$response->status()}.");
    }

    public function getAlatInvasif(string $noRawat): array
    {
        $response = $this->client()->get("pasien/{$noRawat}/alat-invasif");
        if ($response->successful()) {
            return $this->extractData($response->json());
        }

        return [];
    }

    /**
     * Tarik pasien rawat inap aktif dari SIMRS dan simpan ke tabel lokal
     * (reg_periksa, pasien, kamar_inap) agar petugas tidak perlu input manual.
     */
    public function syncPasienRanapAktif(array $params = []): array
    {
        if (! Schema::hasTable('kamar_inap') || ! Schema::hasTable('reg_periksa') || ! Schema::hasTable('pasien')) {
            throw new RuntimeException('Tabel SIMRS lokal (pasien/reg_periksa/kamar_inap) belum tersedia di SiHAI\'s. Jalankan migrasi/skema SIMRS terlebih dahulu.');
        }

        $data = $this->getPasienRanapAktif($params);

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $unmapped = 0;

        // Peta kode ruangan SIMRS -> kode bangsal/bed lokal melalui mapping.
        $ruanganMappings = BridgingMapping::query()
            ->where('mapping_type', 'ruangan')
            ->where('is_active', true)
            ->whereNotNull('local_code')
            ->pluck('local_code', 'external_code');

        DB::transaction(function () use ($data, $ruanganMappings, &$imported, &$updated, &$skipped, &$unmapped): void {
            foreach ($data as $item) {
                $noRawat = $item['no_rawat'] ?? null;
                $noRm = $item['no_rkm_medis'] ?? null;

                if (blank($noRawat) || blank($noRm)) {
                    $skipped++;

                    continue;
                }

                // 1) Upsert identitas pasien.
                $pasienAttributes = [
                    'nm_pasien' => $item['nama_pasien'] ?? $item['nm_pasien'] ?? '-',
                    'jk' => $this->normalizeJenisKelamin($item['jenis_kelamin'] ?? $item['jk'] ?? null) ?? 'L',
                    'tgl_lahir' => $item['tanggal_lahir'] ?? $item['tgl_lahir'] ?? null,
                    'no_ktp' => $item['nik'] ?? $item['no_ktp'] ?? null,
                    'alamat' => $item['alamat'] ?? null,
                    'umur' => $item['umur'] ?? null,
                ];

                $defaultKdPj = Schema::hasTable('penjab') ? (Penjab::query()->value('kd_pj') ?? '-') : '-';
                $defaultKdPoli = Schema::hasTable('poliklinik') ? (Poliklinik::query()->value('kd_poli') ?? '-') : '-';
                $defaultKdDokter = Schema::hasTable('dokter') ? (Dokter::query()->value('kd_dokter') ?? '-') : '-';

                $defaultKdProp = Schema::hasTable('propinsi') ? (DB::table('propinsi')->value('kd_prop') ?? 1) : 0;
                $defaultKdKab = Schema::hasTable('kabupaten') ? (DB::table('kabupaten')->value('kd_kab') ?? 1) : 0;
                $defaultKdKec = Schema::hasTable('kecamatan') ? (DB::table('kecamatan')->value('kd_kec') ?? 0) : 0;
                $defaultKdKel = Schema::hasTable('kelurahan') ? (DB::table('kelurahan')->value('kd_kel') ?? 1) : 0;
                $defaultSuku = Schema::hasTable('suku_bangsa') ? (DB::table('suku_bangsa')->value('id') ?? 1) : 0;
                $defaultBahasa = Schema::hasTable('bahasa_pasien') ? (DB::table('bahasa_pasien')->value('id') ?? 1) : 0;
                $defaultCacat = Schema::hasTable('cacat_fisik') ? (DB::table('cacat_fisik')->value('id') ?? 1) : 0;
                $defaultPerusahaan = Schema::hasTable('perusahaan_pasien') ? (DB::table('perusahaan_pasien')->value('kode_perusahaan') ?? '-') : '-';

                $extraPasienDefaults = [
                    'nm_ibu' => '-',
                    'pnd' => '-',
                    'namakeluarga' => '-',
                    'kd_pj' => $defaultKdPj,
                    'kd_kel' => $defaultKdKel,
                    'kd_kec' => $defaultKdKec,
                    'kd_kab' => $defaultKdKab,
                    'pekerjaanpj' => '-',
                    'alamatpj' => '-',
                    'kelurahanpj' => '-',
                    'kecamatanpj' => '-',
                    'kabupatenpj' => '-',
                    'perusahaan_pasien' => $defaultPerusahaan,
                    'suku_bangsa' => $defaultSuku,
                    'bahasa_pasien' => $defaultBahasa,
                    'cacat_fisik' => $defaultCacat,
                    'email' => '-',
                    'nip' => '-',
                    'kd_prop' => $defaultKdProp,
                    'propinsipj' => '-',
                ];
                foreach ($extraPasienDefaults as $col => $defVal) {
                    if (Schema::hasColumn('pasien', $col)) {
                        $pasienAttributes[$col] = $item[$col] ?? $defVal;
                    }
                }

                Pasien::query()->updateOrCreate(
                    ['no_rkm_medis' => $noRm],
                    $pasienAttributes,
                );

                // 2) Tentukan kode kamar lokal dari mapping ruangan.
                $kodeRuangan = $item['kode_ruangan'] ?? $item['kd_bangsal'] ?? null;
                $localBangsal = $kodeRuangan ? $ruanganMappings->get((string) $kodeRuangan) : null;

                if (blank($localBangsal)) {
                    $unmapped++;
                }

                $kdKamar = $this->resolveKamarLokal($item, $localBangsal);

                // 3) Pastikan reg_periksa ada.
                $tglMasuk = $this->normalizeDateTime($item['tanggal_masuk'] ?? $item['tgl_masuk'] ?? null);
                $regAttributes = [
                    'no_rkm_medis' => $noRm,
                    'tgl_registrasi' => $tglMasuk ? $tglMasuk->toDateString() : now()->toDateString(),
                    'jam_reg' => $tglMasuk ? $tglMasuk->format('H:i:s') : now()->format('H:i:s'),
                    'status_lanjut' => 'Ranap',
                    'stts' => 'Sudah',
                ];

                if (Schema::hasColumn('reg_periksa', 'kd_pj')) {
                    $regAttributes['kd_pj'] = $defaultKdPj;
                }
                if (Schema::hasColumn('reg_periksa', 'kd_poli')) {
                    $regAttributes['kd_poli'] = $defaultKdPoli;
                }
                if (Schema::hasColumn('reg_periksa', 'kd_dokter')) {
                    $regAttributes['kd_dokter'] = $defaultKdDokter;
                }
                if (Schema::hasColumn('reg_periksa', 'stts_daftar')) {
                    $regAttributes['stts_daftar'] = 'Lama';
                }
                if (Schema::hasColumn('reg_periksa', 'status_bayar')) {
                    $regAttributes['status_bayar'] = 'Belum Bayar';
                }
                if (Schema::hasColumn('reg_periksa', 'status_poli')) {
                    $regAttributes['status_poli'] = 'Lama';
                }

                RegPeriksa::query()->updateOrCreate(
                    ['no_rawat' => $noRawat],
                    $regAttributes,
                );

                // 4) Upsert kamar_inap (hanya untuk pasien yang masih aktif).
                if (blank($kdKamar)) {
                    $skipped++;

                    continue;
                }

                $existing = KamarInap::query()
                    ->where('no_rawat', $noRawat)
                    ->where(function ($q) {
                        $q->where('stts_pulang', '-')->orWhereNull('stts_pulang');
                    })
                    ->first();

                $attributes = [
                    'no_rawat' => $noRawat,
                    'kd_kamar' => $kdKamar,
                    'tgl_masuk' => $tglMasuk ? $tglMasuk->toDateString() : now()->toDateString(),
                    'jam_masuk' => $tglMasuk ? $tglMasuk->format('H:i:s') : now()->format('H:i:s'),
                    'stts_pulang' => '-',
                ];

                if ($existing) {
                    $existing->update([
                        'kd_kamar' => $kdKamar,
                    ]);
                    $updated++;

                    continue;
                }

                KamarInap::query()->create($attributes);
                $imported++;
            }
        });

        $message = "{$imported} pasien baru, {$updated} diperbarui, {$skipped} dilewati, {$unmapped} ruangan belum dipetakan.";
        $this->updateSyncStatus('success', $message);

        return [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'unmapped' => $unmapped,
            'total' => count($data),
        ];
    }

    /**
     * Update status pasien yang sudah pulang berdasarkan endpoint pulang.
     */
    public function syncPasienPulang(array $params = []): array
    {
        $data = $this->getPasienPulang($params);

        $updated = 0;

        DB::transaction(function () use ($data, &$updated): void {
            foreach ($data as $item) {
                $noRawat = $item['no_rawat'] ?? null;
                if (blank($noRawat)) {
                    continue;
                }

                $tglKeluar = $this->normalizeDateTime($item['tanggal_keluar'] ?? null);

                $affected = KamarInap::query()
                    ->where('no_rawat', $noRawat)
                    ->where(function ($q) {
                        $q->where('stts_pulang', '-')->orWhereNull('stts_pulang');
                    })
                    ->update([
                        'tgl_keluar' => $tglKeluar ? $tglKeluar->toDateString() : now()->toDateString(),
                        'jam_keluar' => $tglKeluar ? $tglKeluar->format('H:i:s') : now()->format('H:i:s'),
                        'stts_pulang' => $item['keadaan_pulang'] ?? $item['stts_pulang'] ?? 'Sehat',
                    ]);

                $updated += $affected;
            }
        });

        return ['updated' => $updated, 'total' => count($data)];
    }

    protected function normalizeJenisKelamin(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return match (strtoupper(trim($value))) {
            'L', 'LAKI-LAKI', 'LAKI LAKI', 'M', 'PRIA' => 'L',
            'P', 'PEREMPUAN', 'F', 'WANITA' => 'P',
            default => $value,
        };
    }

    protected function normalizeDateTime(mixed $value): ?\Illuminate\Support\Carbon
    {
        if (blank($value) || $value === '0000-00-00 00:00:00') {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Cari atau buat kode kamar lokal. Karena SIMRS bersifat eksternal,
     * kamar dibuat otomatis dari data API bila belum ada.
     * Prioritas: kode kamar dari SIMRS, lalu kamar pertama di bangsal hasil mapping.
     */
    protected function resolveKamarLokal(array $item, ?string $localBangsal): ?string
    {
        if (! Schema::hasTable('kamar')) {
            return null;
        }

        $kodeKamar = $item['kode_kamar'] ?? $item['kd_kamar'] ?? null;

        if (! blank($kodeKamar)) {
            $kamar = Kamar::query()->whereKey($kodeKamar)->first();

            if ($kamar) {
                return (string) $kamar->kd_kamar;
            }

            // Buat kamar baru dari data SIMRS.
            Kamar::query()->create([
                'kd_kamar' => (string) $kodeKamar,
                'kd_bangsal' => $localBangsal,
                'trf_kamar' => $item['trf_kamar'] ?? 0,
                'status' => 'KOSONG',
            ]);

            return (string) $kodeKamar;
        }

        if (! blank($localBangsal)) {
            $kamar = Kamar::query()->where('kd_bangsal', $localBangsal)->first();

            if ($kamar) {
                return (string) $kamar->kd_kamar;
            }

            // Belum ada kamar di bangsal ini, buat kamar default.
            $newKode = $localBangsal.'-01';
            Kamar::query()->create([
                'kd_kamar' => $newKode,
                'kd_bangsal' => $localBangsal,
                'trf_kamar' => 0,
                'status' => 'KOSONG',
            ]);

            return $newKode;
        }

        return null;
    }

    protected function extractData(mixed $payload): array
    {
        if (! is_array($payload)) {
            throw new RuntimeException('Respons SIMRS bukan JSON yang valid.');
        }

        $data = $payload['data'] ?? $payload;

        if (! is_array($data)) {
            throw new RuntimeException('Field data pada respons SIMRS harus berupa array.');
        }

        return $data;
    }

    public function updateSyncStatus(string $status, string $message): void
    {
        $this->setting->update([
            'last_sync_at' => now(),
            'last_sync_status' => $status,
            'last_sync_message' => $message,
        ]);
    }
}
