<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SttsKerja extends Model
{
    use HasFactory;

    protected $table = 'stts_kerja';
    protected $primaryKey = 'stts';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'stts',
        'ktg',
        'index',
    ];
}
