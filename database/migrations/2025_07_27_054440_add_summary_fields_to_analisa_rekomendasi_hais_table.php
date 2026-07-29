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
        Schema::table('analisa_rekomendasi_hais', function (Blueprint $table) {
            // Summary fields untuk HAP
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_hap_kasus')) $table->integer('total_hap_kasus')->default(0)->after('summary_laju');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_hap_hari_rawat')) $table->integer('total_hap_hari_rawat')->default(0)->after('total_hap_kasus');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'rata_hap_laju')) $table->decimal('rata_hap_laju', 8, 2)->default(0)->after('total_hap_hari_rawat');
            
            // Summary fields untuk IAD
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_iad_kasus')) $table->integer('total_iad_kasus')->default(0)->after('rata_hap_laju');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_iad_hari_terpasang')) $table->integer('total_iad_hari_terpasang')->default(0)->after('total_iad_kasus');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'rata_iad_laju')) $table->decimal('rata_iad_laju', 8, 2)->default(0)->after('total_iad_hari_terpasang');
            
            // Summary fields untuk ILO
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_ilo_kasus')) $table->integer('total_ilo_kasus')->default(0)->after('rata_iad_laju');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_ilo_hari_operasi')) $table->integer('total_ilo_hari_operasi')->default(0)->after('total_ilo_kasus');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'rata_ilo_laju')) $table->decimal('rata_ilo_laju', 8, 2)->default(0)->after('total_ilo_hari_operasi');
            
            // Summary fields untuk ISK
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_isk_kasus')) $table->integer('total_isk_kasus')->default(0)->after('rata_ilo_laju');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_isk_hari_kateter')) $table->integer('total_isk_hari_kateter')->default(0)->after('total_isk_kasus');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'rata_isk_laju')) $table->decimal('rata_isk_laju', 8, 2)->default(0)->after('total_isk_hari_kateter');
            
            // Summary fields untuk Plebitis
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_plebitis_kasus')) $table->integer('total_plebitis_kasus')->default(0)->after('rata_isk_laju');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_plebitis_hari_infus')) $table->integer('total_plebitis_hari_infus')->default(0)->after('total_plebitis_kasus');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'rata_plebitis_laju')) $table->decimal('rata_plebitis_laju', 8, 2)->default(0)->after('total_plebitis_hari_infus');
            
            // Summary fields untuk VAP
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_vap_kasus')) $table->integer('total_vap_kasus')->default(0)->after('rata_plebitis_laju');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'total_vap_hari_ventilator')) $table->integer('total_vap_hari_ventilator')->default(0)->after('total_vap_kasus');
            if (!Schema::hasColumn('analisa_rekomendasi_hais', 'rata_vap_laju')) $table->decimal('rata_vap_laju', 8, 2)->default(0)->after('total_vap_hari_ventilator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_rekomendasi_hais', function (Blueprint $table) {
            $table->dropColumn([
                'total_hap_kasus', 'total_hap_hari_rawat', 'rata_hap_laju',
                'total_iad_kasus', 'total_iad_hari_terpasang', 'rata_iad_laju',
                'total_ilo_kasus', 'total_ilo_hari_operasi', 'rata_ilo_laju',
                'total_isk_kasus', 'total_isk_hari_kateter', 'rata_isk_laju',
                'total_plebitis_kasus', 'total_plebitis_hari_infus', 'rata_plebitis_laju',
                'total_vap_kasus', 'total_vap_hari_ventilator', 'rata_vap_laju'
            ]);
        });
    }
};
