<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditBundleVap extends Model
{
    use HasFactory;

    protected $table = 'audit_bundle_vap';
    protected $primaryKey = 'tanggal';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'posisi_kepala',
        'pengkajian_setiap_hari',
        'hand_hygiene',
        'oral_hygiene',
        'suction_manajemen_sekresi',
        'profilaksis_peptic_ulcer',
        'dvt_profiklasisi',
        'penggunaan_apd_sesuai',
        'no_rawat',
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
