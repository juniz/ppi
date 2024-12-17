<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditPenangananDarah extends Model
{
    use HasFactory;

    protected $table = 'audit_penanganan_darah';
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'menggunakan_apd_waktu_membuang_darah',
        'komponen_darah_tidak_ada_dilantai',
        'membuang_darah_pada_tempat_ditentukan',
        'pembersihan_areal_tumbahan_darah',
        'apd_dibuang_di_limbah_infeksius',
        'melakukan_kebersihan_tangan_setelah_prosedur',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'tanggal' => Carbon::now()->format('Y-m-d H:i:s'),
        ], true);
        parent::__construct($attributes);
    }

    public function ruangAuditKepatuhan()
    {
        return $this->belongsTo(RuangAuditKepatuhan::class, 'id_ruang', 'id_ruang');
    }
}
