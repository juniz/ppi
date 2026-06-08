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
        Schema::create('audit_etika_batuk', function (Blueprint $table) {
            $table->dateTime('tanggal')->primary();
            $table->string('nik', 20)->charset('latin1')->collation('latin1_swedish_ci');
            $table->enum('tutup_mulut', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('buang_tissue', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('tisue_tutup_siku', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('kebersihan_tangan', ['Ya', 'Tidak'])->default('Ya');
            $table->enum('gunakan_masker', ['Ya', 'Tidak'])->default('Ya');

            $table->index('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_etika_batuk');
    }
};
