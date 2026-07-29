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
        'no_rawat',
        'pemasangan_1_indikasi',
        'pemasangan_2_hand_hygiene',
        'pemasangan_3_teknik_aseptik',
        'pemasangan_4_alat_steril',
        'perawatan_1_hand_hygiene',
        'perawatan_2_genitalia_dibersihkan',
        'perawatan_3_fiksasi_kateter',
        'perawatan_4_tidak_diganti_rutin',
        'perawatan_5_aliran_steril_tertutup',
        'perawatan_6_hubungan_kateter_tertutup',
        'perawatan_7_urine_bag_tidak_di_lantai',
        'perawatan_8_selang_tidak_terlipat',
        'perawatan_10_segera_dilepas',
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
            ->selectRaw('CONCAT(ROUND( ((pemasangan_1_indikasi = "Ya") + (pemasangan_2_hand_hygiene = "Ya") + (pemasangan_3_teknik_aseptik = "Ya") + (pemasangan_4_alat_steril = "Ya") + (perawatan_1_hand_hygiene = "Ya") + (perawatan_2_genitalia_dibersihkan = "Ya") + (perawatan_3_fiksasi_kateter = "Ya") + (perawatan_4_tidak_diganti_rutin = "Ya") + (perawatan_5_aliran_steril_tertutup = "Ya") + (perawatan_6_hubungan_kateter_tertutup = "Ya") + (perawatan_7_urine_bag_tidak_di_lantai = "Ya") + (perawatan_8_selang_tidak_terlipat = "Ya") + (perawatan_10_segera_dilepas = "Ya")) / NULLIF( ((pemasangan_1_indikasi != "NA") + (pemasangan_2_hand_hygiene != "NA") + (pemasangan_3_teknik_aseptik != "NA") + (pemasangan_4_alat_steril != "NA") + (perawatan_1_hand_hygiene != "NA") + (perawatan_2_genitalia_dibersihkan != "NA") + (perawatan_3_fiksasi_kateter != "NA") + (perawatan_4_tidak_diganti_rutin != "NA") + (perawatan_5_aliran_steril_tertutup != "NA") + (perawatan_6_hubungan_kateter_tertutup != "NA") + (perawatan_7_urine_bag_tidak_di_lantai != "NA") + (perawatan_8_selang_tidak_terlipat != "NA") + (perawatan_10_segera_dilepas != "NA")), 0) * 100, 2)) as ttl')
            ->get();
        return $data->avg('ttl');
    }

    public function ruangAuditKepatuhan()
    {
        return $this->belongsTo(RuangAuditKepatuhan::class, 'id_ruang', 'id_ruang');
    }
}
