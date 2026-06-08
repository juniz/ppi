<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $auditCounts = [
        'audit_etika_batuk' => 6,
        'audit_penempatan_pasien' => 6,
        'audit_pembuangan_limbah' => 13,
        'audit_pengendalian_lingkungan' => 20,
        'audit_penatalaksanaan_peralatan' => 16,
        'audit_fasilitas_apd' => 11,
        'audit_fasilitas_cuci_tangan' => 6,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->auditCounts as $table => $count) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            for ($i = 1; $i <= $count; $i++) {
                $column = "audit{$i}";

                if (Schema::hasColumn($table, $column)) {
                    DB::statement("ALTER TABLE {$table} MODIFY {$column} ENUM('Ya', 'Tidak', 'Na') NULL DEFAULT 'Ya'");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->auditCounts as $table => $count) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            for ($i = 1; $i <= $count; $i++) {
                $column = "audit{$i}";

                if (Schema::hasColumn($table, $column)) {
                    DB::statement("ALTER TABLE {$table} MODIFY {$column} ENUM('Ya', 'Tidak') NULL DEFAULT 'Ya'");
                }
            }
        }
    }
};
