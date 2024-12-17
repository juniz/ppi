<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditPembuanganLimbah extends Model
{
    use HasFactory;

    protected $table = 'audit_pembuangan_limbah';
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'pemisahan_limbah_oleh_penghasil_limbah',
        'limbah_infeksius_dimasukkan_kantong_kuning',
        'limbah_noninfeksius_dimasukkan_kantong_hitam',
        'limbah_tigaperempat_diikat',
        'limbah_segera_dibawa_kepembuangan_sementara',
        'kotak_sampah_dalam_kondisi_bersih',
        'pembersihan_tempat_sampah_dengan_desinfekten',
        'pembersihan_penampungan_sementara_dengan_desinfekten',
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
