<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahasaPasien extends Model
{
    use HasFactory;

    protected $table = 'bahasa_pasien';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
        'id',
        'nama_bahasa'
    ];
}
