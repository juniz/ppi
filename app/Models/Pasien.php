<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';
    protected $primaryKey = 'no_rkm_medis';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rkm_medis',
        'no_ktp',
        'nm_pasien',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'nm_ibu',
        'alamat',
        'gol_darah',
        'pekerjaan',
        'stts_nikah',
        'agama',
        'tgl_daftar',
        'no_tlp',
        'umur',
        'pnd',
        'keluarga',
        'namakeluarga',
        'kd_pj',
        'no_peserta',
        'kd_kel',
        'kd_kec',
        'kd_kab',
        'pekerjaanpj',
        'alamatpj',
        'kelurahanpj',
        'kecamatanpj',
        'kabupatenpj',
        'perusahaan_pasien',
        'suku_bangsa',
        'bahasa_pasien',
        'cacat_fisik',
        'email',
        'nip',
        'kd_prop',
        'propinsipj',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'no_ktp' => '',
            'tmp_lahir' => '',
            'nm_ibu' => '',
            'gol_darah' => '-',
            'pekerjaan' => '',
            'stts_nikah' => 'BELUM NIKAH',
            'agama' => 'ISLAM',
            'tgl_daftar' => Carbon::now()->format('Y-m-d'),
            'no_tlp' => '',
            'pnd' => '-',
            'keluarga' => 'LAIN-LAIN',
            'namakeluarga' => '',
            'kd_pj' => '-',
            'no_peserta' => '',
            'kd_kel' => '1',
            'kd_kec' => '1',
            'kd_kab' => '1',
            'pekerjaanpj' => '',
            'alamatpj' => '',
            'kelurahanpj' => '1',
            'kecamatanpj' => '1',
            'kabupatenpj' => '1',
            'perusahaan_pasien' => '-',
            'suku_bangsa' => '1',
            'bahasa_pasien' => '1',
            'cacat_fisik' => '1',
            'email' => '',
            'nip' => '',
            'kd_prop' => '1',
            'propinsipj' => '1',
        ], true);
        parent::__construct($attributes);
    }

    public static function calculateAge($tgl_lahir)
    {
        $birthDate = \Carbon\Carbon::parse($tgl_lahir);
        $age = $birthDate->age;
        $month = $birthDate->month;
        $day = $birthDate->day;
        return $age . ' Th ' . $month . ' Bl ' . $day . ' Hr';
    }

    public static function generateNoRm()
    {
        $lastNoRm = self::max('no_rkm_medis');
        // $lastNoRm = (int) substr($lastNoRm, 2);
        $lastNoRm++;
        $newNoRm = sprintf('%06s', $lastNoRm);
        return $newNoRm;
    }

    public function getJkAttribute($value)
    {
        return $value == 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getTglLahirAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function penjab()
    {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kd_kel', 'kd_kel');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kd_kec', 'kd_kec');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kd_kab', 'kd_kab');
    }

    public function propinsi()
    {
        return $this->belongsTo(Propinsi::class, 'kd_prop', 'kd_prop');
    }

    public function reg_periksa()
    {
        return $this->hasMany(RegPeriksa::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function perusahaan()
    {
        return $this->belongsTo(PerusahaanPasien::class, 'perusahaan_pasien', 'kode_perusahaan');
    }

    public function suku_bangsa()
    {
        return $this->belongsTo(SukuBangsa::class, 'suku_bangsa', 'id');
    }

    public function bahasa_pasien()
    {
        return $this->belongsTo(BahasaPasien::class, 'bahasa_pasien', 'id');
    }

    public function cacat_fisik()
    {
        return $this->belongsTo(CacatFisik::class, 'cacat_fisik', 'id');
    }
}
