<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Spesialis extends Model
{
    protected $table = 'spesialis';
    protected $primaryKey = 'kd_sps';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_sps',
        'nm_sps'
    ];

    public function dokter(): HasMany
    {
        return $this->hasMany(Dokter::class, 'kd_sps', 'kd_sps');
    }
} 