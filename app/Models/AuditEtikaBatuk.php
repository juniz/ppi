<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditEtikaBatuk extends Model
{
    use HasFactory;

    protected $table = 'audit_etika_batuk';
    protected $primaryKey = 'tanggal';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'tanggal',
        'nik',
        'tutuk_mulut',
        'buang_tissue',
        'tisue_tutup_siku',
        'kebersihan_tangan',
        'gunakan_masker',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'tanggal' => Carbon::now()->format('Y-m-d H:i:s'),
        ], true);
        parent::__construct($attributes);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }
}
