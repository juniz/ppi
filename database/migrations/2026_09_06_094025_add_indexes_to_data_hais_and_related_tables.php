<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── data_HAIs ───────────────────────────────────────────────────────────
        // Cek apakah index sudah ada sebelum menambahkan (aman untuk re-run)
        $this->addIndexIfNotExists('data_HAIs', 'data_hais_tanggal_index', function (Blueprint $table) {
            $table->index('tanggal', 'data_hais_tanggal_index');
        });

        $this->addIndexIfNotExists('data_HAIs', 'data_hais_no_rawat_index', function (Blueprint $table) {
            $table->index('no_rawat', 'data_hais_no_rawat_index');
        });

        $this->addIndexIfNotExists('data_HAIs', 'data_hais_kd_kamar_index', function (Blueprint $table) {
            $table->index('kd_kamar', 'data_hais_kd_kamar_index');
        });

        // Composite index untuk query paling umum: filter tanggal + join kamar
        $this->addIndexIfNotExists('data_HAIs', 'data_hais_no_rawat_tanggal_index', function (Blueprint $table) {
            $table->index(['no_rawat', 'tanggal'], 'data_hais_no_rawat_tanggal_index');
        });

        $this->addIndexIfNotExists('data_HAIs', 'data_hais_kd_kamar_tanggal_index', function (Blueprint $table) {
            $table->index(['kd_kamar', 'tanggal'], 'data_hais_kd_kamar_tanggal_index');
        });

        // ─── analisa_rekomendasi_hais ─────────────────────────────────────────────
        $this->addIndexIfNotExists('analisa_rekomendasi_hais', 'analisa_ruangan_tgl_index', function (Blueprint $table) {
            $table->index(['ruangan', 'tanggal_mulai', 'tanggal_selesai'], 'analisa_ruangan_tgl_index');
        });

        // ─── audit_bundle_* — index no_rawat ─────────────────────────────────────
        foreach ([
            'audit_bundle_iadp',
            'audit_bundle_ido',
            'audit_bundle_isk',
            'audit_bundle_plabsi',
            'audit_bundle_vap',
        ] as $bundleTable) {
            if (Schema::hasTable($bundleTable)) {
                $indexName = "{$bundleTable}_no_rawat_index";
                $this->addIndexIfNotExists($bundleTable, $indexName, function (Blueprint $table) use ($indexName) {
                    $table->index('no_rawat', $indexName);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('data_HAIs', function (Blueprint $table) {
            $table->dropIndexIfExists('data_hais_tanggal_index');
            $table->dropIndexIfExists('data_hais_no_rawat_index');
            $table->dropIndexIfExists('data_hais_kd_kamar_index');
            $table->dropIndexIfExists('data_hais_no_rawat_tanggal_index');
            $table->dropIndexIfExists('data_hais_kd_kamar_tanggal_index');
        });

        Schema::table('analisa_rekomendasi_hais', function (Blueprint $table) {
            $table->dropIndexIfExists('analisa_ruangan_tgl_index');
        });

        foreach ([
            'audit_bundle_iadp',
            'audit_bundle_ido',
            'audit_bundle_isk',
            'audit_bundle_plabsi',
            'audit_bundle_vap',
        ] as $bundleTable) {
            if (Schema::hasTable($bundleTable)) {
                Schema::table($bundleTable, function (Blueprint $table) use ($bundleTable) {
                    $table->dropIndexIfExists("{$bundleTable}_no_rawat_index");
                });
            }
        }
    }

    /**
     * Tambah index hanya jika belum ada, aman dijalankan di DB yang sudah ada datanya.
     */
    private function addIndexIfNotExists(string $tableName, string $indexName, callable $callback): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        $exists = collect(DB::select("SHOW INDEX FROM `{$tableName}`"))
            ->pluck('Key_name')
            ->contains($indexName);

        if (!$exists) {
            Schema::table($tableName, $callback);
        }
    }
};
