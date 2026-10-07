<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membuat skema tabel SIMRS (Khanza) minimum yang dibutuhkan SiHAI's.
 *
 * Tabel-tabel ini secara normal berasal dari dump database SIMRS. Migrasi ini
 * menyediakan struktur minimum agar SiHAI's dapat berjalan (terutama untuk
 * lingkungan pengembangan), tanpa menimpa tabel yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->createBangsal();
        $this->createKamar();
        $this->createDokter();
        $this->createPoliklinik();
        $this->createPenjab();
        $this->createPasien();
        $this->createRegPeriksa();
        $this->createKamarInap();
        $this->createDataHais();
        $this->createRuangAuditKepatuhan();
    }

    public function down(): void
    {
        // Sengaja TIDAK menghapus tabel SIMRS saat rollback.
        //
        // Tabel-tabel ini menyimpan data operasional rumah sakit (pasien, rawat inap,
        // data HAIs). Menghapusnya saat rollback sangat berbahaya dan tidak dapat
        // dipulihkan. Bila benar-benar perlu menghapus, lakukan secara manual.
    }

    protected function createBangsal(): void
    {
        if (Schema::hasTable('bangsal')) {
            return;
        }

        Schema::create('bangsal', function (Blueprint $table) {
            $table->char('kd_bangsal', 5)->primary();
            $table->string('nm_bangsal', 50)->nullable();
            $table->char('status', 1)->nullable();
        });
    }

    protected function createKamar(): void
    {
        if (Schema::hasTable('kamar')) {
            return;
        }

        Schema::create('kamar', function (Blueprint $table) {
            $table->char('kd_kamar', 15)->primary();
            $table->char('kd_bangsal', 5)->nullable();
            $table->string('trf_kamar', 20)->nullable();
            $table->char('status', 1)->nullable();
            $table->char('kelas', 3)->nullable();
            $table->char('statusdata', 1)->nullable();
        });
    }

    protected function createDokter(): void
    {
        if (Schema::hasTable('dokter')) {
            return;
        }

        Schema::create('dokter', function (Blueprint $table) {
            $table->char('kd_dokter', 20)->primary();
            $table->string('nm_dokter', 60)->nullable();
            $table->char('jk', 1)->nullable();
            $table->string('tmp_lahir', 60)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->char('gol_drh', 3)->nullable();
            $table->string('agama', 12)->nullable();
            $table->string('almt_tgl', 60)->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->char('stts_nikah', 15)->nullable();
            $table->string('kd_sps', 5)->nullable();
            $table->string('alumni', 60)->nullable();
            $table->string('no_ijn_praktek', 20)->nullable();
            $table->char('status', 1)->nullable();
        });
    }

    protected function createPoliklinik(): void
    {
        if (Schema::hasTable('poliklinik')) {
            return;
        }

        Schema::create('poliklinik', function (Blueprint $table) {
            $table->char('kd_poli', 5)->primary();
            $table->string('nm_poli', 50)->nullable();
        });
    }

    protected function createPenjab(): void
    {
        if (Schema::hasTable('penjab')) {
            return;
        }

        Schema::create('penjab', function (Blueprint $table) {
            $table->char('kd_pj', 3)->primary();
            $table->string('png_jawab', 50)->nullable();
        });
    }

    protected function createPasien(): void
    {
        if (Schema::hasTable('pasien')) {
            return;
        }

        Schema::create('pasien', function (Blueprint $table) {
            $table->char('no_rkm_medis', 15)->primary();
            $table->string('nm_pasien', 40)->nullable();
            $table->char('jk', 1)->nullable();
            $table->string('tmp_lahir', 30)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('nm_ibu', 40)->nullable();
            $table->string('alamat', 200)->nullable();
            $table->char('gol_darah', 3)->nullable();
            $table->string('pekerjaan', 30)->nullable();
            $table->char('stts_nikah', 15)->nullable();
            $table->string('agama', 12)->nullable();
            $table->date('tgl_daftar')->nullable();
            $table->string('no_tlp', 15)->nullable();
            $table->string('umur', 30)->nullable();
            $table->string('pnd', 5)->nullable();
            $table->string('keluarga', 20)->nullable();
            $table->string('namakeluarga', 40)->nullable();
            $table->char('kd_pj', 3)->nullable();
            $table->string('no_peserta', 20)->nullable();
            $table->string('kd_kel', 10)->nullable();
            $table->string('kd_kec', 10)->nullable();
            $table->string('kd_kab', 10)->nullable();
            $table->string('pekerjaanpj', 30)->nullable();
            $table->string('alamatpj', 200)->nullable();
            $table->string('kelurahanpj', 40)->nullable();
            $table->string('kecamatanpj', 40)->nullable();
            $table->string('kabupatenpj', 40)->nullable();
            $table->string('perusahaan_pasien', 10)->nullable();
            $table->string('suku_bangsa', 5)->nullable();
            $table->string('bahasa_pasien', 5)->nullable();
            $table->string('cacat_fisik', 5)->nullable();
            $table->string('email', 40)->nullable();
            $table->string('nip', 20)->nullable();
            $table->string('kd_prop', 10)->nullable();
            $table->string('propinsipj', 40)->nullable();
            $table->string('no_ktp', 20)->nullable();
        });
    }

    protected function createRegPeriksa(): void
    {
        if (Schema::hasTable('reg_periksa')) {
            return;
        }

        Schema::create('reg_periksa', function (Blueprint $table) {
            $table->bigIncrements('no_reg');
            $table->char('no_rawat', 17)->unique();
            $table->date('tgl_registrasi')->nullable();
            $table->time('jam_reg')->nullable();
            $table->timestamp('waktu_checkin')->nullable();
            $table->char('kd_dokter', 20)->nullable();
            $table->char('no_rkm_medis', 15)->nullable();
            $table->char('kd_poli', 5)->nullable();
            $table->string('p_jawab', 40)->nullable();
            $table->string('almt_pj', 200)->nullable();
            $table->string('hubunganpj', 20)->nullable();
            $table->decimal('biaya_reg', 10, 2)->nullable();
            $table->char('stts', 15)->nullable();
            $table->char('stts_daftar', 15)->nullable();
            $table->enum('status_lanjut', ['Ralan', 'Ranap'])->nullable();
            $table->char('kd_pj', 3)->nullable();
            $table->string('umurdaftar', 10)->nullable();
            $table->char('sttsumur', 10)->nullable();
            $table->char('status_bayar', 15)->nullable();
            $table->char('status_poli', 15)->nullable();
        });
    }

    protected function createKamarInap(): void
    {
        if (Schema::hasTable('kamar_inap')) {
            return;
        }

        Schema::create('kamar_inap', function (Blueprint $table) {
            $table->char('no_rawat', 17);
            $table->char('kd_kamar', 15)->nullable();
            $table->decimal('trf_kamar', 10, 2)->nullable();
            $table->string('diagnosa_awal', 60)->nullable();
            $table->string('diagnosa_akhir', 60)->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->time('jam_masuk')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('lama', 5)->nullable();
            $table->decimal('ttl_biaya', 10, 2)->nullable();
            $table->char('stts_pulang', 15)->nullable();

            $table->index(['no_rawat', 'tgl_masuk', 'jam_masuk']);
            $table->index('kd_kamar');
        });
    }

    protected function createDataHais(): void
    {
        if (Schema::hasTable('data_HAIs')) {
            return;
        }

        Schema::create('data_HAIs', function (Blueprint $table) {
            $table->date('tanggal');
            $table->char('no_rawat', 17);
            $table->integer('ETT')->default(0);
            $table->integer('CVL')->default(0);
            $table->integer('IVL')->default(0);
            $table->integer('UC')->default(0);
            $table->integer('VAP')->default(0);
            $table->integer('IAD')->default(0);
            $table->integer('PLEB')->default(0);
            $table->integer('ISK')->default(0);
            $table->integer('ILO')->default(0);
            $table->integer('HAP')->default(0);
            $table->integer('Tinea')->default(0);
            $table->integer('Scabies')->default(0);
            $table->char('DEKU', 5)->default('TIDAK');
            $table->char('SPUTUM', 5)->default('TIDAK');
            $table->char('DARAH', 5)->default('TIDAK');
            $table->char('URINE', 5)->default('TIDAK');
            $table->string('ANTIBIOTIK', 100)->default('-');
            $table->char('kd_kamar', 15)->nullable();

            $table->primary(['tanggal', 'no_rawat']);
        });
    }

    protected function createRuangAuditKepatuhan(): void
    {
        if (Schema::hasTable('ruang_audit_kepatuhan')) {
            return;
        }

        Schema::create('ruang_audit_kepatuhan', function (Blueprint $table) {
            $table->bigIncrements('id_ruang');
            $table->string('nama_ruang', 100)->nullable();
        });
    }
};
