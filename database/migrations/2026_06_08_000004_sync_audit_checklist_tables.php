<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $auditCounts = [
        'audit_etika_batuk' => 6,
        'audit_pembuangan_limbah' => 13,
    ];

    private array $newTables = [
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
            Schema::table($table, function (Blueprint $blueprint) use ($table, $count) {
                for ($i = 1; $i <= $count; $i++) {
                    $column = "audit{$i}";

                    if (! Schema::hasColumn($table, $column)) {
                        $blueprint->enum($column, ['Ya', 'Tidak', 'Na'])->default('Ya');
                    }
                }
            });
        }

        for ($i = 1; $i <= 6; $i++) {
            DB::statement("ALTER TABLE audit_penempatan_pasien MODIFY audit{$i} ENUM('Ya', 'Tidak', 'Na') NULL DEFAULT 'Ya'");
        }

        foreach ($this->newTables as $table => $count) {
            if (! Schema::hasTable($table)) {
                Schema::create($table, function (Blueprint $blueprint) use ($count) {
                    $blueprint->dateTime('tanggal');
                    $blueprint->string('id_ruang', 5);

                    for ($i = 1; $i <= $count; $i++) {
                        $blueprint->enum("audit{$i}", ['Ya', 'Tidak', 'Na'])->default('Ya');
                    }

                    $blueprint->primary(['tanggal', 'id_ruang']);
                    $blueprint->index('id_ruang');
                });

                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $count) {
                if (! Schema::hasColumn($table, 'tanggal')) {
                    $blueprint->dateTime('tanggal');
                }

                if (! Schema::hasColumn($table, 'id_ruang')) {
                    $blueprint->string('id_ruang', 5);
                }

                for ($i = 1; $i <= $count; $i++) {
                    $column = "audit{$i}";

                    if (! Schema::hasColumn($table, $column)) {
                        $blueprint->enum($column, ['Ya', 'Tidak', 'Na'])->default('Ya');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (array_reverse($this->newTables, true) as $table => $count) {
            Schema::dropIfExists($table);
        }

        for ($i = 1; $i <= 6; $i++) {
            DB::statement("ALTER TABLE audit_penempatan_pasien MODIFY audit{$i} ENUM('Ya', 'Tidak') NULL DEFAULT 'Ya'");
        }

        foreach ($this->auditCounts as $table => $count) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $count) {
                for ($i = $count; $i >= 1; $i--) {
                    $column = "audit{$i}";

                    if (Schema::hasColumn($table, $column)) {
                        $blueprint->dropColumn($column);
                    }
                }
            });
        }
    }
};
