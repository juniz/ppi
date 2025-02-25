<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'dokter';
    protected $primaryKey = 'kd_dokter';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_dokter',
        'nm_dokter',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_drh',
        'agama',
        'almt_tgl',
        'no_telp',
        'stts_nikah',
        'kd_sps',
        'alumni',
        'no_ijn_praktek',
        'status'
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($dokter) {
            // Set status default ke 1
            $dokter->status = '1';

            // Cek apakah pegawai dengan NIK tersebut sudah ada
            $pegawai = Pegawai::find($dokter->kd_dokter);
            
            if (!$pegawai) {
                // Jika belum ada, buat data pegawai baru
                Pegawai::create([
                    'nik' => $dokter->kd_dokter ?? '-',
                    'nama' => $dokter->nm_dokter ?? '-',
                    'jk' => $dokter->jk == 'L' ? 'Pria' : 'Wanita',
                    'jbtn' => '-',
                    'jnj_jabatan' => '-',
                    'kode_kelompok' => '-',
                    'kode_resiko' => '-',
                    'kode_emergency' => '-',
                    'departemen' => '-',
                    'bidang' => '-',
                    'stts_wp' => '-',
                    'stts_kerja' => '-',
                    'npwp' => '-',
                    'pendidikan' => '-',
                    'gapok' => 0,
                    'tmp_lahir' => $dokter->tmp_lahir ?? '-',
                    'tgl_lahir' => $dokter->tgl_lahir ?? '2021-09-22',
                    'alamat' => $dokter->almt_tgl ?? '-',
                    'kota' => '-',
                    'mulai_kerja' => '1900-01-01',
                    'ms_kerja' => '<1',
                    'indexins' => '-',
                    'bpd' => 'T',
                    'rekening' => '-',
                    'stts_aktif' => 'AKTIF',
                    'wajibmasuk' => 0,
                    'pengurang' => 0,
                    'indek' => 0,
                    'mulai_kontrak' => '1900-01-01',
                    'cuti_diambil' => 0,
                    'dankes' => 0,
                    'photo' => 'pages/pegawai/photo/',
                    'no_ktp' => '-'
                ]);
            }
        });
    }

    public function spesialis(): BelongsTo
    {
        return $this->belongsTo(Spesialis::class, 'kd_sps', 'kd_sps');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'kd_dokter', 'nik');
    }
}
