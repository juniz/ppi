<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JnjJabatan extends Model
{
    use HasFactory;

    protected $table = 'jnj_jabatan';
    protected $primaryKey = 'kode';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'kode',
        'nama',
        'tnj',
        'index',
    ];
}
