<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bridging_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('simrs_name')->default('SIMRS Utama');
            $table->string('base_url')->nullable();
            $table->string('auth_type')->default('bearer'); // bearer, api_key, basic, none
            $table->string('api_key_header')->default('Authorization'); // e.g. Authorization or X-API-KEY
            $table->text('api_token')->nullable();
            $table->integer('timeout')->default(30);
            $table->boolean('auto_sync')->default(false);
            $table->integer('auto_sync_interval')->default(15); // in minutes
            $table->timestamp('last_sync_at')->nullable();
            $table->string('last_sync_status')->nullable(); // success, failed
            $table->text('last_sync_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bridging_settings');
    }
};
