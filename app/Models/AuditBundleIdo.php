<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditBundleIdo extends Model
{
    use HasFactory;

    protected $table = 'audit_bundle_ido';
    protected $primaryKey = ['tanggal', 'id_ruang'];
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'pencukuran_rambut',
        'antibiotik',
        'temperature',
        'sugar',
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
            ->selectRaw('CONCAT(ROUND(((pencukuran_rambut = "Ya") + (antibiotik = "Ya") + (temperature = "Ya") + (sugar = "Ya")) / 4 * 100, 2)) as ttl')
            ->get();
        return $data->avg('ttl');
    }

    public function ruangAuditKepatuhan()
    {
        return $this->belongsTo(RuangAuditKepatuhan::class, 'id_ruang', 'id_ruang');
    }
}
