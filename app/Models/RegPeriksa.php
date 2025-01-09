<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegPeriksa extends Model
{
    use HasFactory;

    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'no_reg',
        'no_rawat',
        'tgl_registrasi',
        'jam_reg',
        'kd_dokter',
        'no_rkm_medis',
        'kd_poli',
        'p_jawab',
        'almt_pj',
        'hubunganpj',
        'biaya_reg',
        'stts',
        'stts_daftar',
        'status_lanjut',
        'kd_pj',
        'umurdaftar',
        'sttsumur',
        'status_bayar',
        'status_poli',
    ];

    public static function generateNoReg($kd_dokter, $kd_poli)
    {
        $lastNoReg = self::where('tgl_registrasi', date('Y-m-d'))->where('kd_dokter', $kd_dokter)->where('kd_poli', $kd_poli)->max('no_reg');
        return str_pad($lastNoReg + 1, 3, '0', STR_PAD_LEFT);
    }

    public static function generateNoRawat()
    {
        $lastNoRawat = self::where('tgl_registrasi', date('Y-m-d'))->selectRaw("ifnull(MAX(CONVERT(RIGHT(reg_periksa.no_rawat,6),signed)),0) as no")->first()->no;
        return date('Y/m/d') . '/' . str_pad($lastNoRawat + 1, 6, '0', STR_PAD_LEFT);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    public function penjab()
    {
        return $this->belongsTo(Penjab::class, 'kd_pj', 'kd_pj');
    }

    public function poliklinik()
    {
        return $this->belongsTo(Poliklinik::class, 'kd_poli', 'kd_poli');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    public function kamarInap()
    {
        return $this->hasOne(KamarInap::class, 'no_rawat', 'no_rawat');
    }
}
