<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'bank';
    protected $primaryKey = 'namabank';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'namabank',
    ];
}
