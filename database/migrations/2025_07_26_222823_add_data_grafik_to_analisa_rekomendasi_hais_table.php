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
            $table->json('data_hap')->nullable()->after('rekomendasi');
            $table->json('data_iad')->nullable()->after('data_hap');
            $table->json('data_ilo')->nullable()->after('data_iad');
            $table->json('data_isk')->nullable()->after('data_ilo');
            $table->json('data_plebitis')->nullable()->after('data_isk');
            $table->json('data_vap')->nullable()->after('data_plebitis');
            $table->json('summary_laju')->nullable()->after('data_vap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_rekomendasi_hais', function (Blueprint $table) {
            $table->dropColumn([
                'data_hap',
                'data_iad', 
                'data_ilo',
                'data_isk',
                'data_plebitis',
                'data_vap',
                'summary_laju'
            ]);
        });
    }
};
