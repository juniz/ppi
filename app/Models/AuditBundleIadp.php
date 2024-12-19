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
