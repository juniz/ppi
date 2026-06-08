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
        Schema::create('audit_injeksi_aman', function (Blueprint $table) {
            $table->dateTime('tanggal');
            $table->string('id_ruang', 5);

            for ($i = 1; $i <= 18; $i++) {
                $table->enum("audit{$i}", ['Ya', 'Tidak', 'Na'])->default('Ya');
            }

            $table->primary(['tanggal', 'id_ruang']);
            $table->index('id_ruang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_injeksi_aman');
    }
};
