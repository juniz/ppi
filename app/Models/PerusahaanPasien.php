<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerusahaanPasien extends Model
{
    use HasFactory;

    protected $table = 'perusahaan_pasien';
    protected $primaryKey = 'kode_perusahaan';
    public $timestamps = false;
}
