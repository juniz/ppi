<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SukuBangsa extends Model
{
    use HasFactory;

    protected $table = 'suku_bangsa';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
        'id',
        'nama_suku_bangsa'
    ];
}
