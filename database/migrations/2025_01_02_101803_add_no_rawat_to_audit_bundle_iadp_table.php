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
        Schema::table('audit_bundle_iadp', function (Blueprint $table) {
            $table->string('no_rawat', 20)->nullable()->after('perawatan_rutin')->charset('latin1')->collation('latin1_swedish_ci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_bundle_iadp', function (Blueprint $table) {
            $table->dropColumn('no_rawat');
        });
    }
};
