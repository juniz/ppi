<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyIndex extends Model
{
    use HasFactory;

    protected $table = 'emergency_index';
    protected $primaryKey = 'kode_emergency';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'kode_emergency',
        'nama_emergency',
        'indek',
    ];
}
