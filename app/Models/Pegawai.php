<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';
    protected $primaryKey = 'nik';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nik',
        'nama',
        'jk',
        'jbtn',
        'jnj_jabatan',
        'kode_kelompok',
        'kode_resiko',
        'kode_emergency',
        'departemen',
        'bidang',
        'stts_wp',
        'stts_kerja',
        'npwp',
        'pendidikan',
        'gapok',
        'tmp_lahir',
        'tgl_lahir',
        'alamat',
        'kota',
        'mulai_kerja',
        'indexins',
        'bpd',
        'rekening',
        'stts_aktif',
        'wajibmasuk',
        'pengurang',
        'indek',
        'mulai_kontrak',
        'cuti_diambil',
        'dankes',
        'photo',
        'no_ktp',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'mulai_kerja' => 'date',
        'mulai_kontrak' => 'date',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'jk' => 'Pria',
            'jbtn' => '-',
            'jnj_jabatan' => '-',
            'kode_kelompok' => '-',
            'kode_resiko' => '-',
            'kode_emergency' => '-',
            'departemen' => '-',
            'bidang' => '-',
            'stts_wp' => '-',
            'stts_kerja' => '-',
            'npwp' => '',
            'pendidikan' => '-',
            'gapok' => '',
            'tmp_lahir' => '',
            'tgl_lahir' => '0000-00-00',
            'alamat' => '',
            'kota' => '',
            'mulai_kerja' => Carbon::now()->format('Y-m-d'),
            'ms_kerja' => '<1',
            'indexins' => '-',
            'bpd' => 'BPD',
            'rekening' => '',
            'stts_aktif' => 'AKTIF',
            'wajibmasuk' => '0',
            'pengurang' => '0',
            'indek' => '0',
            'mulai_kontrak' => Carbon::now()->format('Y-m-d'),
            'cuti_diambil' => '0',
            'dankes' => '0',
            'photo' => '',
        ], true);
        parent::__construct($attributes);
    }

    public function jnjJabatan()
    {
        return $this->belongsTo(JnjJabatan::class, 'jnj_jabatan', 'kode');
    }

    public function kelompokJabatan()
    {
        return $this->belongsTo(KelompokJabatan::class, 'kode_kelompok', 'kode_kelompok');
    }

    public function resikoKerja()
    {
        return $this->belongsTo(ResikoKerja::class, 'kode_resiko', 'kode_resiko');
    }

    public function emergencyIndex()
    {
        return $this->belongsTo(EmergencyIndex::class, 'kode_emergency', 'kode_emergency');
    }

    public function getDepartemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen', 'dep_id');
    }

    public function getBidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang', 'nama');
    }

    public function sttsWp()
    {
        return $this->belongsTo(SttsWp::class, 'stts_wp', 'stts');
    }

    public function sttsKerja()
    {
        return $this->belongsTo(SttsKerja::class, 'stts_kerja', 'stts');
    }

    public function getPendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan', 'tingkat');
    }

    public function indexIns()
    {
        return $this->belongsTo(Indexins::class, 'indexins', 'dep_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bpd', 'namabank');
    }

    public function dokter(): HasOne
    {
        return $this->hasOne(Dokter::class, 'kd_dokter', 'nik');
    }
}
