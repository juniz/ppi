<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditBundlePlabsi extends Model
{
    use HasFactory;

    protected $table = 'audit_bundle_plabsi';
    protected $primaryKey = 'tanggal';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'sebelum_melakukan_hand_hygiene',
        'menggunakan_apd_lengkap',
        'lokasi_pemasangan_sesuai',
        'alat_yang_digunakan_steril',
        'pembersihan_kulit',
        'setelah_melakukan_hand_hygiene',
        'perawatan_dressing_infus',
        'spoit_yang_digunakan_disposible',
        'memberi_tanggal_dan_jam_pemasangan_infus',
        'set_infus_setiap_72jam',
        'no_rawat',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'tanggal' => Carbon::now()->format('Y-m-d H:i:s'),
        ], true);
        parent::__construct($attributes);
    }

    static function rataTtlNilai(string $month, string $year)
    {
        $data = self::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->selectRaw('CONCAT(ROUND(((sebelum_melakukan_hand_hygiene = "Ya") + (menggunakan_apd_lengkap = "Ya") + (lokasi_pemasangan_sesuai = "Ya") + (alat_yang_digunakan_steril = "Ya") + (pembersihan_kulit = "Ya") + (setelah_melakukan_hand_hygiene = "Ya") + (perawatan_dressing_infus = "Ya") + (spoit_yang_digunakan_disposible = "Ya") + (memberi_tanggal_dan_jam_pemasangan_infus = "Ya") + (set_infus_setiap_72jam = "Ya")) / 10 * 100, 2)) as ttl')
            ->get();
        return $data->avg('ttl');
    }

    public function ruangAuditKepatuhan()
    {
        return $this->belongsTo(RuangAuditKepatuhan::class, 'id_ruang', 'id_ruang');
    }
}
