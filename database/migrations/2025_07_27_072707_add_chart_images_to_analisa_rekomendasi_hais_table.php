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
            $table->string('chart_infeksi_image')->nullable()->after('rata_vap_laju');
            $table->string('chart_pemasangan_image')->nullable()->after('chart_infeksi_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_rekomendasi_hais', function (Blueprint $table) {
            $table->dropColumn(['chart_infeksi_image', 'chart_pemasangan_image']);
        });
    }
};
