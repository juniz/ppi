<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';
    protected $primaryKey = 'kd_kamar';
    public $timestamps = false;

    protected $fillable = [
        'kd_kamar',
        'kd_bangsal',
        'trf_kamar',
        'status',
        'kelas',
        'status_data',
    ];

    public function bangsal()
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }
}
