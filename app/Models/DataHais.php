<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataHais extends Model
{
    use HasFactory;

    protected $table = 'data_HAIs';
    public $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'no_rawat',
        'ETT',
        'CVL',
        'IVL',
        'UC',
        'VAP',
        'IAD',
        'PLEB',
        'ISK',
        'ILO',
        'HAP',
        'Tinea',
        'Scabies',
        'DEKU',
        'SPUTUM',
        'DARAH',
        'URINE',
        'ANTIBIOTIK',
        'kd_kamar',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kd_kamar', 'kd_kamar');
    }

    public function regPeriksa()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
}
