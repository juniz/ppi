<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $table = 'bidang';
    protected $primaryKey = 'nama';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'nama',
    ];
}
