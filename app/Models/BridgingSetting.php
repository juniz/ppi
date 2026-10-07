<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BridgingSetting extends Model
{
    protected $table = 'bridging_settings';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_sync' => 'boolean',
        'api_token' => 'encrypted',
        'last_sync_at' => 'datetime',
        'last_connection_at' => 'datetime',
    ];

    protected $hidden = [
        'api_token',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'is_active' => false,
                'simrs_name' => 'SIMRS Utama',
                'base_url' => 'https://simrs.faskes.id/api/v1',
                'auth_type' => 'bearer',
                'api_key_header' => 'Authorization',
                'api_token' => '',
                'timeout' => 30,
                'auto_sync' => false,
                'auto_sync_interval' => 15,
            ]
        );
    }
}
