<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';
    protected $primaryKey = 'dep_id';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'dep_id',
        'nama',
    ];
}
