<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class AuditKepatuhanApd extends Model
{
    use HasFactory;

    protected $table = 'audit_kepatuhan_apd';
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nik',
        'tindakan',
        'tanggal',
        'topi',
        'masker',
        'kacamata',
        'sarungtangan',
        'apron',
        'sepatu',
    ];


    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }
}
