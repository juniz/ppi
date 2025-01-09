<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditBundleIadp extends Model
{
    use HasFactory;

    protected $table = 'audit_bundle_iadp';
    protected $primaryKey = 'tanggal';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'tanggal',
        'nik',
        'handhygiene',
        'apd',
        'skin_antiseptik',
        'lokasi_iv',
        'perawatan_rutin',
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
            ->selectRaw('CONCAT(ROUND(((handhygiene = "Ya") + (apd = "Ya") + (skin_antiseptik = "Ya") + (lokasi_iv = "Ya") + (perawatan_rutin = "Ya")) / 5 * 100, 2)) as ttl')
            ->get();
        return $data->avg('ttl');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }
}
