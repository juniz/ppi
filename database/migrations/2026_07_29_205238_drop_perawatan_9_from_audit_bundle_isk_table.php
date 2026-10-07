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
        if (! Schema::hasTable('audit_bundle_isk') || ! Schema::hasColumn('audit_bundle_isk', 'perawatan_9_drainase_tertutup')) {
            return;
        }

        Schema::table('audit_bundle_isk', function (Blueprint $table) {
            $table->dropColumn('perawatan_9_drainase_tertutup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('audit_bundle_isk') || Schema::hasColumn('audit_bundle_isk', 'perawatan_9_drainase_tertutup')) {
            return;
        }

        Schema::table('audit_bundle_isk', function (Blueprint $table) {
            $table->enum('perawatan_9_drainase_tertutup', ['Ya', 'Tidak', 'NA'])->default('Ya');
        });
    }
};
