<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bridging_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('mapping_type', 50)->default('ruangan');
            $table->string('external_code', 100);
            $table->string('external_name')->nullable();
            $table->string('local_code', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['mapping_type', 'external_code']);
            $table->index(['mapping_type', 'is_active', 'local_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bridging_mappings');
    }
};
