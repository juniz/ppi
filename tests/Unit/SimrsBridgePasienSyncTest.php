<?php

namespace Tests\Unit;

use App\Models\Bangsal;
use App\Models\BridgingMapping;
use App\Models\BridgingSetting;
use App\Models\Kamar;
use App\Models\KamarInap;
use App\Models\Pasien;
use App\Models\RegPeriksa;
use App\Services\SimrsBridgeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SimrsBridgePasienSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Gunakan SQLite in-memory agar tidak menyentuh database aplikasi.
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        DB::purge('testing');
        DB::setDefaultConnection('testing');

        $this->createMinimalSchema();
    }

    protected function createMinimalSchema(): void
    {
        Schema::connection('testing')->create('bridging_settings', function ($table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('simrs_name')->nullable();
            $table->string('base_url')->nullable();
            $table->string('auth_type')->default('bearer');
            $table->string('api_key_header')->default('Authorization');
            $table->text('api_token')->nullable();
            $table->integer('timeout')->default(30);
            $table->boolean('auto_sync')->default(false);
            $table->integer('auto_sync_interval')->default(15);
            $table->timestamp('last_sync_at')->nullable();
            $table->string('last_sync_status')->nullable();
            $table->text('last_sync_message')->nullable();
            $table->timestamps();
        });

        Schema::connection('testing')->create('bridging_mappings', function ($table) {
            $table->id();
            $table->string('mapping_type')->nullable();
            $table->string('external_code')->nullable();
            $table->string('external_name')->nullable();
            $table->string('local_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('testing')->create('bangsal', function ($table) {
            $table->string('kd_bangsal')->primary();
            $table->string('nm_bangsal')->nullable();
            $table->string('status')->nullable();
        });

        Schema::connection('testing')->create('kamar', function ($table) {
            $table->string('kd_kamar')->primary();
            $table->string('kd_bangsal')->nullable();
            $table->decimal('trf_kamar', 12, 2)->nullable();
            $table->string('status')->nullable();
        });

        Schema::connection('testing')->create('pasien', function ($table) {
            $table->string('no_rkm_medis')->primary();
            $table->string('nm_pasien')->nullable();
            $table->string('jk')->nullable();
            $table->string('tmp_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('nm_ibu')->nullable();
            $table->string('alamat')->nullable();
            $table->string('gol_darah')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('stts_nikah')->nullable();
            $table->string('agama')->nullable();
            $table->date('tgl_daftar')->nullable();
            $table->string('no_tlp')->nullable();
            $table->string('umur')->nullable();
            $table->string('pnd')->nullable();
            $table->string('keluarga')->nullable();
            $table->string('namakeluarga')->nullable();
            $table->string('kd_pj')->nullable();
            $table->string('no_peserta')->nullable();
            $table->string('kd_kel')->nullable();
            $table->string('kd_kec')->nullable();
            $table->string('kd_kab')->nullable();
            $table->string('pekerjaanpj')->nullable();
            $table->string('alamatpj')->nullable();
            $table->string('kelurahanpj')->nullable();
            $table->string('kecamatanpj')->nullable();
            $table->string('kabupatenpj')->nullable();
            $table->string('perusahaan_pasien')->nullable();
            $table->string('suku_bangsa')->nullable();
            $table->string('bahasa_pasien')->nullable();
            $table->string('cacat_fisik')->nullable();
            $table->string('email')->nullable();
            $table->string('nip')->nullable();
            $table->string('kd_prop')->nullable();
            $table->string('propinsipj')->nullable();
            $table->string('no_ktp')->nullable();
        });

        Schema::connection('testing')->create('reg_periksa', function ($table) {
            $table->string('no_rawat')->primary();
            $table->string('no_rkm_medis')->nullable();
            $table->date('tgl_registrasi')->nullable();
            $table->string('jam_reg')->nullable();
            $table->string('status_lanjut')->nullable();
            $table->string('stts')->nullable();
        });

        Schema::connection('testing')->create('kamar_inap', function ($table) {
            $table->string('no_rawat');
            $table->string('kd_kamar')->nullable();
            $table->decimal('trf_kamar', 12, 2)->nullable();
            $table->string('diagnosa_awal')->nullable();
            $table->string('diagnosa_akhir')->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->string('jam_masuk')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->string('jam_keluar')->nullable();
            $table->string('lama')->nullable();
            $table->decimal('ttl_biaya', 12, 2)->nullable();
            $table->string('stts_pulang')->nullable();
        });
    }

    protected function makeSetting(): BridgingSetting
    {
        return new BridgingSetting([
            'base_url' => 'https://simrs.example.test/api/v1',
            'auth_type' => 'bearer',
            'api_token' => 'secret-token',
            'timeout' => 10,
        ]);
    }

    public function test_sync_pasien_ranap_aktif_membuat_data_lokal(): void
    {
        Http::fake([
            'https://simrs.example.test/api/v1/pasien-ranap/aktif*' => Http::response([
                'status' => 'success',
                'data' => [[
                    'no_rawat' => '2026/10/07/000123',
                    'no_rkm_medis' => '00123456',
                    'nama_pasien' => 'Budi Santoso',
                    'jenis_kelamin' => 'L',
                    'tanggal_lahir' => '1985-04-12',
                    'tanggal_masuk' => '2026-10-05 14:30:00',
                    'kode_ruangan' => 'B01',
                    'kode_kamar' => 'MEL-01A',
                ]],
            ]),
        ]);

        Bangsal::query()->create(['kd_bangsal' => 'B01', 'nm_bangsal' => 'Ruang Melati', 'status' => '1']);
        Kamar::query()->create(['kd_kamar' => 'MEL-01A', 'kd_bangsal' => 'B01', 'status' => 'KOSONG']);
        BridgingMapping::query()->create([
            'mapping_type' => 'ruangan',
            'external_code' => 'B01',
            'local_code' => 'B01',
            'is_active' => true,
        ]);

        $result = (new SimrsBridgeService($this->makeSetting()))->syncPasienRanapAktif();

        $this->assertSame(1, $result['imported']);
        $this->assertSame(0, $result['unmapped']);

        $this->assertDatabaseHas('pasien', ['no_rkm_medis' => '00123456', 'nm_pasien' => 'Budi Santoso', 'jk' => 'L'], 'testing');
        $this->assertDatabaseHas('reg_periksa', ['no_rawat' => '2026/10/07/000123', 'no_rkm_medis' => '00123456', 'status_lanjut' => 'Ranap'], 'testing');
        $this->assertDatabaseHas('kamar_inap', ['no_rawat' => '2026/10/07/000123', 'kd_kamar' => 'MEL-01A', 'stts_pulang' => '-'], 'testing');
    }

    public function test_sync_pasien_ranap_aktif_idempoten(): void
    {
        Http::fake([
            'https://simrs.example.test/api/v1/pasien-ranap/aktif*' => Http::response([
                'status' => 'success',
                'data' => [[
                    'no_rawat' => '2026/10/07/000123',
                    'no_rkm_medis' => '00123456',
                    'nama_pasien' => 'Budi Santoso',
                    'tanggal_masuk' => '2026-10-05 14:30:00',
                    'kode_kamar' => 'MEL-01A',
                ]],
            ]),
        ]);

        Kamar::query()->create(['kd_kamar' => 'MEL-01A', 'kd_bangsal' => 'B01', 'status' => 'KOSONG']);

        $service = new SimrsBridgeService($this->makeSetting());
        $service->syncPasienRanapAktif();
        $second = $service->syncPasienRanapAktif();

        $this->assertSame(0, $second['imported']);
        $this->assertSame(1, $second['updated']);
        $this->assertSame(1, KamarInap::query()->where('no_rawat', '2026/10/07/000123')->count());
    }

    public function test_sync_pasien_pulang_mengubah_status(): void
    {
        Http::fake([
            'https://simrs.example.test/api/v1/pasien-ranap/pulang*' => Http::response([
                'status' => 'success',
                'data' => [[
                    'no_rawat' => '2026/10/01/000045',
                    'keadaan_pulang' => 'Sembuh',
                    'tanggal_keluar' => '2026-10-06 11:30:00',
                ]],
            ]),
        ]);

        KamarInap::query()->create([
            'no_rawat' => '2026/10/01/000045',
            'kd_kamar' => 'MEL-01A',
            'tgl_masuk' => '2026-10-01',
            'jam_masuk' => '09:00:00',
            'stts_pulang' => '-',
        ]);

        $result = (new SimrsBridgeService($this->makeSetting()))->syncPasienPulang();

        $this->assertSame(1, $result['updated']);
        $this->assertDatabaseHas('kamar_inap', [
            'no_rawat' => '2026/10/01/000045',
            'stts_pulang' => 'Sembuh',
            'tgl_keluar' => '2026-10-06',
        ], 'testing');
    }
}
