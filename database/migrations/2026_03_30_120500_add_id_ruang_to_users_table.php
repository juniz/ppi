<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'id_ruang')) {
                $table->string('id_ruang', 5)->nullable()->after('nip');
                $table->index('id_ruang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_ruang')) {
                $table->dropIndex(['id_ruang']);
                $table->dropColumn('id_ruang');
            }
        });
    }
};
