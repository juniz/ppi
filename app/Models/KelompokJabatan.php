<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokJabatan extends Model
{
    use HasFactory;

    protected $table = 'kelompok_jabatan';
    protected $primaryKey = 'kode_kelompok';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'kode_kelompok',
        'nama_kelompok',
        'index',
    ];
}
