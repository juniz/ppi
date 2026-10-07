<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bridging_settings', function (Blueprint $table) {
            $table->dateTime('last_connection_at')->nullable()->after('auto_sync_interval');
            $table->string('last_connection_status')->nullable()->after('last_connection_at');
            $table->unsignedInteger('last_connection_latency_ms')->nullable()->after('last_connection_status');
            $table->text('last_connection_message')->nullable()->after('last_connection_latency_ms');
        });

        DB::table('bridging_settings')->where('api_token', '')->update(['api_token' => null]);

        DB::table('bridging_settings')
            ->whereNotNull('api_token')
            ->where('api_token', '!=', '')
            ->orderBy('id')
            ->eachById(function (object $setting): void {
                DB::table('bridging_settings')
                    ->where('id', $setting->id)
                    ->update(['api_token' => Crypt::encryptString($setting->api_token)]);
            });
    }

    public function down(): void
    {
        DB::table('bridging_settings')
            ->whereNotNull('api_token')
            ->where('api_token', '!=', '')
            ->orderBy('id')
            ->eachById(function (object $setting): void {
                DB::table('bridging_settings')
                    ->where('id', $setting->id)
                    ->update(['api_token' => Crypt::decryptString($setting->api_token)]);
            });

        Schema::table('bridging_settings', function (Blueprint $table) {
            $table->dropColumn([
                'last_connection_at',
                'last_connection_status',
                'last_connection_latency_ms',
                'last_connection_message',
            ]);
        });
    }
};
