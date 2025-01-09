<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditBundleIsk extends Model
{
    use HasFactory;

    protected $table = 'audit_bundle_isk';
    protected $primaryKey = 'tanggal';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'pemasangan_sesuai_indikasi',
        'hand_hygiene',
        'menggunakan_apd_yang_tepat',
        'pemasangan_menggunakan_alat_steril',
        'segera_dilepas_setelah_tidak_diperlukan',
        'pengisian_balon_sesuai_petunjuk',
        'fiksasi_kateter_dengan_plester',
        'urinebag_menggantung_tidak_menyentuh_lantai',
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
            ->selectRaw('CONCAT(ROUND(((pemasangan_sesuai_indikasi = "Ya") + (hand_hygiene = "Ya") + (menggunakan_apd_yang_tepat = "Ya") + (pemasangan_menggunakan_alat_steril = "Ya") + (segera_dilepas_setelah_tidak_diperlukan = "Ya") + (pengisian_balon_sesuai_petunjuk = "Ya") + (fiksasi_kateter_dengan_plester = "Ya") + (urinebag_menggantung_tidak_menyentuh_lantai = "Ya")) / 8 * 100, 2)) as ttl')
            ->get();
        return $data->avg('ttl');
    }

    public function ruangAuditKepatuhan()
    {
        return $this->belongsTo(RuangAuditKepatuhan::class, 'id_ruang', 'id_ruang');
    }
}
