<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BridgingMapping extends Model
{
    protected $fillable = [
        'mapping_type',
        'external_code',
        'external_name',
        'local_code',
        'is_active',
        'last_seen_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function localBangsal(): BelongsTo
    {
        return $this->belongsTo(Bangsal::class, 'local_code', 'kd_bangsal');
    }
}
