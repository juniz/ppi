<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KamarInap extends Model
{
    use HasFactory;

    protected $table = 'kamar_inap';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'kd_kamar',
        'trf_kamar',
        'diagnosa_awal',
        'diagnosa_akhir',
        'tgl_masuk',
        'jam_masuk',
        'tgl_keluar',
        'jam_keluar',
        'lama',
        'ttl_biaya',
        'stts_pulang',
    ];

    protected function setKeysForSaveQuery($query)
    {
        $query->where('no_rawat', $this->getAttribute('no_rawat'))
              ->where('tgl_masuk', $this->getAttribute('tgl_masuk'))
              ->where('jam_masuk', $this->getAttribute('jam_masuk'));
        
        return $query;
    }

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'stts_pulang' => '-',
        ], true);
        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Cek apakah pasien dengan No. RM yang sama masih dalam rawat inap
            $existingInap = static::query()
                ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
                ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
                ->where('reg_periksa.no_rkm_medis', function($query) use ($model) {
                    $query->select('no_rkm_medis')
                        ->from('reg_periksa')
                        ->where('no_rawat', $model->no_rawat)
                        ->first();
                })
                ->where('kamar_inap.stts_pulang', '-')
                ->first();

            if ($existingInap) {
                throw new \Exception("Pasien dengan No. RM ini masih dalam perawatan di ruang {$existingInap->nm_bangsal}");
            }
        });
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kd_kamar', 'kd_kamar');
    }

    public function scopeWithCompositeKey($query, $no_rawat, $tgl_masuk, $jam_masuk)
    {
        return $query->where('no_rawat', $no_rawat)
                    ->where('tgl_masuk', $tgl_masuk)
                    ->where('jam_masuk', $jam_masuk);
    }
}
