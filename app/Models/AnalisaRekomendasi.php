<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalisaRekomendasi extends Model
{
    protected $table = 'analisa_rekomendasi_hais';

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'ruangan',
        'analisa',
        'rekomendasi',
        'data_hap',
        'data_iad',
        'data_ilo',
        'data_isk',
        'data_plebitis',
        'data_vap',
        'summary_laju',
        // Summary fields
        'total_hap_kasus',
        'total_hap_hari_rawat',
        'rata_hap_laju',
        'total_iad_kasus',
        'total_iad_hari_terpasang',
        'rata_iad_laju',
        'total_ilo_kasus',
        'total_ilo_hari_operasi',
        'rata_ilo_laju',
        'total_isk_kasus',
        'total_isk_hari_kateter',
        'rata_isk_laju',
        'total_plebitis_kasus',
        'total_plebitis_hari_infus',
        'rata_plebitis_laju',
        'total_vap_kasus',
        'total_vap_hari_ventilator',
        'rata_vap_laju',
        // Chart images
        'chart_infeksi_image',
        'chart_pemasangan_image',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'data_hap' => 'array',
        'data_iad' => 'array',
        'data_ilo' => 'array',
        'data_isk' => 'array',
        'data_plebitis' => 'array',
        'data_vap' => 'array',
        'summary_laju' => 'array',
    ];

    public function bangsal(): BelongsTo
    {
        return $this->belongsTo(Bangsal::class, 'ruangan', 'kd_bangsal');
    }
}