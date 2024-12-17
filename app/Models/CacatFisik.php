<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacatFisik extends Model
{
    use HasFactory;

    protected $table = 'cacat_fisik';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
        'id',
        'nama_cacat'
    ];
}
