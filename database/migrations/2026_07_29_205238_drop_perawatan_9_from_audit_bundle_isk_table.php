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
        Schema::table('audit_bundle_isk', function (Blueprint $table) {
            $table->dropColumn('perawatan_9_drainase_tertutup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_bundle_isk', function (Blueprint $table) {
            $table->enum('perawatan_9_drainase_tertutup', ['Ya', 'Tidak', 'NA'])->default('Ya');
        });
    }
};
