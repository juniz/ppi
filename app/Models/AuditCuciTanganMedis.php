<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditCuciTanganMedis extends Model
{
    use HasFactory;

    protected $table = 'audit_cuci_tangan_medis';
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nik',
        'tanggal',
        'sebelum_menyentuh_pasien',
        'sebelum_tehnik_aseptik',
        'setelah_terpapar_cairan_tubuh_pasien',
        'setelah_kontak_dengan_pasien',
        'setelah_kontak_dengan_lingkungan_pasien',
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
