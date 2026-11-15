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
            $table->dateTime('tanggal');
            $table->string('nik', 20);
            $table->string('sebelum_menyentuh_pasien', 5)->nullable();
            $table->string('sebelum_tehnik_aseptik', 5)->nullable();
            $table->string('setelah_terpapar_cairan_tubuh_pasien', 5)->nullable();
            $table->string('setelah_kontak_dengan_pasien', 5)->nullable();
            $table->string('setelah_kontak_dengan_lingkungan_pasien', 5)->nullable();

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
