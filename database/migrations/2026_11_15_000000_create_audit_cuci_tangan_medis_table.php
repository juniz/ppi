<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_cuci_tangan_medis')) {
            return;
        }

        Schema::create('audit_cuci_tangan_medis', function (Blueprint $table): void {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';

            $table->dateTime('tanggal');
            $table->string('nik', 20);
            $table->enum('sebelum_menyentuh_pasien', ['Ya', 'Tidak'])->nullable();
            $table->enum('sebelum_tehnik_aseptik', ['Ya', 'Tidak'])->nullable();
            $table->enum('setelah_terpapar_cairan_tubuh_pasien', ['Ya', 'Tidak'])->nullable();
            $table->enum('setelah_kontak_dengan_pasien', ['Ya', 'Tidak'])->nullable();
            $table->enum('setelah_kontak_dengan_lingkungan_pasien', ['Ya', 'Tidak'])->nullable();

            $table->primary(['tanggal', 'nik']);
            $table->foreign('nik')
                ->references('nik')
                ->on('pegawai')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Audit records are intentionally preserved on rollback.
    }
};
