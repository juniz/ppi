<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('audit_bundle_isk')) {
            return;
        }

        $oldColumns = [
            'pemasangan_sesuai_indikasi',
            'hand_hygiene',
            'menggunakan_apd_yang_tepat',
            'pemasangan_menggunakan_alat_steril',
            'segera_dilepas_setelah_tidak_diperlukan',
            'pengisian_balon_sesuai_petunjuk',
            'fiksasi_kateter_dengan_plester',
            'urinebag_menggantung_tidak_menyentuh_lantai',
        ];

        $newColumns = [
            'pemasangan_1_indikasi',
            'pemasangan_2_hand_hygiene',
            'pemasangan_3_teknik_aseptik',
            'pemasangan_4_alat_steril',
            'perawatan_1_hand_hygiene',
            'perawatan_2_genitalia_dibersihkan',
            'perawatan_3_fiksasi_kateter',
            'perawatan_4_tidak_diganti_rutin',
            'perawatan_5_aliran_steril_tertutup',
            'perawatan_6_hubungan_kateter_tertutup',
            'perawatan_7_urine_bag_tidak_di_lantai',
            'perawatan_8_selang_tidak_terlipat',
            'perawatan_9_drainase_tertutup',
            'perawatan_10_segera_dilepas',
        ];

        // Drop kolom lama yang benar-benar ada.
        $existingOld = array_filter($oldColumns, fn ($c) => Schema::hasColumn('audit_bundle_isk', $c));
        if (! empty($existingOld)) {
            Schema::table('audit_bundle_isk', function (Blueprint $table) use ($existingOld) {
                $table->dropColumn(array_values($existingOld));
            });
        }

        // Tambah kolom baru yang belum ada.
        Schema::table('audit_bundle_isk', function (Blueprint $table) use ($newColumns) {
            $map = [
                'pemasangan_1_indikasi' => 'pemasangan_1_indikasi',
                'pemasangan_2_hand_hygiene' => 'pemasangan_2_hand_hygiene',
                'pemasangan_3_teknik_aseptik' => 'pemasangan_3_teknik_aseptik',
                'pemasangan_4_alat_steril' => 'pemasangan_4_alat_steril',
                'perawatan_1_hand_hygiene' => 'perawatan_1_hand_hygiene',
                'perawatan_2_genitalia_dibersihkan' => 'perawatan_2_genitalia_dibersihkan',
                'perawatan_3_fiksasi_kateter' => 'perawatan_3_fiksasi_kateter',
                'perawatan_4_tidak_diganti_rutin' => 'perawatan_4_tidak_diganti_rutin',
                'perawatan_5_aliran_steril_tertutup' => 'perawatan_5_aliran_steril_tertutup',
                'perawatan_6_hubungan_kateter_tertutup' => 'perawatan_6_hubungan_kateter_tertutup',
                'perawatan_7_urine_bag_tidak_di_lantai' => 'perawatan_7_urine_bag_tidak_di_lantai',
                'perawatan_8_selang_tidak_terlipat' => 'perawatan_8_selang_tidak_terlipat',
                'perawatan_9_drainase_tertutup' => 'perawatan_9_drainase_tertutup',
                'perawatan_10_segera_dilepas' => 'perawatan_10_segera_dilepas',
            ];

            foreach ($map as $column) {
                if (! Schema::hasColumn('audit_bundle_isk', $column)) {
                    $table->enum($column, ['Ya', 'Tidak', 'NA'])->default('Ya');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('audit_bundle_isk')) {
            return;
        }

        Schema::table('audit_bundle_isk', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'pemasangan_1_indikasi',
                'pemasangan_2_hand_hygiene',
                'pemasangan_3_teknik_aseptik',
                'pemasangan_4_alat_steril',
                'perawatan_1_hand_hygiene',
                'perawatan_2_genitalia_dibersihkan',
                'perawatan_3_fiksasi_kateter',
                'perawatan_4_tidak_diganti_rutin',
                'perawatan_5_aliran_steril_tertutup',
                'perawatan_6_hubungan_kateter_tertutup',
                'perawatan_7_urine_bag_tidak_di_lantai',
                'perawatan_8_selang_tidak_terlipat',
                'perawatan_9_drainase_tertutup',
                'perawatan_10_segera_dilepas',
            ]);

            // Re-add old columns
            $table->enum('pemasangan_sesuai_indikasi', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('hand_hygiene', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('menggunakan_apd_yang_tepat', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('pemasangan_menggunakan_alat_steril', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('segera_dilepas_setelah_tidak_diperlukan', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('pengisian_balon_sesuai_petunjuk', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('fiksasi_kateter_dengan_plester', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('urinebag_menggantung_tidak_menyentuh_lantai', ['Ya', 'Tidak'])->default('Ya');
        });
    }
};
