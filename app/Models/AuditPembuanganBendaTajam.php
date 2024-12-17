<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditPembuanganBendaTajam extends Model
{
    use HasFactory;

    protected $table = 'audit_pembuangan_benda_tajam';
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'setiap_injeksi_needle_langsung_dimasukkan_safety_box',
        'setiap_pemasangan_iv_canula_langsung_dimasukkan_safety_box',
        'setiap_benda_tajam_jarum_dimasukkan_safety_box',
        'safety_box_tigaperempat_diganti',
        'safety_box_keadaan_bersih',
        'saftey_box_tertutup_setelah_digunakan',
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
